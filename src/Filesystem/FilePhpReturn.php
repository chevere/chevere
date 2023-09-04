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

namespace Chevere\Filesystem;

use Chevere\Filesystem\Exceptions\FileInvalidContentsException;
use Chevere\Filesystem\Exceptions\FileNotExistsException;
use Chevere\Filesystem\Exceptions\FileUnableToGetException;
use Chevere\Filesystem\Exceptions\FileWithoutContentsException;
use Chevere\Filesystem\Interfaces\FilePhpInterface;
use Chevere\Filesystem\Interfaces\FilePhpReturnInterface;
use Chevere\String\StringAssert;
use Chevere\VariableSupport\Interfaces\StorableVariableInterface;
use Chevere\VariableSupport\StorableVariable;
use Throwable;
use function Chevere\Message\message;

final class FilePhpReturn implements FilePhpReturnInterface
{
    public function __construct(
        private FilePhpInterface $filePhp
    ) {
    }

    public function filePhp(): FilePhpInterface
    {
        return $this->filePhp;
    }

    public function get(): mixed
    {
        $this->assert();
        $filePath = $this->filePhp->file()->path()->__toString();

        return require $filePath;
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function getArray(): array
    {
        /** @var array<mixed> */
        return $this->get();
    }

    public function getBoolean(): bool
    {
        /** @var boolean */
        return $this->get();
    }

    public function getFloat(): float
    {
        /** @var float */
        return $this->get();
    }

    public function getInteger(): int
    {
        /** @var int */
        return $this->get();
    }

    public function getObject(): object
    {
        /** @var object */
        return $this->get();
    }

    public function getString(): string
    {
        /** @var string */
        return $this->get();
    }

    public function put(StorableVariableInterface $storable): void
    {
        $variable = $storable->variable();
        $export = $this->getFileReturnVariable($variable);
        $this->filePhp->file()->put(
            self::PHP_RETURN . $export . ';'
        );
    }

    /**
     * @throws FileNotExistsException
     * @throws FileUnableToGetException
     * @throws FileWithoutContentsException
     * @throws FileInvalidContentsException
     */
    private function assert(): void
    {
        $contents = $this->filePhp->file()->getContents();

        try {
            (new StringAssert($contents))->notEmpty()->notCtypeSpace();
        } catch (Throwable) {
            throw new FileWithoutContentsException(
                message("The file at %path% doesn't have any contents")
                    ->withCode('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
        if (preg_match('#^<\?php[\S\s]*return[\S\s]*;$#', $contents) !== 1) {
            throw new FileInvalidContentsException(
                message('Unexpected contents in %path%')
                    ->withCode('%path%', $this->filePhp->file()->path()->__toString())
            );
        }
    }

    private function getFileReturnVariable(mixed $variable): mixed
    {
        return (new StorableVariable($variable))->toExport();
    }
}
