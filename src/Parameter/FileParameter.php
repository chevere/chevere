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
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Type\typeFile;

final class FileParameter implements FileParameterInterface
{
    use ParameterTrait;
    use ArrayParameterTrait;

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
        $this->parameters = new Parameters(
            error: integer(accept: [UPLOAD_ERR_OK]),
            name: $name,
            size: $size,
            tmp_name: $tmp_name,
            type: $type,
        );
    }

    public function assertCompatible(FileParameterInterface $parameter): void
    {
        foreach ($this->parameters as $name => $stock) {
            $stock->assertCompatible($parameter->parameters()->get($name));
        }
    }

    private function getType(): TypeInterface
    {
        return typeFile();
    }
}
