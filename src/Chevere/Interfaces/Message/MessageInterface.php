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

namespace Chevere\Interfaces\Message;

use Ahc\Cli\Output\Color;
use Chevere\Interfaces\Common\ToStringInterface;

/**
 * Describes the component in charge of handling rich system messages for CLI and HTML.
 */
interface MessageInterface extends ToStringInterface
{
    public const CLI_TABLE = [
        'emphasis' => [
            'bold' => 3,
        ],
        'strong' => [
            'bold' => 1,
        ],
        'underline' => [
            'bold' => 4,
        ],
        'code' => [
            'bg' => Color::WHITE,
            'fg' => Color::BLACK,
        ],
    ];

    public const HTML_TABLE = [
        'emphasis' => 'em',
        'underline' => 'u',
    ];

    /**
     * @param string $template A message template, i.e: `Disk %foo% is %percent% full`
     */
    public function __construct(string $template);

    /**
     * Provides access to the message template.
     */
    public function template(): string;

    /**
     * Provides access to the message translation table.
     */
    public function trTable(): array;

    /**
     * Returns a console message representation.
     */
    public function toConsole(): string;

    /**
     * Returns a HTML message representation.
     */
    public function toHtml(): string;

    /**
     *  Returns a text message representation.
     */
    public function toString(): string;

    /**
     * Return an instance with the specified string translation.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified string translation.
     */
    public function strtr(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` emphasis tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` emphasis tag.
     */
    public function emphasis(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` as strong tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` as strong tag.
     */
    public function strong(string $search, string $replace): self;

    /**
     * Return an instance with the specified underline.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified underline.
     */
    public function underline(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` as code tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` as code tag.
     */
    public function code(string $search, string $replace): self;
}
