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
use Chevereto\Core\Traits\PrintableTrait;

/**
 * $json = new Json($data);
 * $json->setResponse('message', 'code')->print();.
 */
// TODO: Use {json:api}
class Json implements Interfaces\PrintableInterface
{
    use DataTrait;
    use PrintableTrait;

    const CODE = 'code';
    const DATA = 'data';
    const DESCRIPTION = 'description';
    const MESSAGE = 'message';
    const STATUS = 'status';
    const RESPONSE = 'response';
    const CONTENT_TYPE = ['Content-type' => 'application/json; charset=UTF-8'];

    protected $response;
    protected $callback;
    protected $status;
    protected $printable;

    public $content;
    /**
     * JSON data constructor.
     *
     * @param array $data data array
     */

    /**
     * Set the JSON response data.
     *
     * @param string $message app response message
     * @param int    $code    app responde code
     *
     * @return $this chaineable
     */
    public function setResponse(string $message, int $code = null): self
    {
        $this->response = [static::CODE => $code, static::MESSAGE => $message];

        return $this;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function setResponseKey(string $key, $var)
    {
        $this->response[$key] = $var;
    }

    /**
     * Executes the JSON format operation.
     */
    public function exec(): void
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
