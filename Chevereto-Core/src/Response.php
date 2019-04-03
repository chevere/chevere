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

use Chevereto\Core\Traits\DataTrait;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Exception;
use InvalidArgumentException;

/**
 * JSON:API HTTP Response handler.
 *
 * Forked version of Symfony\Component\HttpFoundation\JsonResponse (Igor Wiedler <igor@wiedler.ch>)
 * (c) Fabien Potencier <fabien@symfony.com>
 */
class Response extends HttpResponse
{
    // use DataTrait;

    public $val;

    // https://jsonapi.org/format/1.0/
    const JSON_API_VERSION = '1.0';

    /** @var bool */
    protected $hasBody = true;

    protected $jsonString;
    protected $callback;

    /**
     * A document MUST contain at least one of the following top-level members:
     * data,errors,meta
     * ^ The members data and errors MUST NOT coexist in the same document.
     */
    // the document’s “primary data”
    protected $data;
    // an array of error objects
    protected $errors;
    // a meta object that contains non-standard meta-information.
    protected $meta;

    /**
     * A document MAY contain any of these top-level members:
     * jsonapi, links, included
     * ^ If a document does not contain a top-level data key, the included member MUST NOT be present either.
     */
    // an object describing the server’s implementation.
    protected $jsonapi = ['version' => self::JSON_API_VERSION];
    // a links object related to the primary data.
    protected $links;
    // an array of resource objects that are related to the primary data and/or each other (“included resources”).
    protected $included;

    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    const DEFAULT_ENCODING_OPTIONS = 15;

    protected $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /**
     * @param mixed $data    The response data
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     * @param bool  $json    If the data is already a JSON string
     */
    public function __construct(array $data = null, int $status = 200, array $headers = [])
    {
        parent::__construct('', $status, $headers);
        // if (null != $data) {
        //     $this->setData($data);
        // }
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function addData(string $type, string $id, array $attributes = null): self
    {
        $data = [
            'type' => $type,
            'id' => $id,
        ];
        if (null != $attributes) {
            $data['attributes'] = $attributes;
        }

        return $this->appendData($data);
    }

    public function appendData(array $data): self
    {
        $this->data[] = $data;

        return $this;
    }

    public function setMeta(array $data): self
    {
        $this->meta = $data;

        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function hasDataKey(string $key): bool
    {
        return array_key_exists($key, $this->meta);
    }

    public function setMetaKey(string $key, $var): self
    {
        $this->meta[$key] = $var;

        return $this;
    }

    public function getMetaKey(string $key)
    {
        return $this->meta[$key] ?? null;
    }

    public function removeMetaKey(string $key): self
    {
        unset($this->meta[$key]);

        return $this;
    }

    /**
     * Sets the JSONP callback.
     *
     * @param string|null $callback The JSONP callback or null to use none
     *
     * @return $this
     *
     * @throws InvalidArgumentException When the callback name is not valid
     */
    public function setCallback($callback = null): self
    {
        if (null !== $callback) {
            // partially taken from http://www.geekality.net/2011/08/03/valid-javascript-identifier/
            // partially taken from https://github.com/willdurand/JsonpCallbackValidator
            //      JsonpCallbackValidator is released under the MIT License. See https://github.com/willdurand/JsonpCallbackValidator/blob/v1.1.0/LICENSE for details.
            //      (c) William Durand <william.durand1@gmail.com>
            $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*(?:\[(?:"(?:\\\.|[^"\\\])*"|\'(?:\\\.|[^\'\\\])*\'|\d+)\])*?$/u';
            $reserved = [
                'break', 'do', 'instanceof', 'typeof', 'case', 'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue', 'for', 'switch', 'while',
                'debugger', 'function', 'this', 'with', 'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum', 'extends', 'super',  'const', 'export',
                'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 'null', 'true', 'false',
            ];
            $parts = explode('.', $callback);
            foreach ($parts as $part) {
                if (!preg_match($pattern, $part) || in_array($part, $reserved, true)) {
                    throw new InvalidArgumentException('The callback name is not valid.');
                }
            }
        }
        $this->callback = $callback;

        return $this;
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    protected function genJsonString(): self
    {
        try {
            $output = [
                'jsonapi' => $this->jsonapi,
                'meta' => $this->getMeta(),
                'data' => $this->getData(),
            ];
            // JSON_PRETTY_PRINT
            $encodingOptions = $this->encodingOptions;
            if (CLI) {
                $encodingOptions = $encodingOptions | JSON_PRETTY_PRINT;
            }
            $jsonString = json_encode($output, $encodingOptions);
        } catch (Exception $e) {
            if ('Exception' === get_class($e) && 0 === strpos($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }
        $this->jsonString = $jsonString;

        return $this;
    }

    /**
     * Returns options used while encoding data to JSON.
     *
     * @return int
     */
    public function getEncodingOptions()
    {
        return $this->encodingOptions;
    }

    /**
     * Sets options used while encoding data to JSON.
     *
     * @param int $encodingOptions
     *
     * @return $this
     */
    public function setEncodingOptions(int $encodingOptions): self
    {
        $this->encodingOptions = $encodingOptions;

        return $this;
    }

    /**
     * Updates the content and headers according to the JSON data and callback.
     *
     * @return $this
     */
    protected function setJsonContent(): self
    {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->headers->set('Content-Type', 'text/javascript');

            return $this->setContent(sprintf('/**/%s(%s);', $this->callback, $this->jsonString));
        }
        // Only set the header when there is none or when it equals 'text/javascript' (from a previous update with callback)
        // in order to not overwrite a custom definition.
        if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'application/vnd.api+json');
        }

        return $this->setContent($this->jsonString);
    }

    public function sendJson(): HttpResponse
    {
        if ($this->hasBody) {
            $this->genJsonString()->setJsonContent();
        }

        return parent::send();
    }

    /**
     * Completely removes the response body. Needed when dealing with HEAD responses.
     */
    public function setNoBody(): self
    {
        $this->hasBody = false;

        return $this;
    }
}
