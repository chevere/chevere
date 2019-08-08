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

namespace Chevere\HttpFoundation;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ServerBag;

// FIXME: Do a client, not an extension
final class Request extends HttpRequest
{
    // Left side: Chevere, right side Request function name
    const MAP = [
        'isSecure' => 'isSecure',
        'isMethodIdempotent' => 'isMethodIdempotent',
        'isMethodCacheable' => 'isMethodCacheable',
        'isNoCache' => 'isNoCache',
        'isXmlHttpRequest' => 'isXmlHttpRequest',
        'isFromTrustedProxy' => 'isFromTrustedProxy',
        'trustedProxies' => 'getTrustedProxies',
        'trustedHeaderSet' => 'getTrustedHeaderSet',
        'trustedHosts' => 'getTrustedHosts',
        'httpMethodParameterOverride' => 'getHttpMethodParameterOverride',
        'session' => 'getSession',
        'clientIps' => 'getClientIps',
        'clientIp' => 'getClientIp',
        'scriptName' => 'getScriptName',
        'pathInfo' => 'getPathInfo',
        'basePath' => 'getBasePath',
        'baseUrl' => 'getBaseUrl',
        'scheme' => 'getScheme',
        'port' => 'getPort',
        'user' => 'getUser',
        'password' => 'getPassword',
        'userInfo' => 'getUserInfo',
        'httpHost' => 'getHttpHost',
        'requestUri' => 'getRequestUri',
        'schemeAndHttpHost' => 'getSchemeAndHttpHost',
        'uri' => 'getUri',
        'queryString' => 'getQueryString',
        'host' => 'getHost',
        'method' => 'getMethod',
        'realMethod' => 'getRealMethod',
        'requestFormat' => 'getRequestFormat',
        'contentType' => 'getContentType',
        'defaultLocale' => 'getDefaultLocale',
        'locale' => 'getLocale',
        'protocolVersion' => 'getProtocolVersion',
        'content' => 'getContent',
        'eTags' => 'getETags',
        'preferredLanguage' => 'getPreferredLanguage',
        'languages' => 'getLanguages',
        'charsets' => 'getCharsets',
        'encodings' => 'getEncodings',
        'acceptableContentTypes' => 'getAcceptableContentTypes',
    ];

    /** @var ParameterBag */
    public $request;

    /** @var ParameterBag */
    public $query;

    /** @var ParameterBag */
    public $cookies;

    /** @var ParameterBag */
    public $attributes;

    /** @var FileBag */
    public $files;

    /** @var ServerBag */
    public $server;

    /** @var HeaderBag */
    public $headers;

    public function readInfo(): array
    {
        $info = [];
        foreach (static::MAP as $k => $v) {
            $info[$k] = $this->readInfoKey($k);
        }

        return $info;
    }

    public function readInfoKey(string $key)
    {
        $function = static::MAP[$key] ?? null;
        if (!isset($function)) {
            return null;
        }
        if ('getSession' === $function) {
            return $this->hasSession() ? $this->{$function}() : null;
        } else {
            return $this->{$function}();
        }
    }
}
