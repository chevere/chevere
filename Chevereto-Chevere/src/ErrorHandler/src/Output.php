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

namespace Chevere\ErrorHandler\src;

use const Chevere\CLI;
use DateTime;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Chevere\Console\Console;
use Chevere\ErrorHandler\ErrorHandler;
use Chevere\ErrorHandler\ExceptionHandler;
use Chevere\Json;

/**
 * Provides ErrorHandler output by passing a Formatter.
 */
class Output
{
    /** @var string The rich (console/html) content representation */
    public $content;

    /** @var string The plain content representation (log txt) */
    public $plainContent;

    /** @var array */
    public $templateTags;

    /** @var ErrorHandler */
    protected $errorHandler;

    /** @var Formatter */
    protected $formatter;

    /** @var ExceptionHandler */
    protected $exceptionHandler;

    /** @var string */
    protected $output;

    /** @var array */
    protected $headers = [];

    protected $richTemplate;

    protected $plainTemplate;

    public function __construct(ErrorHandler $errorHandler, Formatter $formatter)
    {
        $this->errorHandler = $errorHandler;
        $this->formatter = $formatter;
        $this->generateTemplates($formatter);
        $this->parseTemplate();
        if ($errorHandler->request()->isXmlHttpRequest()) {
            $this->setJsonOutput();
        } else {
            if (CLI) {
                $this->setConsoleOutput();
            } else {
                $this->setHtmlOutput();
            }
        }
    }

    protected function setJsonOutput(): void
    {
        $json = new Json();
        $this->headers = array_merge($this->headers, Json::CONTENT_TYPE);
        $response = [Template::NO_DEBUG_TITLE_PLAIN, 500];
        $log = [
            'id' => $this->getTemplateTag('id'),
            'level' => $this->formatter->loggerLevel,
            'filename' => $this->getTemplateTag('logFilename'),
        ];
        switch ($this->errorHandler->isDebugEnabled()) {
            case 0:
                unset($log['filename']);
                break;
            case 1:
                $response[0] = $this->formatter->thrown.' in '.$this->getTemplateTag('file').':'.$this->getTemplateTag('line');
                $error = [];
                foreach (['file', 'line', 'code', 'message', 'class'] as $v) {
                    $error[$v] = $this->getTemplateTag($v);
                }
                $json->data->setKey('error', $error);
                break;
        }
        $json->data->setKey('log', $log);
        $json->setResponse(...$response);
        $this->output = (string) $json;
    }

    protected function setHtmlOutput(): void
    {
        if ($this->errorHandler->isDebugEnabled()) {
            $bodyTemplate = Template::DEBUG_BODY_HTML;
        } else {
            $this->content = Template::NO_DEBUG_CONTENT_HTML;
            $this->addTemplateTag('content', $this->content);
            $this->addTemplateTag('title', Template::NO_DEBUG_TITLE_PLAIN);
            $bodyTemplate = Template::NO_DEBUG_BODY_HTML;
        }
        $this->addTemplateTag('body', strtr($bodyTemplate, $this->templateTags));
        $this->output = strtr(Template::HTML_TEMPLATE, $this->templateTags);
    }

    protected function setConsoleOutput(): void
    {
        foreach ($this->formatter->consoleSections as $k => $v) {
            if ('title' == $k) {
                $tpl = $v[0];
            } else {
                Console::cli()->out->section(strtr($v[0], $this->templateTags));
                $tpl = $v[1];
            }
            $message = strtr($tpl, $this->templateTags);
            if ('title' == $k) {
                Console::cli()->out->error($message);
            } else {
                $message = preg_replace_callback('#<code>(.*?)<\/code>#', function ($matches) {
                    $consoleColor = new ConsoleColor();

                    return $consoleColor->apply('light_blue', $matches[1]);
                }, $message);
                Console::cli()->out->writeln($message);
            }
        }
        Console::cli()->out->writeln('');
    }

    protected function generateTemplates(Formatter $formatter)
    {
        $templateStrings = new TemplateStrings($formatter);
        $templateStrings->setTitleBreak(str_repeat('=', $formatter::COLUMNS));
        $i = 0;
        foreach ($formatter->plainContentSections as $k => $plainSection) {
            $templateStrings
                ->setPlainSection($plainSection)
                ->setRichSection($formatter->richContentSections[$k] ?? null)
                ->process($i);
            ++$i;
        }
        $this->richTemplate = $templateStrings->rich;
        $this->plainTemplate = $templateStrings->plain;
    }

    public function parseTemplate()
    {
        $this->templateTags = [
            '%id%' => $this->errorHandler->id(),
            '%datetimeUtc%' => $this->errorHandler->dateTimeAtom(),
            '%timestamp%' => $this->errorHandler->timestamp(),
            '%loadedConfigFilesString%' => $this->errorHandler->loadedConfigFilesString(),
            '%logFilename%' => $this->errorHandler->logFilename(),
            '%css%' => $this->formatter->css,
            '%bodyClass%' => $this->formatter->bodyClass,
            '%body%' => null,
            '%title%' => $this->formatter->title,
            '%content%' => $this->content,
            '%title%' => $this->formatter->title,
            '%file%' => $this->formatter->file,
            '%line%' => $this->formatter->line,
            '%message%' => $this->formatter->message,
            '%code%' => $this->formatter->code,
            '%plainStack%' => $this->formatter->plainStack,
            '%consoleStack%' => $this->formatter->consoleStack,
            '%richStack%' => $this->formatter->richStack,
            '%clientIp%' => $this->formatter->clientIp,
            '%clientUserAgent%' => $this->formatter->clientUserAgent,
            '%serverProtocol%' => $this->formatter->serverProtocol,
            '%requestMethod%' => $this->formatter->requestMethod ?? 'n/a',
            '%uri%' => $this->formatter->uri ?? null,
            '%serverHost%' => $this->formatter->serverHost,
            '%serverPort%' => $this->formatter->serverPort,
            '%serverSoftware%' => $this->formatter->serverSoftware,
        ];
        $this->content = strtr($this->richTemplate, $this->templateTags);
        $this->plainContent = strtr($this->plainTemplate, $this->templateTags);
        $this->addTemplateTag('content', $this->content);
    }

    /**
     * $table stores the template placeholders and its value.
     *
     * @param string $tagName Template tag name
     * @param mixed  $value   value
     */
    public function addTemplateTag(string $tagName, $value): void
    {
        $this->templateTags["%$tagName%"] = $value;
    }

    /**
     * @param string $tagName Template tag name
     */
    public function getTemplateTag(string $tagName)
    {
        return $this->templateTags["%$tagName%"] ?? null;
    }

    public function out(): void
    {
        if ($this->errorHandler->request()->isXmlHttpRequest()) {
            $response = new HttpJsonResponse();
        } else {
            $response = new HttpResponse();
        }
        $response->setContent($this->output);
        $response->setLastModified(new DateTime());
        $response->setStatusCode(500);
        foreach ($this->headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        $response->send();
    }
}
