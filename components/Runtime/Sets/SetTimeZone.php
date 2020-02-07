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

namespace Chevere\Components\Runtime\Sets;

use InvalidArgumentException;
use RuntimeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Interfaces\SetInterface;
use Exception;
use Throwable;

/**
 * Sets the default timezone
 */
final class SetTimeZone implements SetInterface
{
    use SetTrait;

    /**
     * Creates a new instance
     *
     * @param string $value A timezone identifier to pass to `date_default_timezone_set`
     * @throws InvalidArgumentException If the $value is not a valid timezone identifier
     * @throws RuntimeException If unable to set the value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        if (date_default_timezone_get() == $this->value) {
            return;
        }
        $this->assertArgument();
        $this->assertSetTimeZone();
    }

    private function assertArgument(): void
    {
        if ('UTC' != $this->value && !$this->validateTimezone()) {
            throw new InvalidArgumentException(
                (new Message('Invalid timezone value %timezone%'))
                    ->code('%timezone%', $this->value)
                    ->toString()
            );
        }
    }

    private function validateTimezone(): bool
    {
        $return = false;
        $list = timezone_abbreviations_list();
        foreach ($list as $zone) {
            foreach ($zone as $item) {
                $tz = $item['timezone_id'] ?? null;
                if (isset($tz) && $this->value == $tz) {
                    $return = true;
                    break 2;
                }
            }
        }

        return $return;
    }

    private function assertSetTimeZone(): void
    {
        if (!@date_default_timezone_set($this->value)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                (new Message('False return on %s(%v) %thrown%'))
                    ->code('%s', 'date_default_timezone_set')
                    ->code('%v', $this->value)
                    // ->code('%thrown%', $e->getMessage())
                    ->toString()
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
