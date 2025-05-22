<?php

declare(strict_types=1);

namespace Infra\Router;


class Cache
{
    /**
     * @param string $key
     * @param int $duration
     * @param callable $callback
     */
    public static function run(string $key, int $duration, callable $callback): mixed
    {
        $cacheFile = ROOT_PATH . "/cache/" . md5($key) . ".cache";
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $duration) {
            // * Cache HIT
            return json_decode(file_get_contents($cacheFile));
        }

        // * Execute code and get result.
        $data = $callback();
        // * Create cache.
        file_put_contents($cacheFile, json_encode($data));
        return $data;
    }

    /**
     * 
     */
    public static function invalidate(string $key): void {}
}
