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

namespace Chevere\Interfaces\Router\Routing;

use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Writer\WriterInterface;

/**
 * Describes the component in charge of generating routing descriptors from a given directory.
 */
interface RoutingDescriptorsMakerInterface
{
    /**
     * @throws LogicException
     */
    public function __construct(string $repository);

    public function withWriter(WriterInterface $writer): self;

    public function withTrailingSlash(bool $bool): self;

    public function withDescriptorsFor(DirInterface $dir): self;

    public function writer(): WriterInterface;

    public function useTrailingSlash(): bool;

    public function repository(): string;

    /**
     * Provides access to the generated routing descriptors.
     */
    public function descriptors(): RoutingDescriptorsInterface;
}
