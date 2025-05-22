<?php

declare(strict_types=1);

namespace Infra\Router;

// TODO This is generated, needs changes.

// class Limiter
// {
//     public static function rate_limit($limit = 100, $period = 3600)
//     {
//         $ip = $_SERVER["REMOTE_ADDR"];
//         $file = ROOT_PATH . "/limiter/{$ip}.json";

//         if (!file_exists(__DIR__ . "/rate_limits")) {
//             mkdir(__DIR__ . "/rate_limits", 0777, true);
//         }

//         $data = [
//             'count' => 0,
//             'start_time' => time()
//         ];

//         if (file_exists($file)) {
//             $data = json_decode(file_get_contents($file), true);
//         }

//         $now = time();

//         // Reset if time window has passed
//         if ($now - $data['start_time'] > $period) {
//             $data['count'] = 0;
//             $data['start_time'] = $now;
//         }

//         $data['count']++;

//         file_put_contents($file, json_encode($data));

//         if ($data['count'] > $limit) {
//             http_response_code(429); // Too Many Requests
//             echo "Rate limit exceeded. Try again later.";
//             exit;
//         }
//     }
// }
