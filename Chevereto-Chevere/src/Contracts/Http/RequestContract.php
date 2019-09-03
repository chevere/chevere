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

/*
 * This file contains part of Symfony code.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 */

namespace Chevere\Contracts\Http;

use LogicException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

interface RequestContract
{
  /**
   * @param array                $query      The GET parameters
   * @param array                $request    The POST parameters
   * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
   * @param array                $cookies    The COOKIE parameters
   * @param array                $files      The FILES parameters
   * @param array                $server     The SERVER parameters
   * @param string|resource|null $content    The raw body data
   */
  public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null);

  /**
   * Sets the parameters for this request.
   *
   * This method also re-initializes all properties.
   *
   * @param array                $query      The GET parameters
   * @param array                $request    The POST parameters
   * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
   * @param array                $cookies    The COOKIE parameters
   * @param array                $files      The FILES parameters
   * @param array                $server     The SERVER parameters
   * @param string|resource|null $content    The raw body data
   */
  public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null);

  /**
   * Creates a new request with values from PHP's super globals.
   *
   * @return SymfonyRequest
   */
  public static function createFromGlobals();

  /**
   * Creates a Request based on a given URI and configuration.
   *
   * The information contained in the URI always take precedence
   * over the other information (server and parameters).
   *
   * @param string               $uri        The URI
   * @param string               $method     The HTTP method
   * @param array                $parameters The query (GET) or request (POST) parameters
   * @param array                $cookies    The request cookies ($_COOKIE)
   * @param array                $files      The request files ($_FILES)
   * @param array                $server     The server parameters ($_SERVER)
   * @param string|resource|null $content    The raw body data
   *
   * @return SymfonyRequest
   */
  public static function create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null);

  /**
   * Sets a callable able to create a Request instance.
   *
   * This is mainly useful when you need to override the Request class
   * to keep BC with an existing system. It should not be used for any
   * other purpose.
   *
   * @param callable|null $callable A PHP callable
   */
  public static function setFactory($callable);

  /**
   * Clones a request and overrides some of its parameters.
   *
   * @param array $query      The GET parameters
   * @param array $request    The POST parameters
   * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
   * @param array $cookies    The COOKIE parameters
   * @param array $files      The FILES parameters
   * @param array $server     The SERVER parameters
   *
   * @return SymfonyRequest
   */
  public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null);

  /**
   * Clones the current request.
   *
   * Note that the session is not cloned as duplicated requests
   * are most of the time sub-requests of the main one.
   */
  public function __clone();

  /**
   * Returns the request as a string.
   *
   * @return string The request
   */
  public function __toString();

  /**
   * Overrides the PHP global variables according to this request instance.
   *
   * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
   * $_FILES is never overridden, see rfc1867
   */
  public function overrideGlobals();

  /**
   * Sets a list of trusted proxies.
   *
   * You should only list the reverse proxies that you manage directly.
   *
   * @param array $proxies          A list of trusted proxies
   * @param int   $trustedHeaderSet A bit field of Request::HEADER_*, to set which headers to trust from your proxies
   *
   * @throws InvalidArgumentException When $trustedHeaderSet is invalid
   */
  public static function setTrustedProxies(array $proxies, int $trustedHeaderSet);

  /**
   * Gets the list of trusted proxies.
   *
   * @return array An array of trusted proxies
   */
  public static function getTrustedProxies();

  /**
   * Gets the set of trusted headers from trusted proxies.
   *
   * @return int A bit field of Request::HEADER_* that defines which headers are trusted from your proxies
   */
  public static function getTrustedHeaderSet();

  /**
   * Sets a list of trusted host patterns.
   *
   * You should only list the hosts you manage using regexs.
   *
   * @param array $hostPatterns A list of trusted host patterns
   */
  public static function setTrustedHosts(array $hostPatterns);

  /**
   * Gets the list of trusted host patterns.
   *
   * @return array An array of trusted host patterns
   */
  public static function getTrustedHosts();

  /**
   * Normalizes a query string.
   *
   * It builds a normalized query string, where keys/value pairs are alphabetized,
   * have consistent escaping and unneeded delimiters are removed.
   *
   * @param string $qs Query string
   *
   * @return string A normalized query string for the Request
   */
  public static function normalizeQueryString($qs);

  /**
   * Enables support for the _method request parameter to determine the intended HTTP method.
   *
   * Be warned that enabling this feature might lead to CSRF issues in your code.
   * Check that you are using CSRF tokens when required.
   * If the HTTP method parameter override is enabled, an html-form with method "POST" can be altered
   * and used to send a "PUT" or "DELETE" request via the _method request parameter.
   * If these methods are not protected against CSRF, this presents a possible vulnerability.
   *
   * The HTTP method can only be overridden when the real HTTP method is POST.
   */
  public static function enableHttpMethodParameterOverride();

  /**
   * Checks whether support for the _method request parameter is enabled.
   *
   * @return bool True when the _method request parameter is enabled, false otherwise
   */
  public static function getHttpMethodParameterOverride();

  /**
   * Gets a "parameter" value from any bag.
   *
   * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
   * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
   * public property instead (attributes, query, request).
   *
   * Order of precedence: PATH (routing placeholders or custom attributes), GET, BODY
   *
   * @param string $key     The key
   * @param mixed  $default The default value if the parameter key does not exist
   *
   * @return mixed
   */
  public function get($key, $default = null);

  /**
   * Gets the Session.
   *
   * @return SessionInterface|null The session
   */
  public function getSession();

  /**
   * Whether the request contains a Session which was started in one of the
   * previous requests.
   *
   * @return bool
   */
  public function hasPreviousSession();

  /**
   * Whether the request contains a Session object.
   *
   * This method does not give any information about the state of the session object,
   * like whether the session is started or not. It is just a way to check if this Request
   * is associated with a Session instance.
   *
   * @return bool true when the Request contains a Session object, false otherwise
   */
  public function hasSession();

  /**
   * Sets the Session.
   *
   * @param SessionInterface $session The Session
   */
  public function setSession(SessionInterface $session);

  /**
   * @internal
   */
  public function setSessionFactory(callable $factory);

  /**
   * Returns the client IP addresses.
   *
   * In the returned array the most trusted IP address is first, and the
   * least trusted one last. The "real" client IP address is the last one,
   * but this is also the least trusted one. Trusted proxies are stripped.
   *
   * Use this method carefully; you should use getClientIp() instead.
   *
   * @return array The client IP addresses
   *
   * @see getClientIp()
   */
  public function getClientIps();

  /**
   * Returns the client IP address.
   *
   * This method can read the client IP address from the "X-Forwarded-For" header
   * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
   * header value is a comma+space separated list of IP addresses, the left-most
   * being the original client, and each successive proxy that passed the request
   * adding the IP address where it received the request from.
   *
   * @return string|null The client IP address
   *
   * @see getClientIps()
   * @see http://en.wikipedia.org/wiki/X-Forwarded-For
   */
  public function getClientIp();

  /**
   * Returns current script name.
   *
   * @return string
   */
  public function getScriptName();

  /**
   * Returns the path being requested relative to the executed script.
   *
   * The path info always starts with a /.
   *
   * Suppose this request is instantiated from /mysite on localhost:
   *
   *  * http://localhost/mysite              returns an empty string
   *  * http://localhost/mysite/about        returns '/about'
   *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
   *  * http://localhost/mysite/about?var=1  returns '/about'
   *
   * @return string The raw path (i.e. not urldecoded)
   */
  public function getPathInfo();

  /**
   * Returns the root path from which this request is executed.
   *
   * Suppose that an index.php file instantiates this request object:
   *
   *  * http://localhost/index.php         returns an empty string
   *  * http://localhost/index.php/page    returns an empty string
   *  * http://localhost/web/index.php     returns '/web'
   *  * http://localhost/we%20b/index.php  returns '/we%20b'
   *
   * @return string The raw path (i.e. not urldecoded)
   */
  public function getBasePath();

  /**
   * Returns the root URL from which this request is executed.
   *
   * The base URL never ends with a /.
   *
   * This is similar to getBasePath(), except that it also includes the
   * script filename (e.g. index.php) if one exists.
   *
   * @return string The raw URL (i.e. not urldecoded)
   */
  public function getBaseUrl();

  /**
   * Gets the request's scheme.
   *
   * @return string
   */
  public function getScheme();

  /**
   * Returns the port on which the request is made.
   *
   * This method can read the client port from the "X-Forwarded-Port" header
   * when trusted proxies were set via "setTrustedProxies()".
   *
   * The "X-Forwarded-Port" header must contain the client port.
   *
   * @return int|string can be a string if fetched from the server bag
   */
  public function getPort();

  /**
   * Returns the user.
   *
   * @return string|null
   */
  public function getUser();

  /**
   * Returns the password.
   *
   * @return string|null
   */
  public function getPassword();

  /**
   * Gets the user info.
   *
   * @return string A user name and, optionally, scheme-specific information about how to gain authorization to access the server
   */
  public function getUserInfo();

  /**
   * Returns the HTTP host being requested.
   *
   * The port name will be appended to the host if it's non-standard.
   *
   * @return string
   */
  public function getHttpHost();

  /**
   * Returns the requested URI (path and query string).
   *
   * @return string The raw URI (i.e. not URI decoded)
   */
  public function getRequestUri();

  /**
   * Gets the scheme and HTTP host.
   *
   * If the URL was called with basic authentication, the user
   * and the password are not added to the generated string.
   *
   * @return string The scheme and HTTP host
   */
  public function getSchemeAndHttpHost();

  /**
   * Generates a normalized URI (URL) for the Request.
   *
   * @return string A normalized URI (URL) for the Request
   *
   * @see getQueryString()
   */
  public function getUri();

  /**
   * Generates a normalized URI for the given path.
   *
   * @param string $path A path to use instead of the current one
   *
   * @return string The normalized URI for the path
   */
  public function getUriForPath($path);

  /**
   * Returns the path as relative reference from the current Request path.
   *
   * Only the URIs path component (no schema, host etc.) is relevant and must be given.
   * Both paths must be absolute and not contain relative parts.
   * Relative URLs from one resource to another are useful when generating self-contained downloadable document archives.
   * Furthermore, they can be used to reduce the link size in documents.
   *
   * Example target paths, given a base path of "/a/b/c/d":
   * - "/a/b/c/d"     -> ""
   * - "/a/b/c/"      -> "./"
   * - "/a/b/"        -> "../"
   * - "/a/b/c/other" -> "other"
   * - "/a/x/y"       -> "../../x/y"
   *
   * @param string $path The target path
   *
   * @return string The relative target path
   */
  public function getRelativeUriForPath($path);

  /**
   * Generates the normalized query string for the Request.
   *
   * It builds a normalized query string, where keys/value pairs are alphabetized
   * and have consistent escaping.
   *
   * @return string|null A normalized query string for the Request
   */
  public function getQueryString();

  /**
   * Checks whether the request is secure or not.
   *
   * This method can read the client protocol from the "X-Forwarded-Proto" header
   * when trusted proxies were set via "setTrustedProxies()".
   *
   * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
   *
   * @return bool
   */
  public function isSecure();

  /**
   * Returns the host name.
   *
   * This method can read the client host name from the "X-Forwarded-Host" header
   * when trusted proxies were set via "setTrustedProxies()".
   *
   * The "X-Forwarded-Host" header must contain the client host name.
   *
   * @return string
   *
   * @throws SuspiciousOperationException when the host name is invalid or not trusted
   */
  public function getHost();

  /**
   * Sets the request method.
   *
   * @param string $method
   */
  public function setMethod($method);

  /**
   * Gets the request "intended" method.
   *
   * If the X-HTTP-Method-Override header is set, and if the method is a POST,
   * then it is used to determine the "real" intended HTTP method.
   *
   * The _method request parameter can also be used to determine the HTTP method,
   * but only if enableHttpMethodParameterOverride() has been called.
   *
   * The method is always an uppercased string.
   *
   * @return string The request method
   *
   * @see getRealMethod()
   */
  public function getMethod();

  /**
   * Gets the "real" request method.
   *
   * @return string The request method
   *
   * @see getMethod()
   */
  public function getRealMethod();

  /**
   * Gets the mime type associated with the format.
   *
   * @param string $format The format
   *
   * @return string|null The associated mime type (null if not found)
   */
  public function getMimeType($format);

  /**
   * Gets the mime types associated with the format.
   *
   * @param string $format The format
   *
   * @return array The associated mime types
   */
  public static function getMimeTypes($format);

  /**
   * Gets the format associated with the mime type.
   *
   * @param string $mimeType The associated mime type
   *
   * @return string|null The format (null if not found)
   */
  public function getFormat($mimeType);

  /**
   * Associates a format with mime types.
   *
   * @param string       $format    The format
   * @param string|array $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
   */
  public function setFormat($format, $mimeTypes);

  /**
   * Gets the request format.
   *
   * Here is the process to determine the format:
   *
   *  * format defined by the user (with setRequestFormat())
   *  * _format request attribute
   *  * $default
   *
   * @param string|null $default The default format
   *
   * @return string|null The request format
   */
  public function getRequestFormat($default = 'html');

  /**
   * Sets the request format.
   *
   * @param string $format The request format
   */
  public function setRequestFormat($format);

  /**
   * Gets the format associated with the request.
   *
   * @return string|null The format (null if no content type is present)
   */
  public function getContentType();

  /**
   * Sets the default locale.
   *
   * @param string $locale
   */
  public function setDefaultLocale($locale);

  /**
   * Get the default locale.
   *
   * @return string
   */
  public function getDefaultLocale();

  /**
   * Sets the locale.
   *
   * @param string $locale
   */
  public function setLocale($locale);

  /**
   * Get the locale.
   *
   * @return string
   */
  public function getLocale();

  /**
   * Checks if the request method is of specified type.
   *
   * @param string $method Uppercase request method (GET, POST etc)
   *
   * @return bool
   */
  public function isMethod($method);

  /**
   * Checks whether or not the method is safe.
   *
   * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
   *
   * @param bool $andCacheable Adds the additional condition that the method should be cacheable. True by default.
   *
   * @return bool
   */
  public function isMethodSafe();

  /**
   * Checks whether or not the method is idempotent.
   *
   * @return bool
   */
  public function isMethodIdempotent();

  /**
   * Checks whether the method is cacheable or not.
   *
   * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
   *
   * @return bool True for GET and HEAD, false otherwise
   */
  public function isMethodCacheable();

  /**
   * Returns the protocol version.
   *
   * If the application is behind a proxy, the protocol version used in the
   * requests between the client and the proxy and between the proxy and the
   * server might be different. This returns the former (from the "Via" header)
   * if the proxy is trusted (see "setTrustedProxies()"), otherwise it returns
   * the latter (from the "SERVER_PROTOCOL" server parameter).
   *
   * @return string
   */
  public function getProtocolVersion();

  /**
   * Returns the request body content.
   *
   * @param bool $asResource If true, a resource will be returned
   *
   * @return string|resource The request body content or a resource to read the body stream
   *
   * @throws LogicException
   */
  public function getContent($asResource = false);

  /**
   * Gets the Etags.
   *
   * @return array The entity tags
   */
  public function getETags();

  /**
   * @return bool
   */
  public function isNoCache();

  /**
   * Returns the preferred language.
   *
   * @param array $locales An array of ordered available locales
   *
   * @return string|null The preferred locale
   */
  public function getPreferredLanguage(array $locales = null);

  /**
   * Gets a list of languages acceptable by the client browser.
   *
   * @return array Languages ordered in the user browser preferences
   */
  public function getLanguages();

  /**
   * Gets a list of charsets acceptable by the client browser.
   *
   * @return array List of charsets in preferable order
   */
  public function getCharsets();

  /**
   * Gets a list of encodings acceptable by the client browser.
   *
   * @return array List of encodings in preferable order
   */
  public function getEncodings();

  /**
   * Gets a list of content types acceptable by the client browser.
   *
   * @return array List of content types in preferable order
   */
  public function getAcceptableContentTypes();

  /**
   * Returns true if the request is a XMLHttpRequest.
   *
   * It works if your JavaScript library sets an X-Requested-With HTTP header.
   * It is known to work with common JavaScript frameworks:
   *
   * @see http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
   *
   * @return bool true if the request is an XMLHttpRequest, false otherwise
   */
  public function isXmlHttpRequest();

  /**
   * Indicates whether this request originated from a trusted proxy.
   *
   * This can be useful to determine whether or not to trust the
   * contents of a proxy-specific header.
   *
   * @return bool true if the request came from a trusted proxy, false otherwise
   */
  public function isFromTrustedProxy();
}
