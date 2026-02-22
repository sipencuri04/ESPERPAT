<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cross-Origin Resource Sharing (CORS) Configuration
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class Cors extends BaseConfig
{
    /**
     * The default CORS configuration.
     *
     * @var array{
     *      allowedOrigins: list<string>,
     *      allowedOriginsPatterns: list<string>,
     *      supportsCredentials: bool,
     *      allowedHeaders: list<string>,
     *      exposedHeaders: list<string>,
     *      allowedMethods: list<string>,
     *      maxAge: int,
     *  }
     */
    public array $default = [
        /**
         * Origins for the `Access-Control-Allow-Origin` header.
         *
         * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin
         *
         * E.g.:
         *   - ['http://localhost:8080']
         *   - ['https://www.example.com']
         */
        'allowedOrigins' => [
            'https://esperpat.vercel.app',
            'http://localhost:5173',
            'http://localhost:3000',
            'http://localhost',
        ],

        /**
         * Origin regex patterns for the `Access-Control-Allow-Origin` header.
         * Allows all vercel.app previews and trycloudflare.com tunnels.
         */
        'allowedOriginsPatterns' => [
            'https://[\w-]+\.vercel\.app',
            'https://[\w-]+\.trycloudflare\.com',
        ],

        /**
         * Weather to send the `Access-Control-Allow-Credentials` header.
         */
        'supportsCredentials' => false,

        /**
         * Set headers to allow.
         */
        'allowedHeaders' => [
            'X-Requested-With',
            'Content-Type',
            'Authorization',
            'Accept',
            'Origin',
        ],

        /**
         * Set headers to expose.
         */
        'exposedHeaders' => [],

        /**
         * Set methods to allow.
         */
        'allowedMethods' => [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'PATCH',
            'OPTIONS',
        ],

        /**
         * Set how many seconds the results of a preflight request can be cached.
         *
         * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age
         */
        'maxAge' => 7200,
    ];
}
