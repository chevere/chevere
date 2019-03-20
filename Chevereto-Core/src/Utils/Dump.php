<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// OK
namespace Chevereto\Core\Utils;

use Chevereto\Core\Path;
use Chevereto\Core\Utils\Str;

use ReflectionObject;
use ReflectionProperty;

use JakubOnderka\PhpConsoleColor\ConsoleColor;

/**
 * Dump utility
 * Another var_dump replacement.
 */
class Dump
{
    /**
     * Variable types
     */
    const TYPE_STRING = 'string';
    const TYPE_FLOAT = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_NULL = 'NULL';
    const TYPE_OBJECT = 'object';
    const TYPE_ARRAY = 'array';
    /**
     * Stuff?
     */
    const _FILE = '_file';
    const _CLASS = '_class';
    const _OPERATOR = '_operator';
    const _FUNCTION = '_function';

    const ANON_CLASS = 'class@anonymous';

    /**
     * Default color palette for each type and thing used.
     * [algo => color]
     */
    const PALETTE = [
        self::TYPE_STRING    => '#e67e22', // orange
        self::TYPE_FLOAT     => '#f1c40f', // yellow
        self::TYPE_INTEGER   => '#f1c40f', // yellow
        self::TYPE_BOOLEAN   => '#9b59b6', // purple
        self::TYPE_NULL      => '#7f8c8d', // grey
        self::TYPE_OBJECT    => '#e74c3c', // red
        self::TYPE_ARRAY     => '#2ecc71', // green
        self::_FILE          => null,
        self::_CLASS         => '#3498db', // blue
        self::_OPERATOR      => '#7f8c8d', // grey
        self::_FUNCTION      => '#9b59b6', // purple
    ];

