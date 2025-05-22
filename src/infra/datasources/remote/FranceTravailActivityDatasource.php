<?php

declare(strict_types=1);

namespace Infra\Datasources;

use Domain\Models\Activity;
use Domain\Repositories\RemoteActivityRepository;

class FranceTravailActivityDatasource implements RemoteActivityRepository
{
    public function findAll(): array
    {
        // * Generate token.
        $token = FranceTravailToken::generate();

        // * Prepare Request.
        $ch = curl_init("https://api.francetravail.io/partenaire/rome-metiers/v1/metiers/appellation");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Bearer " . $token,
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode json data.
        $rawActivities = json_decode($response, true);

        /** @var Activity[] */
        $activities = [];

        // * Parse data.
        foreach ($rawActivities as $activityData) {
            array_push($activities, new Activity(
                ID: $activityData["code"],
                name: $activityData["libelle"]
            ));
        }

        // * Return my decoded data.
        return $activities;
    }

    public function findMany(string $name): array
    {
        // * Generate token.
        $token = FranceTravailToken::generate();

        // * Prepare Request.
        $ch = curl_init("https://api.francetravail.io/partenaire/rome-metiers/v1/metiers/metier/requete?q=" . $name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Bearer " . $token,
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode json data.
        $rawActivities = json_decode($response, true)["resultats"];

        /** @var Activity[] */
        $activities = [];

        // * Parse data.
        foreach ($rawActivities as $activityData) {
            array_push($activities, new Activity(
                ID: $activityData["code"],
                name: $activityData["libelle"]
            ));
        }

        // * Return my decoded data.
        return $activities;
    }

    public function findUnique(string $ID): Activity|null
    {
        // * Generate token.
        $token = FranceTravailToken::generate();

        // * Prepare Request.
        $ch = curl_init("https://api.francetravail.io/partenaire/rome-metiers/v1/metiers/appellation/" . $ID);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Bearer " . $token,
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Send request and close curl prog.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode json data.
        $rawActivities = json_decode($response, true);

        // * Parse and return.
        return new Activity(
            ID: strval($rawActivities["code"]),
            name: $rawActivities["libelle"]
        );
    }
}
