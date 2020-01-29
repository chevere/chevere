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

namespace Chevere\Components\Router\Properties;

use Exception;
use TypeError;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Properties\Traits\ToArrayTrait;
use Chevere\Components\Type\Type;
use Chevere\Components\Router\Interfaces\Properties\IndexPropertyInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;

final class IndexProperty extends PropertyBase implements IndexPropertyInterface
{
    use ToArrayTrait;

    /**
     * Creates a new instance.
     *
     * @param array $index [(string)$key => ['id' => (int)$id, 'group' => (string)$group, 'name' => (string)$name]][]
     *
     * @throws RouterPropertyException if the value doesn't match the property format
     */
    public function __construct(array $index)
    {
        $this->value = $index;
        $this->tryAsserts();
    }

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
            'id' => [TypeInterface::INTEGER],
            'group' => [TypeInterface::STRING],
            'name' => [TypeInterface::NULL, TypeInterface::STRING],
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
