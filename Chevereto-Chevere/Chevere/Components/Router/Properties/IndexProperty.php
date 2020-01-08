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
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Components\Type\Type;
use Chevere\Components\Router\Contracts\Properties\IndexPropertyContract;
use Chevere\Components\Type\Contracts\TypeContract;
use Exception;
use TypeError;

final class IndexProperty extends PropertyBase implements IndexPropertyContract
{
    use ToArrayTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $index)
    {
        $this->value = $index;
        $this->tryAsserts();
    }

    /**
     * {@inheritdoc}
     */
    protected function asserts(): void
    {
        $this->assertArrayNotEmpty($this->value);
        $this->breadcrum = $this->breadcrum
            ->withAddedItem('array');
        foreach ($this->value as $pathUri => $meta) {
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $pathUri);
            $pos = $this->breadcrum->pos();
            $this->assertString($pathUri);
            $this->breadcrum = $this->breadcrum
                ->withAddedItem('array');
            $metaPos = $this->breadcrum->pos();
            $this->assertArrayNotEmpty($meta);
            $this->assertMeta($meta);
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($metaPos)
                ->withRemovedItem($pos);
        }
    }

    private function assertMeta(array $meta): void
    {
        foreach ([
            'id' => [TypeContract::INTEGER],
            'group' => [TypeContract::STRING],
            'name' => [TypeContract::NULL, TypeContract::STRING],
        ] as $key => $acceptTypes) {
            $this->assertMetaKey($key, $meta);
            $this->breadcrum = $this->breadcrum
                ->withAddedItem((string) $key);
            $pos = $this->breadcrum->pos();
            $error = '';
            $hit = 0;
            foreach ($acceptTypes as $type) {
                if (!(new Type($type))->validate($meta[$key])) {
                    $error = $this->getBadTypeMessage()
                        ->code('%for%', $key)
                        ->code('%expected%', implode(' | ', $acceptTypes))
                        ->code('%provided%', gettype($meta[$key]))
                        ->toString();
                    continue;
                }
                ++$hit;
            }
            if (0 == $hit) {
                throw new TypeError($error);
            }
            $this->breadcrum = $this->breadcrum
                ->withRemovedItem($pos);
        }
    }

    private function assertMetaKey(string $key, array $meta): void
    {
        if (!array_key_exists($key, $meta)) {
            throw new Exception(
                (new Message('Missing array key %key% (type %type%)'))
                    ->code('%key%', $key)
                    ->code('%type%', gettype($key))
                    ->toString()
            );
        }
    }
}
