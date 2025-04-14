<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Domain\Models\PersonDetailed;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Domain\Usecases\FindUniquePerson;
use Domain\Usecases\FindUniquePersonParams;

class FindUniquePersonTest extends TestCase
{
    private $_localPersonRepository;

    protected function setUp(): void
    {
        $this->_localPersonRepository = Mockery::mock(LocalPersonRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_findNull(): void
    {
        $uuid = "01";

        $this->_localPersonRepository->allows()->findUnique($uuid)->andReturns(null);
        $findUniquePerson = new FindUniquePerson($this->_localPersonRepository);

        $result = $findUniquePerson->perform(new FindUniquePersonParams(id: $uuid));

        $this->assertSame($result->code, 404);
        $this->assertSame($result->data, "Action failure : this person does not exists.");
    }

    public function test_findOne(): void
    {
        $uuid = "01";
        $data = new PersonDetailed(
            id: $uuid,
            firstName: "f",
            lastName: "l",
            zone: new Zone(id: "01", name: "c"),
            createdAt: 0,
            messages: [],
            links: [],
        );

        $this->_localPersonRepository->allows()->findUnique($uuid)->andReturns($data);
        $findUniquePerson = new FindUniquePerson($this->_localPersonRepository);

        $result = $findUniquePerson->perform(new FindUniquePersonParams(id: $uuid));

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_findWithWrongParams(): void
    {
        $uuid = "01";
        $this->_localPersonRepository->allows()->findUnique($uuid)->andReturns(null);
        $findUniquePerson = new FindUniquePerson($this->_localPersonRepository);

        $result = $findUniquePerson->perform("String params");

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a FindUniquePersonParams structure.");
    }

    public function test_findButExceptionFromRepo(): void
    {
        $uuid = "01";
        $this->_localPersonRepository->allows()->findUnique($uuid)->andThrow(new \Exception("Repository exception"));
        $findUniquePerson = new FindUniquePerson($this->_localPersonRepository);

        $result = $findUniquePerson->perform(new FindUniquePersonParams(id: $uuid));

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
