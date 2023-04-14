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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParametersAccessTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class FileParameter implements FileParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;
    use ParametersAccessTrait;

    /**
     * @var array<string, mixed>
     */
    private ?array $default = null;

    final public function __construct(
        StringParameterInterface $name,
        StringParameterInterface $type,
        StringParameterInterface $tmp_name,
        IntegerParameterInterface $size,
        private string $description = '',
    ) {
        $this->type = $this->type();
        $this->parameters = parameters(
            error: integer()->withAccept(UPLOAD_ERR_OK),
            name: $name,
            size: $size,
            tmp_name: $tmp_name,
            type: $type,
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function assertCompatible(FileParameterInterface $parameter): void
    {
    }
}
