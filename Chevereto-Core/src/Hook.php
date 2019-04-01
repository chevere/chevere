<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

use Exception;

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
 * Hooks gets registered on static::$hook following this structure:
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
class Hook
{
    const ALIAS_PATH_CORE = 'core>';
    const ANCHOR = 'anchor';
    const FILE = 'file';
    const CALLABLE = 'callable';
    const MAKER = 'maker';
    const BEFORE = 'before';
    const AFTER = 'after';
    const DEFAULT_PRIORITY = 10;

    protected static $hooks;

    public static function getAll()
    {
        return static::$hooks;
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
        static::bind($id, $callable, $priority, static::BEFORE);
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
        static::bind($id, $callable, $priority, static::AFTER);
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
    protected static function bind(string $id, callable $callable, int $priority = null, string $pos): void
    {
        $parsed = static::parseIdentifier($id);
        extract($parsed);
        $hook = [
            static::CALLABLE => $callable,
            static::MAKER => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1],
        ];
        $priority = $priority ?? self::DEFAULT_PRIORITY;
        $f = ${static::FILE};
        $a = ${static::ANCHOR};
        $priority_exists = isset(static::$hooks[$f][$a][$pos][$priority]);
        static::$hooks[$f][$a][$pos][$priority][] = $hook;
        if (!$priority_exists) {
            ksort(static::$hooks[$f][$a][$pos]);
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
    protected static function parseIdentifier(string $id, int $trace = 3): array
    {
        if (Utils\Str::contains('@', $id)) {
            $anchored = explode('@', $id);
            $anchor = $anchored[0];
            $pathIdentifier = $anchored[1];
        } else {
            $pathIdentifier = $id;
        }

        return [
            static::ANCHOR => $anchor ?? null,
            static::FILE => Path::fromHandle($pathIdentifier),
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
        if (static::$hooks == null || !isset(static::$hooks[$file])) {
            return [];
        }
        $numArgs = func_num_args();
        switch ($numArgs) {
            case 2:
                return static::$hooks[$file][$anchor] ?? [];
            break;
            case 3:
                if (!in_array($pos, [static::BEFORE, static::AFTER])) {
                    throw new Exception(
                        (new Message('Invalid %s argument value, expecting %b, %a.'))
                            ->code('%s', '$pos')
                            ->code('%b', static::BEFORE)
                            ->code('%a', static::AFTER)
                    );
                }

                return static::$hooks[$file][$anchor][$pos] ?? [];
            break;
        }
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
        $file = static::getCallerFile();
        $file ? static::execAt($file, $anchor, static::BEFORE, $that) : null;
        $callable($that);
        $file ? static::execAt($file, $anchor, static::AFTER, $that) : null;
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
        if ($file = static::getCallerFile()) {
            static::execAt($file, $anchor, static::BEFORE, $that);
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
        if ($file = static::getCallerFile()) {
            static::execAt($file, $anchor, static::AFTER, $that);
        }
    }

    protected static function getCallerFile(): ?string
    {
        // 0:Hook, 1:Hookable, 3:Caller
        return Path::normalize(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['file']);
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
        $hooks = Hook::getAt($file, $anchor, $pos);
        if (!isset($hooks)) {
            return;
        }
        foreach ($hooks as $priority => $entries) {
            foreach ($entries as $entry) {
                $entry[static::CALLABLE]($that);
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
        static::execAt($file, $anchor, static::BEFORE, $that);
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
        static::execAt($file, $anchor, static::AFTER, $that);
    }
}
