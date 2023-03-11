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

namespace Chevere\Message\Interfaces;

use Stringable;

/**
 * Describes the component in charge of handling rich system messages for CLI and HTML.
 */
interface MessageInterface extends Stringable
{
    public const CLI_TABLE = [
        'message_emphasis' => 'italic',
        'message_strong' => 'bold',
        'message_underline' => 'underline',
        'message_code' => 'reverse',
    ];

    public const HTML_TABLE = [
        'emphasis' => 'em',
        'underline' => 'u',
    ];

    /**
     *  Returns a text message representation.
     */
    public function __toString(): string;

    /**
     * Provides access to the message template.
     */
    public function template(): string;

    /**
     * Provides access to the message translation table.
     *
     * @return array<string, string[]>
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
     * Return an instance with the specified string translation.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified string translation.
     */
    public function withTranslate(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` emphasis tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` emphasis tag.
     */
    public function withEmphasis(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` as strong tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` as strong tag.
     */
    public function withStrong(string $search, string $replace): self;

    /**
     * Return an instance with the specified underline.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified underline.
     */
    public function withUnderline(string $search, string $replace): self;

    /**
     * Return an instance with the specified `$search` replaced with `$replace` as code tag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified `$search` replaced with `$replace` as code tag.
     */
    public function withCode(string $search, string $replace): self;
}
