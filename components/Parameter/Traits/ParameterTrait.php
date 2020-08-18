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

namespace Chevere\Components\Parameter\Traits;

use Chevere\Components\Description\Traits\DescriptorTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Regex\RegexInterface;

trait ParameterTrait
{
    private string $name;

    private RegexInterface $regex;

    private string $description = '';

    public function __construct(string $name)
    {
        $this->regex = new Regex('/^.*$/');
        $this->name = $name;
        $this->assertName();
    }

    public function withRegex(RegexInterface $regex): ParameterInterface
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    public function withDescription(string $description): ParameterInterface
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    public function description(): string
    {
        return $this->description;
    }

    private function assertName(): void
    {
        try {
            (new StrAssert($this->name))
                ->notEmpty()
                ->notCtypeSpace()
                ->notContains(' ');
        } catch (Exception $e) {
            throw new ParameterNameInvalidException(
                new Message('Invalid parameter name'),
                0,
                $e
            );
        }
    }
}
