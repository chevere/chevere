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

namespace Chevere\Components\Console;

use Ahc\Cli\Application as CliApplication;
use Ahc\Cli\Exception\RuntimeException;
use Chevere\Components\ExceptionHandler\Documents\ConsoleDocument;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\ExceptionHandler\ExceptionRead;

final class Application extends CliApplication
{
    /**
     * @codeCoverageIgnore
     */
    public function handle(array $argv)
    {
        if (count($argv) < 2) {
            return $this->showHelp();
        }
        $exitCode = 255;
        try {
            $command = $this->parse($argv);
            $result = $this->doAction($command);
            $exitCode = is_int($result) ? $result : 0;
        } catch (RuntimeException $e) {
            $this->io()->writer()->error($e->getMessage())->eol();
        } catch (\Exception $e) {
            $handler = new ExceptionHandler(new ExceptionRead($e));
            $document = new ConsoleDocument($handler);
            $this->io()->writer()->error($document->toString())->eol();
        } catch (\Throwable $e) {
            $this->outputHelper()->printTrace($e);
        }

        return ($this->onExit)($exitCode);
    }
}
