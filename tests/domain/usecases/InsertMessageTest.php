<?php

declare(strict_types=1);

use Domain\Models\Message;
use PHPUnit\Framework\TestCase;
use Domain\Models\PersonDetailed;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\UuidRepository;
use Domain\Usecases\InsertMessage;
use Domain\Usecases\InsertMessageParams;

class InsertMessageTest extends TestCase
{
    private $_uuidRepository;
    private $_localPersonRepository;

    protected function setUp(): void
    {
        $this->_uuidRepository = Mockery::mock(UuidRepository::class);
        $this->_localPersonRepository = Mockery::mock(LocalPersonRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_wrongParams(): void
    {
        $InsertMessage = new InsertMessage($this->_uuidRepository, $this->_localPersonRepository);
        $result = $InsertMessage->perform("");

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
    }

    public function test_unknownPerson(): void
    {
        $personID = "01";
        $messageValue = "Fou du bus.";
        $params = new InsertMessageParams(personID: $personID, message: $messageValue);

        $this->_localPersonRepository->allows()->findUnique($personID)->andReturns(null);
        $InsertMessage = new InsertMessage($this->_uuidRepository, $this->_localPersonRepository);
        $result = $InsertMessage->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the person does not exists.");
    }

    public function test_addLink(): void
    {
        $personID = "01";
        $message = new Message(
            id: "01",
            value: "Petit message",
            createdAt: 0
        );
        $person = new PersonDetailed(
            id: $personID,
            firstName: "f",
            lastName: "l",
            zone: new Zone(id: "01", name: "c"),
            createdAt: 0,
            messages: [],
            links: [],
        );
        $params = new InsertMessageParams(personID: $personID, message: $message->value);

        $this->_localPersonRepository->allows()->findUnique($personID)->andReturns($person);
        $this->_uuidRepository->allows()->generate()->andReturns($message->id);
        // Here, arg is relative to time, it mean when cannot predict this value.
        $this->_localPersonRepository->shouldReceive("addMessage")->andReturns(null);

        $InsertMessage = new InsertMessage($this->_uuidRepository, $this->_localPersonRepository);
        $result = $InsertMessage->perform($params);

        $this->assertSame($result->code, 201);
        $this->assertSame($result->data, "Action success : new entry in message database.");
    }

    public function test_exception(): void
    {
        $personID = "01";
        $messageValue = "Fou du bus.";
        $params = new InsertMessageParams(personID: $personID, message: $messageValue);

        $this->_localPersonRepository->allows()->findUnique($personID)->andThrow(new \Exception("Repository exception"));
        $InsertMessage = new InsertMessage($this->_uuidRepository, $this->_localPersonRepository);
        $result = $InsertMessage->perform($params);

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
