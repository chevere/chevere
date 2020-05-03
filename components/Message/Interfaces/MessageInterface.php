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

namespace Chevere\Components\Message\Interfaces;

use Ahc\Cli\Output\Color;
use Chevere\Components\Common\Interfaces\ToStringInterface;

/**
 * The Chevere Message
 *
 * Creates rich system messages for CLI and HTML.
 */
interface MessageInterface extends ToStringInterface
{
    const CLI_TABLE = [
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

    const HTML_TABLE = [
        'emphasis' => 'em',
        'underline' => 'u',
    ];

    /**
     * @param string $template A message template, i.e: `Disk %foo% is %percent% full`
     */
    public function __construct(string $template);

    /**
     * Provides access to the template property.
     */
    public function template(): string;

    /**
     * Provides access to the trTable property.
     */
    public function trTable(): array;

    /**
     * @return string A CLI version of the message
     */
    public function toConsole(): string;

    /**
     *  @return string A HTML version of the message
     */
    public function toHtml(): string;

    /**
     *  @return string A text version of the message.
     */
    public function toString(): string;

    /**
     * Return an instance with the specified string translation.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified string translation.
     *
     * @param string $search The search string
     * @param string $replace The new value
     */
    public function strtr(string $search, string $replace): MessageInterface;

    /**
     * Return an instance with the specified emphasis.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified emphasis.
     *
     * @param string $search The search string
     * @param string $replace The new value
     */
    public function emphasis(string $search, string $replace): MessageInterface;

    /**
     * Return an instance with the specified strong.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified strong.
     *
     * @param string $search The search string
     * @param string $replace The new value
     */
    public function strong(string $search, string $replace): MessageInterface;

    /**
     * Return an instance with the specified underline.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified underline.
     *
     * @param string $search The search string
     * @param string $replace The new value
     */
    public function underline(string $search, string $replace): MessageInterface;

    /**
     * Return an instance with the specified code.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified code.
     *
     * @param string $search The search string
     * @param string $replace The new value
     */
    public function code(string $search, string $replace): MessageInterface;
}
