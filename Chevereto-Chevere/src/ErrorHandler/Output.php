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

use DateTime;
use Chevereto\Chevere\Console;
use Chevereto\Chevere\Json;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;

/**
 * Provides ErrorHandler output by passing a Formatter.
 */
class Output
{
    public $content;
    public $templateTags;

    /** @var ErrorHandler */
    protected $errorHandler;

    /** @var Formatter */
    protected $formatter;

    /** @var string */
    protected $output;

    /** @var array */
    protected $headers = [];

    protected $richContentTemplate;

    protected $plainContentTemplate;

    public function __construct(ErrorHandler $errorHandler, Formatter $formatter)
    {
        $this->errorHandler = $errorHandler;
        $this->formatter = $formatter;
        $this->generateTemplates();
        $this->parseTemplate();
        if ($errorHandler->httpRequest && $errorHandler->httpRequest->isXmlHttpRequest()) {
            $this->setJsonOutput();
        } else {
            if ('cli' == php_sapi_name()) {
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
            'id' => $this->formatter->getTemplateTag('id'),
            'level' => $this->loggerLevel,
            'filename' => $this->formatter->getTemplateTag('logFilename'),
        ];
        switch ($this->errorHandler->isDebugEnabled) {
            case 0:
                unset($log['filename']);
            break;
            case 1:
                $response[0] = $this->thrown.' in '.$this->formatter->getTemplateTag('file').':'.$this->formatter->getTemplateTag('line');
                $error = [];
                foreach (['file', 'line', 'code', 'message', 'class'] as $v) {
                    $error[$v] = $this->formatter->getTemplateTag($v);
                }
                $json->setDataKey('error', $error);
            break;
        }
        $json->setDataKey('log', $log);
        $json->setResponse(...$response);
        $this->output = (string) $json;
    }

    protected function setHtmlOutput(): void
    {
        switch ($this->errorHandler->isDebugEnabled) {
            default:
            case 0:
                $this->content = Template::NO_DEBUG_CONTENT_HTML;
                $this->addTemplateTag('content', $this->content);
                $this->addTemplateTag('title', Template::NO_DEBUG_TITLE_PLAIN);
                $bodyTemplate = Template::NO_DEBUG_BODY_HTML;
            break;
            case 1:
                $bodyTemplate = Template::DEBUG_BODY_HTML;
            break;
        }
        // HTML error content is empty!
        // dd($this->templateTags['%content%']);
        $this->addTemplateTag('body', strtr($bodyTemplate, $this->templateTags));
        $this->output = strtr(Template::HTML_TEMPLATE, $this->templateTags);
    }

    protected function setConsoleOutput(): void
    {
        foreach ($this->formatter->consoleSections as $k => $v) {
            if ('title' == $k) {
                $tpl = $v[0];
            } else {
                Console::io()->section(strtr($v[0], $this->templateTags));
                $tpl = $v[1];
            }
            $message = strtr($tpl, $this->templateTags);
            if ('title' == $k) {
                Console::io()->error($message);
            } else {
                Console::io()->writeln($message);
            }
        }
        Console::io()->writeln('');
    }

    protected function generateTemplates()
    {
        $sections_length = count($this->formatter->plainContentSections);
        $i = 0;
        foreach ($this->formatter->plainContentSections as $k => $plainSection) {
            $richSection = $this->richContentSections[$k] ?? null;
            $section_length = count($plainSection);
            if (0 == $i || isset($plainSection[1])) {
                $this->richContentTemplate .= '<div class="t'.(0 == $i ? ' t--scream' : null).'">'.$richSection[0].'</div>';
                $this->plainContentTemplate .= html_entity_decode($plainSection[0]);
                if (0 == $i) {
                    $this->richContentTemplate .= "\n".'<div class="hide">'.str_repeat('=', $this->formatter::COLUMNS).'</div>';
                    $this->plainContentTemplate .= "\n".str_repeat('=', $this->formatter::COLUMNS);
                }
            }
            if ($i > 0) {
                $j = 1 == $section_length ? 0 : 1;
                for ($j; $j < $section_length; ++$j) {
                    if ($section_length > 1) {
                        $this->richContentTemplate .= "\n";
                        $this->plainContentTemplate .= "\n";
                    }
                    $this->richContentTemplate .= '<div class="c">'.$richSection[$j].'</div>';
                    $this->plainContentTemplate .= $plainSection[$j];
                }
            }
            if ($i + 1 < $sections_length) {
                $this->richContentTemplate .= "\n".'<br>'."\n";
                $this->plainContentTemplate .= "\n\n";
            }
            ++$i;
        }
    }

    public function parseTemplate()
    {
        $this->templateTags = [
            '%id%' => $this->errorHandler->id,
            '%datetimeUtc%' => $this->errorHandler->datetimeUtc,
            '%timestamp%' => $this->errorHandler->timestamp,
            '%loadedConfigFilesString%' => $this->errorHandler->loadedConfigFilesString,
            '%logFilename%' => $this->errorHandler->logFilename,
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
            '%httpRequestMethod%' => $this->formatter->httpRequestMethod,
            '%uri%' => $this->formatter->uri ?? null,
            '%serverHost%' => $this->formatter->serverHost,
            '%serverPort%' => $this->formatter->serverPort,
            '%serverSoftware%' => $this->formatter->serverSoftware,
        ];
        $this->content = strtr($this->richContentTemplate, $this->templateTags);
        $this->plainContent = strtr($this->plainContentTemplate, $this->templateTags);
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
        if ($this->errorHandler->httpRequest && $this->errorHandler->httpRequest->isXmlHttpRequest()) {
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
