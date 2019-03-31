<?php

declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core\Utils;

// $var = 'inbox@rodolfoberrios.com es mi correo y http://rodolfoberrios.com mi URL y www.wea.com es una weaita';
// $res = new Linkify($var);
// $res
//     ->callback(function ($href, $link) {
//         return '<a href="' . $href . '">' . $link . '</a>';
//     })
//     ->safe(true)
//     ->attributes(['rel' => 'nofollow']);
// dump($var, (string) $res);

class Linkify
{
    const CALLBACK = 'callback';
    const ATTRIBUTES = 'attributes';
    const SAFE = 'safe';
    protected $string;
    protected $return;
    public $safe = true;
    public $callback;
    public $attributes = [];

    /**
     * (string) $object will cast exec().
     */
    public function __toString()
    {
        if ($this->return == null) {
            $this->exec();
        }

        return $this->return;
    }

    /**
     * Convert applicable URLs and emails from into clickable links.
     *
     * Inspired from https://github.com/misd-service-development/php-linkify
     *
     * @param string $string source text to linkify
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Set linkify callback callable.
     *
     * @param callable $callable callable
     *
     * @return self
     */
    public function callback(callable $callable): self
    {
        $this->callback = $callable;

        return $this;
    }

    /**
     * Set linkify safe flag.
     *
     * @param bool $boolean TRUE to set the safe flag (removes any dangerous chars)
     *
     * @return self
     */
    public function safe(bool $boolean): self
    {
        $this->safe = $boolean;

        return $this;
    }

    /**
     * Set linkify link attributes.
     *
     * @param array $attributes Associative array containing HTML attributes [attr => value]
     *
     * @return self
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Execute text linkify.
     */
    public function exec(): void
    {
        $string = $this->string;
        $attr = null;
        $options = [
            static::CALLBACK => $this->callback,
            static::SAFE => $this->safe,
            static::ATTRIBUTES => $this->attributes,
        ];
        if ($this->safe === true) {
            $string = htmlspecialchars($string);
            foreach (['rel' => 'nofollow', 'target' => '_blank'] as $k => $v) {
                $options[static::ATTRIBUTES][$k] = $v;
            }
        }
        if (true === array_key_exists(static::ATTRIBUTES, $options)) {
            foreach ($options[static::ATTRIBUTES] as $key => $value) {
                if (true === is_array($value)) {
                    $value = array_pop($value);
                }
                $attr .= sprintf(' %s="%s"', $key, $value);
            }
        }
        $options[static::ATTRIBUTES] = $attr;
        $ignoreTags = ['head', 'link', 'a', 'script', 'style', 'code', 'pre', 'select', 'textarea', 'button'];
        $chunks = preg_split('/(<.+?>)/is', $string, 0, PREG_SPLIT_DELIM_CAPTURE);
        $openTag = null;
        if (is_array($chunks)) {
            $chunkCnt = count($chunks);
            for ($i = 0; $i < $chunkCnt; ++$i) {
                if ($i % 2 === 0) { // even numbers are text
                    // Only process this chunk if there are no unclosed $ignoreTags
                    if (null === $openTag && isset($chunks[$i])) {
                        $chunks[$i] = static::URLs((string) $chunks[$i], $options);
                        $chunks[$i] = static::emails((string) $chunks[$i], $options);
                    }
                } else { // odd numbers are tags
                    // Only process this tag if there are no unclosed $ignoreTags
                    if (null === $openTag) {
                        // Check whether this tag is contained in $ignoreTags and is not self-closing
                        if (isset($chunks[$i]) && preg_match('`<('.implode('|', $ignoreTags).').*(?<!/)>$`is', $chunks[$i], $matches)) {
                            $openTag = $matches[1];
                        }
                    } else {
                        // Otherwise, check whether this is the closing tag for $openTag.
                        if (null !== $openTag && isset($chunks[$i]) && preg_match('`</\s*'.$openTag.'>`i', $chunks[$i], $matches)) {
                            $openTag = null;
                        }
                    }
                }
            }
            $this->return = implode($chunks);
        }
    }

    /**
     * Linkify email addresses.
     *
     * @param string $string  source text to linkify
     * @param array  $options associative options (attributes => callback fn)
     *
     * @return string HTML code with clickable links
     */
    public static function emails(string $string, array $options = [self::ATTRIBUTES => '']): ?string
    {
        $pattern = '~(?xi)
            \b
            (?<!=)           # Not part of a query string
            [A-Z0-9._\'%+-]+ # Username
            @                # At
            [A-Z0-9.-]+      # Domain
            \.               # Dot
            [A-Z]{2,4}       # Something
        ~';
        $callback = function ($match) use ($options) {
            $href = 'mailto:'.$match[0];
            $link = $match[0];
            if (isset($options[static::CALLBACK])) {
                $cb = $options[static::CALLBACK]($href, $link);
                if (!is_null($cb)) {
                    return $cb;
                }
            }

            return '<a href="'.$href.'"'.$options[static::ATTRIBUTES].'>'.$link.'</a>';
        };

        return preg_replace_callback($pattern, $callback, $string);
    }

    /**
     * Linkify URLs.
     *
     * @param string $string  source text to linkify
     * @param array  $options options (Link attributes + callback fn)
     *
     * @return string HTML code with clickable links
     */
    public static function URLs(string $string, array $options = [self::ATTRIBUTES => '']): ?string
    {
        $pattern = '~(?xi)
            (?:
              ((ht|f)tps?://)                    # scheme://
              |                                  #   or
              www\d{0,3}\.                       # "www.", "www1.", "www2." ... "www999."
              |                                  #   or
              www\-                              # "www-"
              |                                  #   or
              [a-z0-9.\-]+\.[a-z]{2,4}(?=/)      # looks like domain name followed by a slash
            )
            (?:                                  # Zero or more:
              [^\s()<>]+                         # Run of non-space, non-()<>
              |                                  #   or
              \(([^\s()<>]+|(\([^\s()<>]+\)))*\) # balanced parens, up to 2 levels
            )*
            (?:                                  # End with:
              \(([^\s()<>]+|(\([^\s()<>]+\)))*\) # balanced parens, up to 2 levels
              |                                  #   or
              [^\s`!\-()\[\]{};:\'".,<>?«»“”‘’]  # not a space or one of these punct chars
            )
        ~';
        $callback = function ($match) use ($options) {
            $link = $match[0];
            $pattern = '~^(ht|f)tps?://~';
            if (0 === preg_match($pattern, $match[0])) {
                $match[0] = 'http://'.$match[0];
            }
            if (isset($options[static::CALLBACK])) {
                $cb = $options[static::CALLBACK]($match[0], $link);
                if (!is_null($cb)) {
                    return $cb;
                }
            }

            return '<a href="'.$match[0].'"'.$options[static::ATTRIBUTES].'>'.$link.'</a>';
        };

        return preg_replace_callback($pattern, $callback, $string);
    }
}
