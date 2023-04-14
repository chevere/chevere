<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\HttpController\Interfaces;

use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Http\Interfaces\MiddlewaresInterface;
use Chevere\Parameter\Interfaces\ArrayStringParameterInterface;
use Chevere\Parameter\Interfaces\ArrayTypeParameterInterface;

/**
 * Describes the component in charge of defining an Http Controller which adds methods for handling HTTP requests.
 */
interface HttpControllerInterface extends ControllerInterface
{
    public function statusSuccess(): int;

    public function statusError(): int;

    /**
     * Defines the query accepted.
     */
    public function acceptQuery(): ArrayStringParameterInterface;

    /**
     * Defines the body accepted.
     */
    public function acceptBody(): ArrayTypeParameterInterface;

    /**
     * Defines the FILES accepted.
     */
    public function acceptFiles(): ArrayTypeParameterInterface;

    /**
     * @param array<int|string, string> $query
     */
    public function withQuery(array $query): static;

    /**
     * @param array<int|string, mixed> $body
     */
    public function withBody(array $body): static;

    /**
     * @param array<int|string, array<string, int|string>> $files
     */
    public function withFiles(array $files): static;

    /**
     * Return an instance with the specified middlewares.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified middleware.
     */
    public function withMiddlewares(MiddlewaresInterface $middleware): static;

    /**
     * Provides access to the controller middlewares.
     */
    public function middlewares(): MiddlewaresInterface;

    /**
     * @return array<int|string, string>
     */
    public function query(): array;

    /**
     * @return array<int|string, mixed>
     */
    public function body(): array;

    /**
     * @return array<int|string, array<string, int|string>>
     */
    public function files(): array;

    /**
     * @return array<string, string>
     */
    public function responseHeaders(): array;
}
