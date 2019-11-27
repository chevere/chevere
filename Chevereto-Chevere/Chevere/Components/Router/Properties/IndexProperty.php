<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Router\Properties;

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exceptions\RouterPropertyException;
use Chevere\Components\Router\Properties\Traits\AssertsTrait;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Contracts\Router\Properties\IndexPropertyContract;
use Throwable;

final class IndexProperty implements IndexPropertyContract
{
    use ToArrayTrait;
    use AssertsTrait;

    /** @var array [(int)$id => 'entry'] */
    private $locator;

    /** @var array (int)$id[] Checked entries */
    private $check;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $index)
    {
        try {
            $this->locator = [];
            $this->check = [];
            $this->assertArrayNotEmpty($index);
            $this->value = $index;
            $this->asserts();
        } catch (Throwable $e) {
            $message = new Message($e->getMessage());
            if (!empty($this->locator)) {
                foreach ($this->check as $remove) {
                    unset($this->locator[$remove]);
                }
                $message = (new Message('%exception% at %at%'))
                    ->strtr('%exception%', $e->getMessage())
                    ->code('%at%', '[' . implode('][', $this->locator) . ']');
            }
            throw new RouterPropertyException(
                $message->toString()
            );
        }
    }

    private function asserts(): void
    {
        $this->locator[] = 'array';
        foreach ($this->value as $pathUri => $meta) {
            $this->locator[] = (string) $pathUri;
            $this->assertString($pathUri);
            // $this->assertMeta($meta);
            $this->check[] = array_key_last($this->locator);
        }
    }

    private function assertMeta(): void
    {
    }
}
