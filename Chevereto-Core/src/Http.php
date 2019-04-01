<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Symfony\Component\HttpFoundation\Request;

class Http
{
    const STATUS_CODES = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        510 => 'Not Extended',
    ];

    /**
     * Detects if the actual request was made via XMLHttpRequest using $_SERVER.
     *
     * @return bool TRUE if the actual request was made via XHR
     */
    // FIXME: Deprecate
    public static function isXHR(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Get client remote address (client's IP).
     *
     * @return string client IP address
     */
    public static function clientIp(): ?string
    {
        $addr = null;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $addr = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (!empty($_ENV['REMOTE_ADDR']) ? $_ENV['REMOTE_ADDR'] : $addr);
            $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if (is_array($entries) && reset($entries) != false) {
                while (list(, $entry) = each($entries)) {
                    $entry = trim($entry);
                    if (preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $ipList)) {
                        $privateIp = [
                                      '/^0\./',
                                      '/^127\.0\.0\.1/',
                                      '/^192\.168\..*/',
                                      '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
                                      '/^10\..*/', ];
                        $ip = preg_replace($privateIp, $addr, $ipList[1]);
                        if ($addr != $ip) { //  and !isset($_SERVER['HTTP_CF_CONNECTING_IP']
                            $addr = $ip;
                            break;
                        }
                    }
                }
            }
        } else {
            $remoteAddr = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
            $addr = !empty($remoteAddr) ? $remoteAddr : (!empty($_ENV['REMOTE_ADDR']) ? $_ENV['REMOTE_ADDR'] : $addr);
        }

        return $addr;
    }
}
