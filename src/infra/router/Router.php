<?php

namespace Infra;

use Core\CValues;
use Core\Log;

class Router
{
    private array $end_points;

    private string $selected_end_point;
    private string $selected_method;
    private array $request_data;

    public function run(): void
    {
        // Define end_points.
        $this->define_end_points();

        // Store the request method.
        // Store request data.
        $this->selected_method = $_SERVER["REQUEST_METHOD"];
        switch ($this->selected_method) {
            case "GET":
                $this->request_data = $_GET;
                break;
            case "POST":
                $this->request_data = $_POST;
                break;
            case "PUT":
            case "PATCH":
                parse_str(file_get_contents('php://input'), $this->request_data);
                if (!is_array($this->request_data)) {
                    $this->request_data = array();
                }
                break;
            default:
                throw "Request unknown";
        }

        // Check if request have an HTTP_X_API_KEY key.
        if (isset($_SERVER["HTTP_X_API_KEY"])) {
            $this->request_data["token"] = $_SERVER["HTTP_X_API_KEY"];
        }

        // Fetch the route selected.
        $this->selected_end_point = str_replace("/", "", parse_url($_SERVER["REQUEST_URI"])["path"]);
        if (empty($this->selected_end_points)) {
            $this->selected_end_point = "/";
        }

        // Set return content type to json.
        header("Content-Type: application/json; charset=UTF-8");

        // Check if the route exists (route + method). And return data.
        if (isset($this->end_points[$this->selected_method][$this->selected_end_point])) {
            // $this->end_points["checktoken"]($this->request_data);
            $this->end_points[$this->selected_method][$this->selected_end_point]($this->request_data);
            return;
        }

        // The route does not exists, we return a 404 + name of the route.
        $this->end_points["404"](array("endpointName" => $this->selected_end_point));
    }

    public function define_end_points(): void
    {
        /**
         * Route: /
         * Return a welcome message.
         */
        $this->end_points["GET"]["/"] = function (array $request_data): void {
            echo json_encode("This is Red Flag API.");
            exit;
        };

        /**
         * Route : /red-flags
         * Return the complete list of red flags store in database.
         */
        $this->end_points["GET"]["/red-flags"] = function (array $request_data): void {
            echo json_encode("This is Red Flag API.");
            exit;
        };

        /**
         * Route : /red-flags/:id
         * Return a specific red flag data with all comments + links about it.
         */
        $this->end_points["GET"]["/red-flags/:id"] = function (array $request_data): void {
            echo json_encode("This is Red Flag API.");
            exit;
        };

        /**
         * prints a default message if the endpoint path does not exist.
         */
        $this->end_points["404"] = function ($requestData): void {
            echo json_encode("The endpoint : " . $requestData["endpointName"] . " was not found.");
            exit;
        };

        /**
         * 
         */
        $this->end_points["checktoken"] = function ($requestData): void {
            //you can create secure tokens with this line, but that is a discussion for another post.. 
            //but i am using UUIDv4 instead.
            //$token = str_replace("=", "", base64_encode(random_bytes(160 / 8)));

            //authorized tokens
            $tokens = array(
                "fa3b2c9c-a96d-48a8-82ad-0cb775dd3e5d" => ""
            );

            if (!isset($requestData["token"])) {
                echo json_encode("No token was received to authorize the operation. Verify the information sent");
                exit;
            }

            if (!isset($tokens[$requestData["token"]])) {
                echo json_encode("The token  " . $requestData["token"] . " does not exists or is not authorized to perform this operation.");
                exit;
            }
        };
    }
}
