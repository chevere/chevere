<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core;

use Exception;
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
        510 => 'Not Extended'
    ];
    /**
     * Sets HTTP status code.
     *
     * @param int $code HTTP status code.
     * TODO: GET RID OF THIS
     *
     * @throws Exception If $code is not a valid HTTP status code.
     */
    public static function setStatusCode(int $code, bool $force = false) : void
    {
        //FIXME: Deprecate usage, keep method.
        echo 'GET RADS';
        die();
        //     if (App::instance() instanceof App) {
    //         $protocol = App::instance()->getRequest()->server->get('SERVER_PROTOCOL');
    //     } else {
    //         $protocol = $_SERVER['SERVER_PROTOCOL'];
    //     }
    //     if ('HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol) {
    //         $protocol = 'HTTP/1.0';
    //     }
    //     if (($desc = static::statusDescription($code)) == null) {
    //         throw new Exception(
    //             (new Message('Invalid HTTP code %s'))->code('%s', "$protocol $code")
    //         );
    //     }
    //     $status = "$protocol $code $desc";
    //     if (headers_sent()) {
    //         if (($httpResponseCode = http_response_code()) != $code) {
    //             $responseStatus = "$protocol $httpResponseCode " . static::statusDescription($httpResponseCode);
    //             throw new Exception(
    //                 (new Message('Unable to set %s - %r has been already set'))
    //                     ->code('%s', $status)
    //                     ->code('%r', $responseStatus)
    //             );
    //         }
    //     }
    //     http_response_code($code);
    //     header($status, true, $code);
    }
    /**
     * Gets the HTTP header description corresponding to its code.
     *
     * @param int $code HTTP status code.
     *
     * @return string HTTP status code description.
     */
    public static function statusDescription(int $code) : string
    {
        return static::STATUS_CODES[$code];
    }
    /**
     * Redirects to another URL.
     *
     * @param string $to URL or App\URL relative path.
     *
     * @param int $status HTTP status code.
     */
    public static function redirect(string $to, int $status = 301) : void
    {
        // FIXME: Deprecate?
        // if (!filter_var($to, FILTER_VALIDATE_URL)) {
        //     $url = App::url($to);
        // } else {
        //     $url = $to;
        // }
        // $url = preg_replace('|[^a-z0-9-~+_.?#=&;,/:%!]|i', '', $url);
        // if (php_sapi_name() != 'cgi-fcgi') {
        //     static::setStatusCode($status);
        // }
        // header("Location: $url");
        // die();
    }
    /**
     * Stop execution with HTTP status code.
     *
     * @param int $status HTTP status code.
     */
    public static function die(int $status) : void
    {
        static::setStatusCode($status);
        die();
    }
    /**
     * Detects if the actual request was made via XMLHttpRequest using $_SERVER
     *
     * @return bool TRUE if the actual request was made via XHR.
     */
    public static function isXHR() : bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    /**
     * Get client remote address (client's IP).
     *
     * @return string Client IP address.
     */
    public static function clientIp() : ?string
    {
        $addr = null;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $addr =  !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : (!empty($_ENV['REMOTE_ADDR']) ? $_ENV['REMOTE_ADDR'] : $addr);
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
                                      '/^10\..*/'];
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
    /**
     * Get request URL
     *
     * @param bool $configAware True if this should be app/config.php aware.
     *
     * @return string Current URL.
     */
    public static function requestUrl(bool $configAware = true) : string
    {
        // if ($configAware && Config::has(Config::HTTP_SCHEME)) {
        //     $scheme = Config::get(Config::HTTP_SCHEME);
        // }
        // $app = App::instance();
        // if ($app->hasObject('request')) {
        //     $request = $app->getRequest();
        //     $scheme = $scheme ?? (defined('App\HTTP_SCHEME') ? App\HTTP_SCHEME : $request->getScheme());
        //     $host = $request->getHttpHost();
        //     $basePath = $request->getBasePath();
        // } else {
        //     $scheme = $scheme ?? 'http';
        //     $host = $_SERVER['HTTP_HOST'];
        //     $basePath = $_SERVER['REQUEST_URI'];
        // }
        // return $scheme . sprintf('://%s', $host . $basePath);
    }
}
