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

namespace Chevere\Hooking;

use InvalidArgumentException;
use Chevere\Path\Path;
use Chevere\Message\Message;
use Chevere\Path\PathHandle;

/**
 * Hooks refers to code that gets injected at determinated sections of the
 * system identified by the hookable id.
 *
 * Hookables are identified using the following nomenclature:
 * <anchor>@<dirname>:<file_name>
 *
 * 1. <anchor>: The named hookeable section
 * 2. <dirname>: Hookeable path (relative to App\PATH)
 * 3. <file_name>: Hookeable file_name
 *
 * Hooks gets registered on self::$hook following this structure:
 *
 * string <file>
 *     <anchor> => array
 *         <position> => array
 *             <priority> => array
 *                 <n> => array
 *                     callable => object (Closure)
 *                     maker => array (size=5)
 *                         file => <file>
 *                         line => <line>
 *                         method => <method>
 *
 * A file can contain multiple anchors, which index by hookable position
 * (before, after).
 *
 * Maker contains info about the function call used to create the hook.
 */

// Hook::bind('myHook@controller:file', Hook::BEFORE, function ($that) {
//     $that->source .= ' nosehaceeso no';
// });
final class Hook
{
    const ALIAS_PATH_CORE = 'core>';
    const ANCHOR = 'anchor';
    const FILE = 'file';
    const CALLABLE = 'callable';
    const MAKER = 'maker';
    const BEFORE = 'before';
    const AFTER = 'after';
    const DEFAULT_PRIORITY = 10;

    private static $hooks;

    public static function getAll()
    {
        return self::$hooks;
    }

    /**
     * Hook a hookable entry (before). Shorthand of bind().
     *
     * @see bind()
     *
     * @param string   $id       hookable id
     * @param callable $callable callable to run
     * @param int      $priority Priority in which this should be called. Lower the number, higher the priority.
     *                           If the priority is already taken, it gets added based on inclusion order.
     */
    public static function before(string $id, callable $callable, int $priority = null): void
    {
        self::bind($id, $callable, $priority, self::BEFORE);
    }

    /**
     * Hook a hookable entry (after). Shorthand of bind().
     *
     * @see bind()
     *
     * @param string   $id       hookable id
     * @param callable $callable callable to run
     * @param int      $priority Priority in which this should be called. Lower the number, higher the priority.
     *                           If the priority is already taken, it gets added based on inclusion order.
     */
    public static function after(string $id, callable $callable, int $priority = null): void
    {
        self::bind($id, $callable, $priority, self::AFTER);
    }

