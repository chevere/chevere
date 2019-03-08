<?php declare(strict_types=1);
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
 * Simple OPcache utility library.
 *
 * Provides a simple way to access OPcache procedural functions.
 */
class OPcache
{
    public $filename;
    protected static $enabled;
    /**
     * @param string $filename Filename to cache.
     */
    public function __construct(string $filename)
    {
        // We handle enabled as static so it does the thing just one time.
        if (static::$enabled == false) {
            static::isEnabled();
        }
        $this->filename = $filename;
    }
    /**
     * Apply OPcache in the target filename. It touches, invalidates and compile.
     */
    public function cache() : bool
    {
        $this->touch();
        $this->invalidate();
        return $this->compile();
    }
    /**
     * Checks if cache exists in the target filename.
     */
    public function check() : bool
    {
        return opcache_is_script_cached($this->filename);
    }
    /**
     * Invalidates the target filename cache.
     *
     * @param bool $force True to force opcache_invalidate.
     */
    public function invalidate(bool $force = false) : bool
    {
        return opcache_invalidate($this->filename, $force);
    }
    /**
     * Touch the filename so it makes possible to use OPcache in the target filename.
     */
    public function touch() : bool
    {
        return touch($this->filename, time() - 86400); // 86400s = 24h = 1d
    }
    /**
     * OPcache compile the target filename.
     */
    public function compile() : bool
    {
        return opcache_compile_file($this->filename);
    }
    /**
     * Check if OPcache is enabled.
     */
    public static function isEnabled() : bool
    {
        return static::$enabled = extension_loaded('Zend OPcache');
    }
    /**
     * Alias of opcache_reset().
     */
    public static function reset() : bool
    {
        return opcache_reset();
    }
}
