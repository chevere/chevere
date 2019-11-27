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
use Chevere\Components\Type\Type;
use Chevere\Contracts\Router\Properties\IndexPropertyContract;
use Chevere\Contracts\Type\TypeContract;
use Exception;
use Throwable;
use TypeError;

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
            $arrayKey = array_key_last($this->locator);
            $this->assertString($pathUri);
            $this->locator[] = 'meta';
            $metakey = array_key_last($this->locator);
            $this->assertArrayNotEmpty($meta);
            $this->assertMeta($meta);
            $this->check[] = $metakey;
            $this->check[] = $arrayKey;
        }
    }

    private function assertMeta(array $meta): void
    {
        foreach ([
            'id' => [TypeContract::INTEGER],
            'group' => [TypeContract::STRING],
            'name' => [TypeContract::NULL, TypeContract::STRING],
        ] as $key => $acceptTypes) {
            if (!array_key_exists($key, $meta)) {
                throw new Exception(
                    (new Message('Missing array key %key% (type %type%)'))
                        ->code('%key%', $key)
                        ->code('%type%', gettype($key))
                        ->toString()
                );
            }
            $this->locator[] = $key;
            $errors = [];
            $hit = 0;
            foreach ($acceptTypes as $type) {
                if (!(new Type($type))->validate($meta[$key])) {
                    $errors[] = (new Message('Expected type %type%, type %provided% provided'))
                        ->code('%type%', $type)
                        ->code('%provided%', gettype($meta[$key]))
                        ->toString();
                } else {
                    ++$hit;
                }
            }
            if (0 == $hit) {
                throw new TypeError($errors[0]);
            }
            $this->check[] = array_key_last($this->locator);
        }
    }
}
