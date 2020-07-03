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

namespace Chevere\Components\Controller;

use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Controller\ControllerParameterNameInvalidException;
use Chevere\Exceptions\Core\Exception;
use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Chevere\Interfaces\Regex\RegexInterface;

final class ControllerParameter implements ControllerParameterInterface
{
    private bool $isRequired = true;

    private string $name;

    private RegexInterface $regex;

    private string $description = '';

    public function __construct(string $name, RegexInterface $regex)
    {
        $this->name = $name;
        $this->assertName();
        $this->regex = $regex;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
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

    public function withDescription(string $description): ControllerParameterInterface
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    public function withIsRequired(bool $bool): ControllerParameterInterface
    {
        $new = clone $this;
        $new->isRequired = $bool;

        return $new;
    }

    private function assertName(): void
    {
        try {
            (new StrAssert($this->name))
                ->notEmpty()
                ->notCtypeSpace()
                ->notContains(' ');
        } catch (Exception $e) {
            throw new ControllerParameterNameInvalidException(
                $e->message(),
                $e->getCode(),
                $e
            );
        }
    }
}
