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
 * Signed cache utility for internal components.
 *
 * This works in the filesystem and uses OPcache to access shared memory. Using files as signees,
 * it generates two files: The actual cached file and its signature.
 *
 * - The cached file contains the PHP code you want to cache.
 * - The signature is a md5-generated string that contains the target cached filemtime.
 *
 * If the filemtime contained in the signature doesn't match the cached file or if the signees filemtime changes,
 * the cache gets invalidated.
 */
class SignedCache
{
    const DIR = App\PATH_CACHE;
    const HASH_FUNCTION = 'md5';

    protected $mode;
    protected $dir;
    protected $signFilename;

    public $filename;
    public $contents;
    public $signature;
    public $signerArray = [];

    /**
     * Constructs the cache object.
     */
    public function __construct()
    {
        // This class and the caller are always added to signerArray
        $this->signerArray = [Core::getClassFilename($this), debug_backtrace()[0]['file']];
    }

    /**
     * Sets the cache signature.
     *
     * @param string $signature signature string
     */
    public function setSignature(string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * @param string $filename signer file
     */
    public function addSigner(string $filename): self
    {
        $this->signerArray[] = $filename;

        return $this;
    }

    /**
     * Sets the filename to write the cache to.
     *
     * @param string $filename Basename of the file bla bla TODO: Check this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        $dir = dirname($filename);
        $this->dir = Path::tailDir($dir);

        return $this;
    }

    /**
     * Sets the contents of the cache.
     *
     * @param string $contents contents to be cached
     */
    public function setContents($contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Saves the cache.
     *
     * @throws Exception
     */
    public function save(): void
    {
        if ($this->signFilename == null) {
            $this->setSignFilename();
        }
        $this->writer($this->filename, "<?php\n\nreturn ".var_export($this->contents, true).';');
        $filemtime = filemtime($this->filename);
        if ($filemtime === false) {
            throw new Exception('Unable to retrieve filemtime.');
        }
        $this->writer($this->signFilename, (string) $filemtime);
    }

    /**
     * Cache writer.
     *
     * @param string $filename filename to write
     * @param string $contents $contents Contents to write to filename
     */
    protected function writer(string $filename, string $contents): void
    {
        $fh = fopen($filename, 'w');
        if (!$fh) {
            throw new Exception(
                (new Message('Unable to open (w) %s for cache'))->code('%s', $filename)
            );
        }
        if (!flock($fh, LOCK_EX)) {
            throw new Exception(
                (new Message('Unable to %s file %f for cache'))
                    ->code('%s', 'LOCK_EX')
                    ->code('%f', $filename)
            );
        }
        $fwrite = fwrite($fh, $contents);
        flock($fh, LOCK_UN);
        fclose($fh);
        if (!$fwrite) {
            throw new Exception(
                (new Message('Unable to write cache to %s'))->code('%s', $filename)
            );
        }
        if (OPcache::isEnabled()) {
            (new OPcache($filename))->cache();
        }
    }

    /**
     * Check if cache signature contents (filemtime) matches the target filename filemtime.
     *
     * @return bool TRUE if the cache exists
     */
    public function check(): bool
    {
        if ($this->signFilename == null) {
            $this->setSignFilename();
        }
        if (!File::exists($this->signFilename)) {
            return false;
        }
        $signFilemtime = file_get_contents($this->signFilename);
        $cacheFilemtime = filemtime($this->filename);

        return $signFilemtime == $cacheFilemtime;
    }

    /**
     * Set signature filename.
     */
    protected function setSignFilename(): void
    {
        $errors = [];
        $signatures = [];
        if (!isset($this->signature)) {
            $this->signature = null;
            foreach ($this->signerArray as $signer) {
                if (!File::exists($signer)) {
                    $errors[] = $signer;
                    continue;
                }
                $signatures[] = $this->hashFile($signer);
            }
        }
        if ($errors) {
            // FIXME: Usar new Message()
            $message = 'Missing file '.implode('</code>, <code>', $errors);
            throw new Exception($message);
        }
        $this->signature = implode('.', $signatures);
        $this->signFilename = $this->dir.$this->getKey();
    }

    /**
     * Generates the cache key.
     *
     * @return string a hash generated using several file-related signatures
     */
    protected function getKey(): string
    {
        $signerFilemtime = null;
        foreach ($this->signerArray as $file) {
            if (!File::exists($file)) {
                throw new Exception(
                    (new Message("File %s doesn't exists"))->code('%s', $file)
                );
            }
            $signerFilemtime .= filemtime($file);
        }
        $app = App::instance();
        $appHash = $app->getHash();

        return $this->hash($signerFilemtime.$this->signature.$appHash);
    }

    /**
     * Hashes a string using static::HASH_FUNCTION.
     *
     * @param string $string target string to hash
     *
     * @return string hashed string
     */
    protected function hash(string $string): string
    {
        return (static::HASH_FUNCTION)($string);
    }

    /**
     * Get a hashed representation of the file contents.
     *
     * @param string $filename target file path
     *
     * @return string hashed fle contents
     */
    protected function hashFile(string $filename): string
    {
        if ($fgc = file_get_contents($filename)) {
            return $this->hash($fgc);
        } else {
            throw new Exception('Unable to file_get_contents.');
        }
    }

    /**
     * Clear the cache by removing all files in the cache working directory.
     *
     * @throws Exception
     */
    public function clear(): void
    {
        if (!File::exists($this->dir, true)) {
            mkdir($this->dir);
        } else {
            File::removeAll($this->dir);
        }
    }
}
