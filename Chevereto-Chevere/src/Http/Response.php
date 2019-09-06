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

namespace Chevere\Http;

use Chevere\Contracts\Http\ResponseContract;
use Chevere\Contracts\Http\Symfony\ResponseContract as SymfonyResponseContract;
use Chevere\JsonApi\JsonApi;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Response wraps HttpFoundation response (Symfony).
 */
final class Response extends SymfonyResponse implements ResponseContract, SymfonyResponseContract
{
    /** @var string */
    protected $version;

    /** @var int */
    protected $statusCode;

    /** @var string */
    protected $statusText;

    /** @var ResponseHeaderBag */
    public $headers;

    /**
     * {@inheritdoc}
     */
    public function setJsonContent(JsonApi $jsonApi): void
    {
        $this->setJsonHeaders();
        $this->setContent($jsonApi->toString());
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusString(): string
    {
        return sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText);
    }

    /**
     * {@inheritdoc}
     */
    public function getNoBody(): string
    {
        return $this->getStatusString() . "\r\n" . $this->headers . "\r\n";
    }

    /**
     * {@inheritdoc}
     */
    public function setJsonHeaders(): void
    {
        if (!$this->headers->has('Content-Type') || 'text/javascript' === $this->headers->get('Content-Type')) {
            $this->headers->set('Content-Type', 'application/vnd.api+json');
        }
    }
}
