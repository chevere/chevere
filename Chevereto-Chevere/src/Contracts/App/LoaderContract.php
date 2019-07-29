<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Contracts\App;

use Chevere\HttpFoundation\Request;
use Chevere\Runtime\Runtime;

interface LoaderContract
{
    public function __construct();

    /**
     * Forges a Request, wrapper for Symfony Request::create().
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param string|resource|null $content    The raw body data
     */
    public function forgeHttpRequest(...$requestArguments): void;

    public function setController(string $controller): void;

    public function run(): void;

    /**
     * @param array $arguments string arguments captured or injected
     */
    public function setArguments(array $arguments): void;

    public static function runtime(): Runtime;

    public static function request(): Request;

    public static function setDefaultRuntime(Runtime $runtime);
}
