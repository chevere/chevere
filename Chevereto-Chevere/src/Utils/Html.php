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
// OK? Cambiar a new Html(string)->toBBCode(), ->toMarkdown()

namespace Chevereto\Chevere;

class Html
{
    /**
     * Converts HTML code to BBCode.
     *
     * @param string $html HTML code
     *
     * @return string BBCode
     *
     * @see http://kuikie.com/snippets/snippet.php/90-17/php-function-to-convert-bbcode-to-html
     */
    public static function toBBCode(string $html): string
    {
        $htmltags = [
            '/\<b\>(.*?)\<\/b\>/is',
            '/\<i\>(.*?)\<\/i\>/is',
            '/\<u\>(.*?)\<\/u\>/is',
            '/\<ul.*?\>(.*?)\<\/ul\>/is',
            '/\<li\>(.*?)\<\/li\>/is',
            '/\<img(.*?) src=\"(.*?)\" alt=\"(.*?)\" title=\"Smile(y?)\" \/\>/is',
            '/\<img(.*?) src=\"(.*?)\" (.*?)\>/is',
            '/\<img(.*?) src=\"(.*?)\" alt=\":(.*?)\" .*? \/\>/is',
            '/\<div class=\"quotecontent\"\>(.*?)\<\/div\>/is',
            '/\<div class=\"codecontent\"\>(.*?)\<\/div\>/is',
            '/\<div class=\"quotetitle\"\>(.*?)\<\/div\>/is',
            '/\<div class=\"codetitle\"\>(.*?)\<\/div\>/is',
            '/\<cite.*?\>(.*?)\<\/cite\>/is',
            '/\<blockquote.*?\>(.*?)\<\/blockquote\>/is',
            '/\<div\>(.*?)\<\/div\>/is',
            '/\<code\>(.*?)\<\/code\>/is',
            '/\<br(.*?)\>/is',
            '/\<strong\>(.*?)\<\/strong\>/is',
            '/\<em\>(.*?)\<\/em\>/is',
            '/\<a href=\"mailto:(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
            '/\<a .*?href=\"(.*?)\"(.*?)\>http:\/\/(.*?)\<\/a\>/is',
            '/\<a .*?href=\"(.*?)\"(.*?)\>(.*?)\<\/a\>/is',
        ];
        $bbtags = [
            '[b]$1[/b]',
            '[i]$1[/i]',
            '[u]$1[/u]',
            '[list]$1[/list]',
            '[*]$1',
            '$3',
            '[img]$2[/img]',
            ':$3',
            '\[quote\]$1\[/quote\]',
            '\[code\]$1\[/code\]',
            '',
            '',
            '',
            '\[quote\]$1\[/quote\]',
            '$1',
            '\[code\]$1\[/code\]',
            "\n",
            '[b]$1[/b]',
            '[i]$1[/i]',
            '[email=$1]$3[/email]',
            '[url]$1[/url]',
            '[url=$1]$3[/url]',
        ];
        $html = str_replace("\n", ' ', $html);
        $ntext = preg_replace($htmltags, $bbtags, $html);
        if (isset($ntext)) {
            $ntext = preg_replace($htmltags, $bbtags, $ntext);
        }
        // for too large code and cannot handle by str_replace
        if (!$ntext) {
            $ntext = str_replace(['<br>', '<br />'], "\n", $html);
            $ntext = str_replace(['<strong>', '</strong>'], ['[b]', '[/b]'], $ntext);
            $ntext = str_replace(['<em>', '</em>'], ['[i]', '[/i]'], $ntext);
        }
        $ntext = strip_tags($ntext);
        $ntext = trim(html_entity_decode($ntext, ENT_QUOTES, 'UTF-8'));

        return $ntext;
    }
}
