<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Repositories\LocalDomainRepository;
use mysqli_result;

class DomainMysqlDatasource implements LocalDomainRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findAll(): array
    {
        // * Prepare statement.
        /** @var string */
        $query = "SELECT value FROM domain WHERE value = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject values.
        $stmt->bind_param("s", $value);

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();

        // * Parse zones.
        /** @var string[] */
        $domains = $this->_parse($result);

        // * Check if data exists, return null when it's not the case.
        return $domains;
    }

    public function doesExists(string $domainName): bool
    {
        // * Prepare statement.
        /** @var string */
        $query = "SELECT value FROM domain WHERE value = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);

        // * Inject values.
        $stmt->bind_param("s", $value);

        // * Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();

        // * Parse zones.
        /** @var string[] */
        $domains = $this->_parse($result);

        // * Check if data exists, return null when it's not the case.
        return count($domains) > 0;
    }

    /**
     * @param mysqli_result $result
     * @return string[]
     */
    private function _parse(mysqli_result $result): array
    {
        $domains = [];
        while ($row = $result->fetch_assoc()) {
            array_push($domains, $row["value"]);
        }
        return $domains;
    }
}
