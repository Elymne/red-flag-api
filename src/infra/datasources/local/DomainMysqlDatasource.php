<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Repositories\LocalDomainRepository;

class DomainMysqlDatasource implements LocalDomainRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findUnique(string $value): string|null
    {
        // Prepare statement.
        /** @var string */
        $query = "SELECT value FROM domain WHERE value = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("s", $value);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();
        // Parse zones.
        /** @var string[] */
        $domains = [];
        while ($row = $result->fetch_assoc()) {
            array_push($domains, $row["value"]);
        }
        // Check if data exists, return null when it's not the case.
        if (count($domains) == 0) {
            return null;
        }
        //Return the first value.
        return $domains[0];
    }
}
