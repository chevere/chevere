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

    /** @var string HTML document template */
    const HTML_TEMPLATE = '<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>%css%</style></head><body class="%bodyClass%">%body%</body></html>';

    /** @var string HTML body used when debug=0 */
    const BODY_DEBUG_0_HTML = '<main><div>%content%</div></main>';

    /** @var string HTML body used when debug=1 */
    const BODY_DEBUG_1_HTML = '<main class="main--stack"><div>%content%</div></main>';

    public function getFormatter(): FormatterInterface
    {
        return new HtmlFormatter;
    }

    public function getTemplate(): array
    {
        $template = parent::getTemplate();
        if (!$this->exceptionHandler->isDebug()) {
            $template = [
                self::SECTION_TITLE => $template[self::SECTION_TITLE],
            ];
        }

        return $template;
    }

    public function getSectionTitle(): string
    {
        if (!$this->exceptionHandler->isDebug()) {
            return $this->formatter->wrapTitle(self::NO_DEBUG_TITLE_PLAIN) . self::NO_DEBUG_CONTENT_HTML
                . '<p class="fine-print">' . self::TAG_DATE_TIME_UTC_ATOM . ' • ' . self::TAG_ID . '</p>';
        }

        return $this->formatter->wrapTitle(self::TAG_TITLE . ' <span>in&nbsp;' . self::TAG_FILE_LINE . '</span>');
    }

    protected function prepare(string $document): string
    {
        $preDocument = strtr(self::HTML_TEMPLATE, [
            '%bodyClass%' => !headers_sent() ? 'body--flex' : 'body--block',
            '%css%' => file_get_contents(dirname(__DIR__) . '/src/template.css'),
            '%body%' => $this->exceptionHandler->isDebug() ? self::BODY_DEBUG_1_HTML : self::BODY_DEBUG_0_HTML,
        ]);

        return str_replace('%content%', $document, $preDocument);
    }
}
