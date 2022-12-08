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
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Type\Interfaces\TypeInterface;
use Chevere\Type\Type;

final class FileParameter implements FileParameterInterface
{
    use ParameterTrait;

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

    private ParametersInterface $parameters;

    final public function __construct(
        StringParameterInterface $name,
        IntegerParameterInterface $size,
        StringParameterInterface $tmp_name,
        StringParameterInterface $type,
        private string $description = '',
    ) {
        $this->type = $this->type();
        $this->parameters = new Parameters(
            error: integerParameter()
                ->withValue(UPLOAD_ERR_OK),
            name: $name,
            size: $size,
            tmp_name: $tmp_name,
            type: $type,
        );
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function default(): array
    {
        return $this->default;
    }

    private function getType(): TypeInterface
    {
        return new Type(Type::ARRAY);
    }
}
