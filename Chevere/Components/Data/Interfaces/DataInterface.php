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

namespace Chevere\Components\Data\Interfaces;

use Chevere\Components\Common\Interfaces\ToArrayInterface;

interface DataInterface extends ToArrayInterface
{
    public function __construct(array $data);

    /**
     * Return an instance with the specified array.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified array.
     *
     * @param array $data An array which will replace the existing one.
     */
    public function withArray(array $data): DataInterface;

    /**
     * Return an instance with the specified array (merged with existent).
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified array (merged with existent).
     *
     * @param array $data An array which will merged with the existing one.
     */
    public function withMergedArray(array $data): DataInterface;

    /**
     * Return an instance with the specified variable appended.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the append variable.
     *
     * @param mixed $var The variable to append.
     */
    public function withAppend($var): DataInterface;

    /**
     * Return an instance with the specified key-var.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the key-var.
     *
     * @param string $var The key to add.
     * @param mixed $var The variable to add.
     */
    public function withAddedKey(string $key, $var): DataInterface;

    /**
     * Return an instance with the specified key removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that not contains the removed key.
     *
     * @param string $var The key to remove.
     */
    public function withRemovedKey(string $key): DataInterface;

    /**
     * Returns a boolean indicating whether the data is empty.
     */
    public function isEmpty(): bool;

    /**
     * Get the number of data array members.
     */
    public function count(): int;

    /**
     * Returns a boolean indicating whether the data has the given key.
     */
    public function hasKey(string $key): bool;

    /**
     * Get the alleged key.
     *
     * @param string $key The key to retrive.
     * @return mixed The value corresponding to $key.
     */
    public function key(string $key);
}
