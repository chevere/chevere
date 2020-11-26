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

namespace Chevere\Components\Response\Traits;

use Chevere\Components\Parameter\Arguments;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Ramsey\Uuid\Uuid;

trait ResponseTrait
{
    private ArgumentsInterface $arguments;

    private string $uuid;

    private string $token;

    private array $data;

    public function __construct(ParametersInterface $parameters, array $data)
    {
        $this->arguments = new Arguments($parameters, $data);
        $this->uuid = Uuid::uuid4()->toString();
        $this->token = bin2hex(random_bytes(128));
        $this->data = $data;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function data(): array
    {
        return $this->data;
    }
}
