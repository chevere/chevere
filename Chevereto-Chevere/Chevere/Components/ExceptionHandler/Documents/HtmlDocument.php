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

namespace Chevere\Components\ExceptionHandler\Documents;

use Chevere\Components\ExceptionHandler\Formatters\HtmlFormatter;
use Chevere\Components\ExceptionHandler\Interfaces\FormatterInterface;

final class HtmlDocument extends AbstractDocument
{
    /** @var string Title used when debug=0 */
    const NO_DEBUG_TITLE_PLAIN = 'Something went wrong';

    /** @var string HTML content used when debug=0 */
    const NO_DEBUG_CONTENT_HTML = '<p>The system has failed and the server wasn\'t able to fulfil your request. This incident has been logged.</p><p>Please try again later and if the problem persist don\'t hesitate to contact your system administrator.</p>';

    /** @var string HTML template (whole document) */
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';

    /** @var string HTML body used when debug=0 */
    const NO_DEBUG_BODY_HTML = '<main><div><div class="t t--scream">%title%</div>%content%<p class="fine-print">%dateTimeAtom% • %id%</p></div></main>';

    /** @var string HTML body used when debug=1 */
    const DEBUG_BODY_HTML = '<main class="main--stack"><div>%content%<div class="c note user-select-none"><b>Note:</b> This message is being displayed because of active debug mode. Remember to turn this off when going production by editing <code>%loadedConfigFilesString%</code></div></div></main>';

    /**
     * {@inheritdoc}
     */
    public function getFormatter(): FormatterInterface
    {
        return new HtmlFormatter;
    }

    public function getSectionsTemplate(): array
    {
        return [
            static::SECTION_TITLE => $this->wrapTitle('%title% <span>in&nbsp;%fileLine%</span>'),
            static::SECTION_MESSAGE => $this->wrapSectionTitle('# Message') . "\n" . $this->wrapContent('%message% %codeWrap%'),
            static::SECTION_TIME => $this->wrapSectionTitle('# Time') . "\n" . $this->wrapContent('%dateTimeUtcAtom% [%timestamp%]'),
            static::SECTION_ID => $this->wrapSectionTitle('# Incident ID:%id%') . "\n" . $this->wrapContent('Logged at %logFilename%'),
            static::SECTION_STACK => $this->wrapSectionTitle('# Stack trace') . "\n" . $this->wrapContent('%stack%'),
            static::SECTION_CLIENT => $this->wrapSectionTitle('# Client') . "\n" . $this->wrapContent('%clientIp% %clientUserAgent%'),
            static::SECTION_REQUEST => $this->wrapSectionTitle('# Request') . "\n" . $this->wrapContent('%serverProtocol% %requestMethod% %uri%'),
            static::SECTION_SERVER => $this->wrapSectionTitle('# Server') . "\n" . $this->wrapContent('%phpUname% %serverSoftware%'),
        ];
    }

    protected function getGlue(): string
    {
        return "\n<br>\n";
    }

    protected function wrap(string $value): string
    {
        $preDocument = strtr(static::HTML_TEMPLATE, [
            '%bodyClass%' => !headers_sent() ? 'body--flex' : 'body--block',
            '%css%' => file_get_contents(dirname(__DIR__) . '/src/template.css'),
            '%body%' => static::DEBUG_BODY_HTML,
        ]);

        return
            strtr($preDocument, [
                '%content%' => $value,
            ]);
    }

    private function wrapTitle(string $value): string
    {
        return '<div class="t t--scream">' . $value . '</div>';
    }

    private function wrapSectionTitle(string $value): string
    {
        $value = str_replace('# ', $this->wrapHidden('#&nbsp;'), $value);

        return '<div class="t">' . $value . '</div>';
    }

    private function wrapHidden(string $value): string
    {
        return '<span class="hide">' . $value . '</span>';
    }

    private function wrapContent(string $value): string
    {
        return '<div class="c">' . $value . '</div>';
    }
}
