<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Cache;

use InvalidArgumentException;
use Chevere\Message;
use Chevere\Path\Path;

final class Key
{
    const ILLEGAL_CHARACTERS = '\.\/\\\~\:';

    /** @var string Cache identifier (folder) */
    private $key;

    /** @var string Cache path for the given $key */
    private $path;

    public function __construct(string $key)
    {
        $this->validate($key);
        $this->key = $key;
    }
    
    /**
     * @return string Cache file path for the given $name
     */
    public function genFileIdentifier(string $name): string
    {
        $this->validate($name);
        return 'cache/' . $this->key . ':' . $name;
    }


    private function validate(string $key): void
    {
        if (preg_match_all('#['.static::ILLEGAL_CHARACTERS.']#', $key, $matches)) {
            $matches = array_unique($matches[0]);
            $forbidden = implode(', ', $matches);
            throw new InvalidArgumentException(
                (new Message('Use of forbidden character %forbidden%.'))
                    ->code('%forbidden%', $forbidden)
                    ->toString()
            );
        }
    }
}