    /**
     * Stock hook definition in hook table (internal method).
     *
     * @param string   $id       hookable id
     * @param callable $callable callable to run
     * @param int      $priority Priority in which this should be called. Lower the number, higher the priority.
     *                           If the priority is already taken, it gets added based on inclusion order.
     *
     * @see before()
     * @see after()
     */
    private static function bind(string $id, callable $callable, int $priority = null, string $pos): void
    {
        $parsed = self::parseIdentifier($id);
        extract($parsed);
        $hook = [
            self::CALLABLE => $callable,
            self::MAKER => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1],
        ];
        $priority = $priority ?? self::DEFAULT_PRIORITY;
        $f = ${self::FILE};
        $a = ${self::ANCHOR};
        $priority_exists = isset(self::$hooks[$f][$a][$pos][$priority]);
        self::$hooks[$f][$a][$pos][$priority][] = $hook;
        if (!$priority_exists) {
            ksort(self::$hooks[$f][$a][$pos]);
        }
    }

    /**
     * Parse hookable identifier.
     *
     * @param string $id    Hookable identifier
     * @param int    $trace backtrace caller id for :path resolution
     *
     * @return array ['anchor' => '<anchor>', 'file' => '<dirname>/<file_name>.php']
     */
    private static function parseIdentifier(string $id, int $trace = 3): array
    {
        if (false !== strpos($id, '@')) {
            $anchored = explode('@', $id);
            $anchor = $anchored[0];
            $pathIdentifier = $anchored[1];
        } else {
            $pathIdentifier = $id;
        }

        return [
            self::ANCHOR => $anchor ?? null,
            self::FILE => (new PathHandle($pathIdentifier))->path(),
        ];
    }

    /**
     * Get all hooks for the given file, anchor and position.
     *
     * @param string $file   hookeable file
     * @param string $anchor hookable anchor
     * @param string $pos    hookable position (before, after)
     *
     * @return array an array containing al the callables in order
     */
    public static function getAt(string $file, string $anchor, string $pos = null): array
    {
        if (self::$hooks == null || !isset(self::$hooks[$file])) {
            return [];
        }
        $numArgs = func_num_args();
        switch ($numArgs) {
            case 2:
                return self::$hooks[$file][$anchor] ?? [];
            case 3:
                if (!in_array($pos, [self::BEFORE, self::AFTER])) {
                    throw new InvalidArgumentException(
                        (new Message('Invalid %s argument value, expecting %b, %a.'))
                            ->code('%s', '$pos')
                            ->code('%b', self::BEFORE)
                            ->code('%a', self::AFTER)
                            ->toString()
                    );
                }

                return self::$hooks[$file][$anchor][$pos] ?? [];
        }

        return [];
    }

    /**
     * Execute all hooks for the given anchor (before and after).
     *
     * @param string   $anchor   hookable anchor
     * @param callable $callable
     * @param object   $that     that is this
     *
     * @see Hookeable
     */
    public static function exec(string $anchor, callable $callable, object $that = null): void
    {
        $file = self::getCallerFile();
        $file ? self::execAt($file, $anchor, self::BEFORE, $that) : null;
        $callable($that);
        $file ? self::execAt($file, $anchor, self::AFTER, $that) : null;
    }

    /**
     * Exec all before hooks for the given anchor.
     *
     * @param string   $anchor   hookable anchor
     * @param callable $callable
     * @param object   $that     that is this
     *
     * @see Hookeable
     */
    public static function execBefore(string $anchor, callable $callable, object $that = null): void
    {
        $file = self::getCallerFile();
        if (isset($file)) {
            self::execAt($file, $anchor, self::BEFORE, $that);
        }
        $callable($that);
    }

    /**
     * Exec all after hooks for the given anchor.
     *
     * @param string   $anchor   hookable anchor
     * @param callable $callable
     * @param object   $that     that is this
     *
     * @see Hookeable
     */
    public static function execAfter(string $anchor, callable $callable, object $that = null): void
    {
        $callable($that);
        $file = self::getCallerFile();
        if (isset($file)) {
            self::execAt($file, $anchor, self::AFTER, $that);
        }
    }

    private static function getCallerFile(): ?string
    {
        // 0:Hook, 1:Hookable, 3:Caller
        $file = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['file'];
        return (new Path($file))->absolute();
    }

    /**
     * Execute hooks for given file, anchor and position.
     *
     * @param string $file   hookeable file
     * @param string $anchor Hookable anchor
     * @param string $pos    Hookable position
     * @param object $that   that is this
     *
     * @see Hookeable
     */
    public static function execAt(string $file, string $anchor, string $pos, object $that = null): void
    {
        $hooks = self::getAt($file, $anchor, $pos);
        if (!isset($hooks)) {
            return;
        }
        foreach ($hooks as $priority => $entries) {
            foreach ($entries as $entry) {
                $entry[self::CALLABLE]($that);
            }
        }
    }

    /**
     * Exec before hooks for the given file and anchor.
     *
     * Shorthand for execAt().
     *
     * @param string $file   hookeable file
     * @param string $anchor Hookable anchor
     * @param object $that   that is this
     *
     * @see execAt()
     * @see Hookeable
     */
    public static function execBeforeAt(string $file, string $anchor, object $that = null): void
    {
        self::execAt($file, $anchor, self::BEFORE, $that);
    }

    /**
     * Exec after hooks for the given file and anchor.
     *
     * Shorthand for execAt().
     *
     * @param string $file   hookeable file
     * @param string $anchor Hookable anchor
     * @param object $that   that is this
     *
     * @see execAt()
     * @see Hookeable
     */
    public static function execAfterAt(string $file, string $anchor, object $that = null): void
    {
        self::execAt($file, $anchor, self::AFTER, $that);
    }
}
