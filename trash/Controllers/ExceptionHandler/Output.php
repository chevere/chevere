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

namespace Chevere\Components\ExceptionHandler\src;

use Chevere\Components\Instances\BootstrapInstance;
use JsonApiPhp\JsonApi\Error;
use JsonApiPhp\JsonApi\ErrorDocument;
use JsonApiPhp\JsonApi\Error\Code;
use JsonApiPhp\JsonApi\Error\Detail;
use JsonApiPhp\JsonApi\Error\Id;
use JsonApiPhp\JsonApi\Error\Status;
use JsonApiPhp\JsonApi\Error\Title;
use JsonApiPhp\JsonApi\Meta;
use Psr\Http\Message\StreamInterface;
use Chevere\Components\ExceptionHandler\ExceptionHandler;
use Chevere\Components\Http\Response;
use Chevere\Components\Message\Message;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Provides ExceptionHandler output by passing a Formatter. FIXME: Don't handle responses!
 */
final class Output
{
    /** @var string The text/plain content representation */
    private string $textPlain;

    private array $tags;

    private array $preparedTags;

    private ExceptionHandler $exceptionHandler;

    private Formatter $formatter;

    private StreamInterface $output;

    /** @var string The rich template string. Note: Placeholders won't be visible when dumping to console */
    private string $richTemplate;

    /** @var string The plain template string. */
    private string $plainTemplate;

    public function __construct(ExceptionHandler $exceptionHandler, Formatter $formatter)
    {
        $this->exceptionHandler = $exceptionHandler;
        $this->formatter = $formatter;
        $this->generateTemplates();
        $this->parseTemplates();
        if ($exceptionHandler->request()->isXmlHttpRequest()) {
            $this->setJsonOutput();
        } else {
            if (BootstrapInstance::get()->isCli()) {
                // $this->setJsonOutput();
                $this->setConsoleOutput();
            // $this->setHtmlOutput();
            } else {
                $this->setHtmlOutput();
            }
        }
    }

    public function textPlain(): string
    {
        return $this->textPlain;
    }

    public function out(): void
    {
        if (BootstrapInstance::get()->isCli()) {
            // Must kill the CLI, to stop the default CLI error printing to console
            die(1);
        }

        $response = new Response();
        if ($this->exceptionHandler->request()->isXmlHttpRequest()) {
        } else {
            // $response = new HttpResponse();
        }
        $guzzle = $response->guzzle()
            ->withBody($this->output)
            ->withStatus(500);

        $response = $response
            ->withGuzzle($guzzle);

        $response->sendBody();
    }

    private function parseTemplates(): void
    {
        $this->tags = $this->formatter->data()->toArray();

        $this->preparedTags = [];
        foreach ($this->tags as $k => $v) {
            $this->preparedTags["%$k%"] = $v;
        }
        $content = strtr($this->richTemplate, $this->preparedTags);
        $this->tags['content'] = $content;
        $this->preparedTags['%content%'] = $this->tags['content'];
        $this->textPlain = strtr($this->plainTemplate, $this->preparedTags);
        // xdd(var_export(array_keys($this->preparedTags), true));
    }

    // FIXME: JsonApi Document
    private function setJsonOutput(): void
    {
        $log = [
            'id' => $this->tags['id'],
            'level' => $this->formatter->data()->key('loggerLevel'),
        ];
        if ($this->exceptionHandler->isDebugEnabled()) {
            $log['filename'] = $this->tags['logFilename'];
            $title = $this->formatter->data()->key('thrown') . ' in ' . $this->tags['file'] . ':' . $this->tags['line'];
            $error = [];
            foreach (['file', 'line', 'code', 'message', 'class'] as $v) {
                if (isset($this->tags[$v])) {
                    $error[$v] = $this->tags[$v];
                }
            }
        } else {
            $title = Template::NO_DEBUG_TITLE_PLAIN;
        }

        $jsonApi = new ErrorDocument(
            new Error(
                new Id($this->tags['id']),
                new Status('500'),
                new Title($this->formatter->data()->key('thrown')),
                new Detail($title),
                new Code((string) $this->tags['code']),
                new Meta('level', $this->formatter->data()->key('loggerLevel'))
            )
        );

        // $document = new EncodedDocument($jsonApi);
        // xdd();

        $this->output = stream_for(json_encode($jsonApi));
    }

    private function setHtmlOutput(): void
    {
        if ($this->exceptionHandler->isDebugEnabled()) {
            $bodyTemplate = Template::DEBUG_BODY_HTML;
        } else {
            $bodyTemplate = Template::NO_DEBUG_BODY_HTML;
            $this->preparedTags['%content%'] = Template::NO_DEBUG_CONTENT_HTML;
            $this->preparedTags['%title%'] = Template::NO_DEBUG_TITLE_PLAIN;
        }
        $this->preparedTags['%body%'] = strtr($bodyTemplate, $this->preparedTags);
        $this->output = stream_for(strtr(Template::HTML_TEMPLATE, $this->preparedTags));
    }

    private function setConsoleOutput(): void
    {
        foreach ($this->formatter->consoleSections() as $k => $v) {
            if ('title' == $k) {
                $tpl = $v[0];
            } else {
                BootstrapInstance::get()->console()->style()->section(strtr($v[0], $this->preparedTags));
                $tpl = $v[1];
            }
            $message = strtr($tpl, $this->preparedTags);
            if ('title' == $k) {
                BootstrapInstance::get()->console()->style()->error($message);
            } else {
                $message = (new Message($message))->toString();
                BootstrapInstance::get()->console()->style()->writeln($message);
            }
        }
        BootstrapInstance::get()->console()->style()->writeln('');
    }

    private function generateTemplates(): void
    {
        $templateStrings = new TemplatedStrings($this->formatter);
        $this->richTemplate = $templateStrings->rich();
        $this->plainTemplate = $templateStrings->plain();
    }
}
