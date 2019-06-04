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

namespace Chevereto\Chevere\ErrorHandler;

/**
 * Stores the template strings used by ErrorHandler.
 */
class Template
{
    /** @var string Title used when debug is disabled (App config) */
    const NO_DEBUG_TITLE_PLAIN = 'Something went wrong';

    /** @var string HTML content used when debug is disabled (App config) */
    const NO_DEBUG_CONTENT_HTML = '<p>The system has failed and the server wasn\'t able to fulfil your request. This incident has been logged.</p><p>Please try again later and if the problem persist don\'t hesitate to contact your system administrator.</p>';

    /**
     * Stack placeholders (STACK_ITEM_HTML, STACK_ITEM_CONSOLE)
     * - %x% Applies even class (pre--even)
     * - %i% Stack number
     * - %f% File
     * - %l% Line
     * - %fl% File + Line
     * - %c% class
     * - %t% type (::, ->)
     * - %m% Method (function)
     * - %a% Arguments.
     */

    /** @var string HTML template used for each stack entry */
    const STACK_ITEM_HTML = "<pre class=\"%x%\">#%i% %fl%\n%c%%t%%m%()%a%</pre>";

    /** @var string Console template used for each stack entry */
    const STACK_ITEM_CONSOLE = "#%i% %fl%\n%c%%t%%m%()%a%";

    /**
     * HTML placeholders (HTML_TEMPLATE, NO_DEBUG_BODY_HTML, DEBUG_BODY_HTML, BOX_BREAK_HTML).
     *
     * @see Formatter::parseTemplate
     */

    /** @var string HTML template (whole document) */
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="generator" content="Chevereto\Chevere\ErrorHandler"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';

    /** @var string HTML body used when debug is disabled (App config) */
    const NO_DEBUG_BODY_HTML = '<main><div><div class="t t--scream">%title%</div>%content%<p class="fine-print">%datetimeUtc% • %id%</p></div></main>';

    /** @var string HTML body used when debug is enabled (App config) */
    const DEBUG_BODY_HTML = '<main class="main--stack"><div>%content%<div class="c note user-select-none"><b>Note:</b> This message is being displayed because of active debug mode. Remember to turn this off when going production by editing <code>%loadedConfigFilesString%</code></div></div></main>';
    const BOX_BREAK_HTML = '<div class="hr"><span>------------------------------------------------------------</span></div>';
}
