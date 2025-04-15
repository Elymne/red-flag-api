<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Domain\Models\Person;
use Domain\Models\Zone;
use Domain\Repositories\LocalPersonRepository;
use Domain\Usecases\FindPersons;
use Domain\Usecases\FindPersonsParams;

class FindPersonsTest extends TestCase
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

    public function test_findEmpty(): void
    {
        $this->_localPersonRepository->allows()->findMany("Someone", null, null, null)->andReturns([]);
        $findPersons = new FindPersons($this->_localPersonRepository);

        $result = $findPersons->perform(new FindPersonsParams(firstname: "Someone"));

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, []);
    }

    public function test_findMany(): void
    {
        $data = [
            new Person(id: "01", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "02", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "03", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "04", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
        ];

        $this->_localPersonRepository->allows()->findMany("anyone", null, null, null)->andReturns($data);
        $findPersons = new FindPersons($this->_localPersonRepository);

        $result = $findPersons->perform(new FindPersonsParams(firstname: "anyone"));

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, $data);
    }

    public function test_noParams(): void
    {
        $this->_localPersonRepository->allows()->findMany(null, null, null, null)->andReturns([]);
        $findPersons = new FindPersons($this->_localPersonRepository);

        $result = $findPersons->perform(new FindPersonsParams());

        $this->assertSame($result->code, 200);
        $this->assertSame($result->data, []);
    }

    public function test_wrongParams(): void
    {
        $data = [
            new Person(id: "01", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "02", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "03", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
            new Person(id: "04", firstName: "a", lastName: "j", jobName: "t", zone: new Zone(id: "01", name: "Somewhere"), createdAt: 0),
        ];

        $this->_localPersonRepository->allows()->findMany(null, null, null, null)->andReturns($data);
        $findPersons = new FindPersons($this->_localPersonRepository);

        $result = $findPersons->perform("Wrong params");

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a FindPersonsParams structure.");
    }

    public function test_Exception(): void
    {
        $this->_localPersonRepository->allows()->findMany(null, null, null, null)->andThrow(new \Exception("Repository exception"));
        $findPersons = new FindPersons($this->_localPersonRepository);

        $result = $findPersons->perform(new FindPersonsParams(firstname: "Someone"));


        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
