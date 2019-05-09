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

namespace Chevereto\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ServerBag;

class HttpRequest extends Request
{
    // Left side: Our standard, right side Request function name
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

    public function getRequest(): ParameterBag
    {
        return $this->request;
    }

    public function getQuery(): ParameterBag
    {
        return $this->query;
    }

    public function getCookies(): ParameterBag
    {
        return $this->cookies;
    }

    public function getAttributes(): ParameterBag
    {
        return $this->attributes;
    }

    public function getFiles(): FileBag
    {
        return $this->files;
    }

    public function getServer(): ServerBag
    {
        return $this->server;
    }

    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

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
