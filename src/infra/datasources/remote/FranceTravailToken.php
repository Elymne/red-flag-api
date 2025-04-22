<?php

declare(strict_types=1);

namespace Infra\Datasources;

class FranceTravailToken
{
    public static function generate(): string
    {
        //* Prepare request.
        $ch = curl_init("https://francetravail.io/connexion/oauth2/access_token?realm=partenaire");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query([
                "grant_type" => "client_credentials",
                "scope" => "api_rome-metiersv1 nomenclatureRome api_eterritoirev1",
                "client_id" => $_ENV["FRANCE_TRAVAIL_API_ID"],
                "client_secret" => $_ENV["FRANCE_TRAVAIL_API_KEY"],
            ])
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-Type: application/x-www-form-urlencoded",
        ]);

        // * On dev env, we remove SSL checker.
        if ($_ENV["MODE"] == "develop") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // * Run request.
        $response = curl_exec($ch);
        curl_close($ch);

        // * Decode response and return.
        $data = json_decode($response, true);

        // * Return the token.
        return $data["access_token"];
    }
}
