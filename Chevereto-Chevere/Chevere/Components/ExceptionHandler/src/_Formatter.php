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

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// declare(strict_types=1);

// namespace Chevere\Components\ExceptionHandler\src;

// use Chevere\Components\App\Instances\BootstrapInstance;
// use Chevere\Components\VarDump\Dumpeable;
// use Chevere\Components\VarDump\Formatters\DumperFormatter;
// use Chevere\Components\VarDump\Formatters\PlainFormatter;
// use Chevere\Components\VarDump\VarDump;

// /**
//  * Formats the error exception in HTML (default), console and plain text.
//  */
// final class Formatter
// {
//     private function processContentGlobals()
//     {
//         // $globals = $this->exceptionHandler->request()->globals()->globals();
//         $globals = $GLOBALS;
//         foreach (['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER'] as $global) {
//             $val = $globals[$global] ?? null;
//             if (!empty($val)) {
//                 $dumperVarDump = (new VarDump(new Dumpeable($val), new DumperFormatter()))->withProcess();
//                 $plainVarDump = (new VarDump(new Dumpeable($val), new PlainFormatter()))->withProcess();
//                 $wrapped = $dumperVarDump->toString();
//                 if (!BootstrapInstance::get()->isCli()) {
//                     $wrapped = '<pre>' . $wrapped . '</pre>';
//                 }
//                 $this->setRichContentSection($global, ['$' . $global, $this->wrapStringHr($wrapped)]);
//                 $this->setPlainContentSection($global, ['$' . $global, strip_tags($this->wrapStringHr($plainVarDump->toString()))]);
//             }
//         }
//     }

//     /**
//      * @param string $text text to wrap
//      *
//      * @return string wrapped text
//      */
//     private function wrapStringHr(string $text): string
//     {
//         return $this->lineBreak . "\n" . $text . "\n" . $this->lineBreak;
//     }
// }
