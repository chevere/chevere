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
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Traits\ArrayParameterTrait;
use Chevere\Parameter\Traits\ParameterTrait;

final class FileParameter implements FileParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;

    /**
     * @var array<string, mixed>
     */
    private array $default = [
        'error' => UPLOAD_ERR_NO_FILE,
        'name' => '',
        'size' => 0,
        'tmp_name' => '',
        'type' => '',
    ];

    final public function __construct(
        StringParameterInterface $name,
        IntegerParameterInterface $size,
        StringParameterInterface $type,
        private string $description = '',
    ) {
        $this->type = $this->type();
        $this->parameters = new Parameters(
            error: integerParameter()
                ->withAccept(UPLOAD_ERR_OK),
            name: $name,
            size: $size,
            tmp_name: stringParameter(),
            type: $type,
        );
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    /**
     * @codeCoverageIgnore
     */
    public function assertCompatible(FileParameterInterface $parameter): void
    {
    }
}
