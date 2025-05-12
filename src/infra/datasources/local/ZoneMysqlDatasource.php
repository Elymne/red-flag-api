<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Zone;
use Domain\Repositories\LocalZoneRepository;

class ZoneMysqlDatasource implements LocalZoneRepository
{
    private DBConnect $_db;

    public function __construct(DBConnect $db)
    {
        $this->_db = $db;
    }

    public function findMany(string|null $name = null, string|null $id = null): array
    {
        // Prapare the statement.
        /** @var string */
        $query = "SELECT id, name FROM zone WHERE 1=1";
        $params = [];
        if (!is_null($id)) {
            $query .= " AND id = ?";
            $params[] = $id;
        }
        if (!is_null($name)) {
            $query .= " AND name = ?";
            $params[] = $name;
        }
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject the value.
        if (!empty($params)) {
            $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        }
        // Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();
        // Parse raw zones.
        /** @var Zone[] */
        $zones = [];
        while ($row = $result->fetch_assoc()) {
            array_push($zones, new Zone(
                id: $row["id"],
                name: $row["name"],
            ));
        }
        // Return zones.
        return $zones;
    }

    public function findUnique(string $id): Zone|null
    {
        // Prepare statement.
        /** @var string */
        $query = "SELECT id, name FROM zone WHERE id = ?";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $stmt->bind_param("s", $id);
        // Run SQL Command and fetch result.
        $stmt->execute();
        $result = $stmt->get_result();
        // Parse zones.
        /** @var Zone[] */
        $zones = [];
        while ($row = $result->fetch_assoc()) {
            array_push($zones, new Zone(
                id: $row["id"],
                name: $row["name"],
            ));
        }
        // Check if data exists, return null when it's not the case.
        if (count($zones) == 0) {
            return null;
        }
        //Return the first value.
        return $zones[0];
    }

    public function createOne(Zone $zone): void
    {
        // Prepare statement.
        /** @var string */
        $query = "INSERT INTO zone (id, name) VALUES (?, ?)";
        $stmt = $this->_db->getMysqli()->prepare($query);
        // Inject values.
        $zoneID = $zone->id;
        $zoneName = $zone->name;
        $stmt->bind_param("ss", $zoneID, $zoneName);
        // Run SQL Command and fetch result.
        $stmt->execute();
    }
}
