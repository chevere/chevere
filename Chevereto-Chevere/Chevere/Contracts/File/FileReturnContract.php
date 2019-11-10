<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\File;

use Chevere\Components\File\Exceptions\FileNotFoundException;
use Chevere\Components\File\Exceptions\FileNotPhpException;

interface FileReturnContract
{
    const PHP_RETURN = "<?php\n\nreturn ";
    const PHP_RETURN_CHARS = 14;
    const CHECKSUM_ALGO = 'sha256';

    /**
     * Creates a new instance.
     * 
     * @throws FileNotFoundException If the file doesn't exists.
     * @throws FileNotPhpException If the file is not PHP.
     */
    public function __construct(FilePhpContract $file);

    /**
     * Return an instance with no-strict flag.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified no-strict flag.
     */
    public function withNoStrict(): FileReturnContract;

    /**
     * Provides access to the FileContract instance container in the FilePhpContract.
     */
    public function file(): FileContract;

    /**
     * Retrieves the file checksum using the CHECKSUM_ALGO algorithm. 
     */
    public function checksum(): string;

    /**
     * Retrieves the file contents. 
     */
    public function contents(): string;

    /**
     * Retrieves the file return. 
     */
    public function return();

    /**
     * Retrieves the content of the file appling unserialize.
     */
    public function get();

    /**
     * Put $var into the file using var_export return
     */
    public function put($var): void;
}
