<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Domain\Models\Link;
use Domain\Models\PersonDetailed;
use Domain\Models\Zone;
use Domain\Repositories\LocalDomainRepository;
use Domain\Repositories\LocalPersonRepository;
use Domain\Repositories\UuidRepository;
use Domain\Usecases\insertLink;
use Domain\Usecases\insertLinkParams;

class InsertLinkTest extends TestCase
{
    private $_uuidRepository;
    private $_localPersonRepository;
    private $_localDomainRepository;

    protected function setUp(): void
    {
        $this->_uuidRepository = Mockery::mock(UuidRepository::class);
        $this->_localPersonRepository = Mockery::mock(LocalPersonRepository::class);
        $this->_localDomainRepository = Mockery::mock(LocalDomainRepository::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_wrongParams(): void
    {
        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform("");

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the data send from body is not correct. Should be a InsertMessageParams structure.");
    }

    public function test_wrongHttpsLink(): void
    {

        $personID = "01";
        $link = "htptp:://somewhere/heree";
        $params = new insertLinkParams(personID: $personID, link: $link);

        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the provided link is not a valid HTTPS URL.");
    }

    public function test_unvalidDomainName(): void
    {
        $personID = "01";
        $link = "https://somemedia.bidule.fr/link-to-article";
        $params = new insertLinkParams(personID: $personID, link: $link);

        $this->_localDomainRepository->allows()->findUnique("somemedia.bidule.fr")->andReturns(null);

        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the domain name is not accepted.");
    }

    public function test_unknownPerson(): void
    {
        $personID = "01";
        $link = "https://somemedia.bidule.fr/link-to-article";
        $params = new insertLinkParams(personID: $personID, link: $link);

        $this->_localDomainRepository->allows()->findUnique("somemedia.bidule.fr")->andReturns("somemedia.bidule.fr");
        $this->_localPersonRepository->allows()->findUnique($personID)->andReturns(null);

        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform($params);

        $this->assertSame($result->code, 400);
        $this->assertSame($result->data, "Action failure : the person does not exists.");
    }

    public function test_addLink(): void
    {
        $linkID = "01";
        $link = new Link(
            id: $linkID,
            value: "https://somemedia.bidule.fr/link-to-article",
            createdAt: 0
        );

        $personID = "01";
        $person = new PersonDetailed(
            id: $personID,
            firstName: "f",
            lastName: "l",
            jobName: "t",
            zone: new Zone(id: "01", name: "c"),
            createdAt: 0,
            messages: [],
            links: [],
        );

        $params = new insertLinkParams(personID: $personID, link: $link->value);

        $this->_localDomainRepository->allows()->findUnique("somemedia.bidule.fr")->andReturns("somemedia.bidule.fr");
        $this->_localPersonRepository->allows()->findUnique($personID)->andReturns($person);
        $this->_uuidRepository->allows()->generate()->andReturns($linkID);
        // Here, arg is relative to time, it mean when cannot predict this value.
        $this->_localPersonRepository->shouldReceive("addLink")->andReturns(null);

        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform($params);

        $this->assertSame($result->code, 201);
        $this->assertSame($result->data, "Action success : new entry in message database.");
    }

    public function test_exception(): void
    {
        $uuid = "01";
        $link = "https://somemedia.bidule.fr/link-to-article";

        $this->_localDomainRepository->allows()->findUnique("somemedia.bidule.fr")->andThrow(new \Exception("Repository exception"));
        $this->_localPersonRepository->allows()->findUnique($uuid)->andThrow(new \Exception("Repository exception"));
        $insertLink = new InsertLink($this->_uuidRepository, $this->_localPersonRepository, $this->_localDomainRepository);
        $result = $insertLink->perform(new insertLinkParams(personID: $uuid, link: $link));

        $this->assertSame($result->code, 500);
        $this->assertSame($result->data, "Action failure : Internal Server Error.");
    }
}
