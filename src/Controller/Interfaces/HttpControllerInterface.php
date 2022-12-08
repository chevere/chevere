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

namespace Chevere\Controller\Interfaces;

use Chevere\Parameter\Interfaces\ParametersInterface;

/**
 * Describes the component in charge of defining an Http Controller which adds methods for handling HTTP requests.
 */
interface HttpControllerInterface extends ControllerInterface
{
    /**
     * Defines the GET parameters accepted.
     */
    public function acceptGet(): ParametersInterface;

    /**
     * Defines the POST parameters accepted.
     */
    public function acceptPost(): ParametersInterface;

    /**
     * Defines the FILES parameters accepted.
     */
    public function acceptFiles(): ParametersInterface;

    /**
     * @param array<int|string, string> $get
     */
    public function withGet(array $get): static;

    /**
     * @param array<int|string, string> $post
     */
    public function withPost(array $post): static;

    /**
     * @param array<int|string, array<string, int|string>> $files
     */
    public function withFiles(array $files): static;

    /**
     * Return an instance with the specified middleware.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified middleware.
     */
    public function withMiddleware(HttpMiddlewareInterface $middleware): static;

    /**
     * Provides access to the controller middleware.
     */
    public function middleware(): HttpMiddlewareInterface;

    /**
     * @return array<int|string, string>
     */
    public function get(): array;

    /**
     * @return array<int|string, string>
     */
    public function post(): array;

    /**
     * @return array<int|string, array<string, int|string>>
     */
    public function files(): array;
}
