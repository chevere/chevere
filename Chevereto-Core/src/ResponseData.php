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

use Chevereto\Core\Json;
use Chevereto\Core\Interfaces\DataInterface;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;

/**
 * ResponseData provides a way in which we can store and retrieve Response data.
 * It provides "handlebars" to pass a data object for further filtering, templating and HTTP response.
 */
class ResponseData implements DataInterface
{
    /** @var DataInterface */
    protected $data;
    /** @var int */
    protected $code;
    
    public function __construct(int $code = 200, $data = null)
    {
        if (null != $code) {
            $this->setCode($code);
        }
        if (null != $data) {
            $this->setData($data instanceof DataInterface ? $data : new Data($data));
        }
    }
    // FIXME: RespondeData must not handle the response!
    public function generateHttpResponse() : HttpFoundationResponse
    {
        $class = HttpFoundationResponse::class;
        $data = $this->getData();
        if ($data instanceof Json) {
            $class = HttpFoundationJsonResponse::class;
        } else {
            if (false == method_exists($data, '__toString')) {
                $dataData = $data->getData();
            }
            $data = null;
        }
        return (new $class())
            ->setContent($data)
            ->setStatusCode($this->code);
    }
    /**
     * Sets the response code (HTTP).
     *
     * @param int $code HTTP status code.
     */
    public function setCode(int $code) : self
    {
        $this->code = $code;
        return $this;
    }
    /**
     * Sets the response data.
     *
     * @param DataInterface $data
     */
    public function setData(DataInterface $data)
    {
        $this->data = $data;
        
        return $this;
    }
    public function getData() : ?DataInterface
    {
        return $this->data;
    }
    /**
     * Set data proxy
     */
    public function setDataKey(string $key, $var)
    {
        $this->getData()->setDataKey(...func_get_args());
        return $this;
    }
    public function addDataKey(string $key, $var)
    {
        $this->getData()->addData(...func_get_args());
        return $this;
    }
    public function removeDataKey(string $key)
    {
        $this->getData()->addData(...func_get_args());
        return $this;
    }
}