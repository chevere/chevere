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

/**
 * $json = new Json($data);
 * $json->setResponse('message', 'code')->print();
 */
// TODO: Use {json:api}
class Json extends Data implements Interfaces\PrintableInterface
{
    use Traits\Printable;
    
    const CODE = 'code';
    const DATA = 'data';
    const DESCRIPTION = 'description';
    const MESSAGE = 'message';
    const STATUS = 'status';
    const RESPONSE = 'response';
    const CONTENT_TYPE = ['Content-type' => 'application/json; charset=UTF-8'];

    protected $response;
    protected $data;
    protected $callback;
    protected $status;
    protected $printable;
    
    public $content;
    /**
     * JSON data constructor
     *
     * @param array $data Data array.
     */
    
    /**
     * Set the JSON response data.
     *
     * @param string $message App response message.
     * @param int $code App responde code.
     *
     * @return $this Chaineable.
     */
    public function setResponse(string $message, int $code = null) : self
    {
        $this->response = [static::CODE => $code, static::MESSAGE => $message];
        return $this;
    }
    public function getResponse() : ?array
    {
        return $this->response;
    }
    public function setResponseKey(string $key, $var)
    {
        $this->response[$key] = $var;
    }
    // public function setDataKey(string $key, $var) : self
    // {
    //     $this->data[$key] = $var;
    //     return $this;
    // }
    /**
     * Add data keys to data variable.
     *
     * @param string $key Data key.
     * @param mixed $var Data value.
     *
     * @return $this Chaineable.
     */
    // public function addDataKey(string $key, $var) : self
    // {
    //     $this->data[$key] = $var;
    //     return $this;
    // }
    // public function removeDataKey(string $key) : self
    // {
    //     unset($this->data[$key]);
    //     return $this;
    // }
    /**
     * Sets callback (JSONP).
     *
     * @param string $callback JSONP callback.
     *
     * @return $this Chaineable.
     */
    public function callback(string $callback) : self
    {
        $this->callback = $callback;
        return $this;
    }
    /**
     * Executes the JSON format operation.
     */
    public function exec() : void
    {
        $output = [
            static::RESPONSE => $this->response,
        ];
        if ($this->data) {
            $output[static::DATA] = $this->data;
        }
        $jsonEncode = json_encode($output, JSON_PRETTY_PRINT);
        if ($jsonEncode == false) {
            $code = 500;
            $output = [
                static::RESPONSE => [static::CODE => $code, static::MESSAGE => "Data couldn't be encoded into json"],
            ];
            $jsonEncode = json_encode($output, JSON_PRETTY_PRINT);
        }
        if (is_null($this->callback) == false) {
            $this->printable = sprintf('%s(%s);', $this->callback, $jsonEncode);
        } else {
            $this->printable = $jsonEncode;
        }
        $this->content = $this->printable;
    }
}