    /**
     * Default color palette for each type and thing used.
     * [algo => color]
     */
    const CONSOLE_PALETTE = [
        self::TYPE_STRING    => 'color_136', // yellow
        self::TYPE_FLOAT     => 'color_136', // yellow
        self::TYPE_INTEGER   => 'color_136', // yellow
        self::TYPE_BOOLEAN   => 'color_127', // purple
        self::TYPE_NULL      => 'color_245', // grey
        self::TYPE_OBJECT    => 'color_167', // red
        self::TYPE_ARRAY     => 'color_41', // green
        self::_FILE          => null,
        self::_CLASS         => 'color_147', // blue
        self::_OPERATOR      => 'color_245', // grey
        self::_FUNCTION      => 'color_127', // purple
    ];
    /**
     * Dumps information about a variable.
     *
     * @param mixed $anything Anything.
     * @param int $indent Left padding (spaces) for this entry.
     * @param array $dontDump Array containing classes that won't get dumped.
     *
     * @return string Parsed dump string.
     */
    public static function out($anything, int $indent = null, array $dontDump = []) : string
    {
        // Maybe improve this to support any circular reference?
        if (is_array($anything)) {
            $expression = array_merge([], $anything);
        } else {
            $expression = $anything;
        }
        
        if ($indent == null) {
            $indent = 0;
        }
        $val = null;
        $prefix = str_repeat(' <span style="border-left: 1px solid #bdc3c7;"></span>  ', $indent);
        $type = gettype($expression);
        if ($type == 'double') {
            $type = static::TYPE_FLOAT;
        }
        $objects = [];
        switch ($type) {
            case static::TYPE_BOOLEAN:
                $val .= $expression ? 'TRUE' : 'FALSE';
            break;
            case static::TYPE_NULL:
            break;
            case static::TYPE_ARRAY:
                $indent++;
                foreach ($expression as $k => $v) {
                    $operator = static::wrap(static::_OPERATOR, '=>');
                    $val .= "\n$prefix " . htmlspecialchars((string) $k) . " $operator ";
                    $aux = $v;
                    $isCircularRef = is_array($aux) && isset($aux[$k]) && $aux == $aux[$k];
                    if ($isCircularRef) {
                        $val .= static::wrap(static::_OPERATOR, "(<i>circular array reference</i>)");
                    } else {
                        $val .= static::out($aux, $indent, $dontDump);
                    }
                }
                $parentheses = 'size=' . count($expression);
            break;
            case static::TYPE_OBJECT:
                $indent++;
                $reflection = new ReflectionObject($expression);
                if (in_array($reflection->getName(), $dontDump)) {
                    $val .= static::wrap(static::_OPERATOR, '<i>'. $reflection->getName() .'</i>');
                    continue;
                }
                $propertiesFiltered = [
                    'public' => ReflectionProperty::IS_PUBLIC,
                    'protected' => ReflectionProperty::IS_PROTECTED,
                    'private' => ReflectionProperty::IS_PRIVATE,
                    'static' => ReflectionProperty::IS_STATIC,
                ];
                $properties = [];
                foreach ($propertiesFiltered as $k => &$v) {
                    $v = $reflection->getProperties($v);
                    foreach ($v as $kk => $vv) {
                        if (!isset($properties[$vv->getName()])) {
                            $vv->setAccessible(true);
                            $value = $vv->getValue($expression);
                            $properties[$vv->getName()] = ['value' => $vv->getValue($expression)];
                        }
                        $properties[$vv->getName()]['visibility'][] = $k;
                    }
                }
                foreach ($properties as $k => $v) {
                    $isCircularRef = false;
                    $visibility = implode(' ', $properties[$k]['visibility'] ?? $properties['visibility']);
                    $operator = static::wrap(static::_OPERATOR, '->');
                    $val .= "\n$prefix <i>$visibility</i> " . htmlspecialchars($k) . " $operator ";
                    $aux = $v['value'];
                    if (is_object($aux) && property_exists($aux, $k)) {
                        $r = new ReflectionObject($aux);
                        $p = $r->getProperty($k);
                        $p->setAccessible(true);
                        if ($p->getValue($aux) == $aux) {
                            $isCircularRef = true;
                        }
                    }
                    if ($isCircularRef) {
                        $val .= static::wrap(static::_OPERATOR, "(<i>circular object reference</i>)");
                    } else {
                        $val .= static::out($aux, $indent, $dontDump);
                    }
                }
                $className = get_class($expression);
                if (Str::startsWith(static::ANON_CLASS, $className)) {
                    $className = Path::normalize($className);
                }
                $parentheses = $className;
            break;
            default:
                $is_string = is_string($expression);
                $is_numeric = is_numeric($expression);
                if ($is_string || $is_numeric) {
                    $parentheses = 'length=' . strlen($is_numeric ? ((string) $expression) : $expression);
                    // $val .= htmlspecialchars($expression);
                    $val .= strval($expression);
                }
            break;
        }
        switch ($type) {
            case static::TYPE_ARRAY:
            case static::TYPE_OBJECT:
                $template = '%type %parentheses%val';
            break;
            default:
                $template = '%type %val %parentheses';
            break;
        }
        if (isset($parentheses) && strpos($parentheses, '=') !== false) {
            $parentheses = '<i>'.$parentheses.'</i>';
        }
        return strtr($template, [
            '%type'	=> static::wrap($type, $type),
            '%val'	=> $val,
            '%parentheses' => isset($parentheses) ? static::wrap(static::_OPERATOR, '('.$parentheses.')') : null,
        ]);
    }
    /**
     * Get color for palette key.
     *
     * @param string $key Color palette key.
     *
     * @return string Color.
     */
    public static function color(string $key) : ?string
    {
        return php_sapi_name() == 'cli' ? static::CONSOLE_PALETTE[$key] ?? null : static::PALETTE[$key] ?? null;
    }
    /**
     * Wrap dump data into HTML.
     *
     * @param string $key Algo.
     * @param mixed $dump Dump data.
     *
     * @return string Wrapped dump data.
     */
    public static function wrap(string $key, $dump) : ?string
    {
        $color = static::color($key);
        if (isset($color)) {
            if (php_sapi_name() == 'cli') {
                $consoleColor = new ConsoleColor();
                return $consoleColor->apply($color, $dump);
            }
            return '<span style="color:' . $color . '">' . $dump . '</span>';
        } else {
            return (string) $dump;
        }
    }
}
