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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class Response
{
    /** @var SymfonyResponse */
    private $symfony;

    public function __construct()
    {
        $this->symfony = new SymfonyResponse('', 200, []);
    }

    public function symfony(): symfonyResponse
    {
        return $this->symfony;
    }

    public function unsetContent(): void
    {
        $this->symfony->setContent(null);
    }

    public function setJsonContent(string $jsonString): void
    {
        $this->setJsonHeaders();
        $this->symfony->setContent($jsonString);
    }

    public function send(): SymfonyResponse
    {
        return $this->symfony->send();
    }

    public function getStatusString(): string
    {
        return sprintf('HTTP/%s %s %s', $this->symfony->version, $this->symfony->statusCode, $this->symfony->statusText);
    }

    private function setJsonHeaders()
    {
        if (!$this->symfony->headers->has('Content-Type') || 'text/javascript' === $this->symfony->headers->get('Content-Type')) {
            $this->symfony->headers->set('Content-Type', 'application/vnd.api+json');
        }
    }
}
