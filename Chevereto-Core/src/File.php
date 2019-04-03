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

namespace Chevereto\Core;

//FIXME: Detect missing fileinfo extension

use Exception;
use ErrorException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

// $var = ROOT_PATH . 'home_cover.png';
// $res = File::uniqueFilePath($var, [
//     File::METHOD => File::NAME_ORIGINAL,
//     File::MAX_LENGTH => 200,
//     File::RANDOM_LENGTH => 20,
//     File::RANDOM_FILL_LENGTH => 5,
// ]);

class File
{
    const METHOD = 'METHOD';
    const MAX_LENGTH = 'MAX_LENGTH';
    const RANDOM_LENGTH = 'RANDOM_LENGTH';
    const RANDOM_FILL_LENGTH = 'RANDOM_FILL_LENGTH';
    const NAME_ORIGINAL = 'NAME_ORIGINAL';
    const NAME_RANDOM = 'NAME_RANDOM';
    const NAME_MIXED = 'NAME_MIXED';
    const NAMING_OPTIONS = [self::NAME_ORIGINAL, self::NAME_RANDOM, self::NAME_MIXED];
    const NAME_DEFAULT_OPTIONS = [
        self::METHOD => self::NAME_ORIGINAL,
        self::MAX_LENGTH => 200,
        self::RANDOM_LENGTH => 20,
        self::RANDOM_FILL_LENGTH => 5,
    ];

    /**
     * @todo More entries.
     */
    const MIMETYPES = [
        'image/x-windows-bmp' => 'bmp',
        'image/x-ms-bmp' => 'bmp',
        'image/bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/pjpeg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/x-png' => 'png',
        'image/png' => 'png',
        'image/x-tiff' => 'tiff',
        'image/tiff' => 'tiff',
        'image/x-icon' => 'ico',
        'image/x-rgb' => 'rgb',
    ];

    /**
     * Gets the mimetype of a file.
     *
     * @param string $filename file to read
     *
     * @return string mimetipe
     */
    public static function mimetype(string $filename): ?string
    {
        if (defined('FILEINFO_MIME_TYPE') && function_exists('finfo_open')) {
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $filename);
        }
        if (function_exists('mime_content_type')) {
            return (string) mime_content_type($filename);
        }

        return null;
        //   IMAGE ONLY!
        //   case function_exists('getimagesize'):
        //       return getimagesize($filename)['mime'];
        //   break;
        //   case function_exists('exif_imagetype'):
        //       return exif_imagetype($filename); // try
        //   break;
    }

    /**
     * Gets the corresponding file extension from a known mimetype string.
     *
     * @param string $mimetype file mimetype string
     *
     * @return string file extension
     */
    public static function extensionFromMime(string $mimetype): ?string
    {
        return static::MIMETYPES[$mimetype];
    }

    /**
     * Gets a file extension based on the pathinfo() function.
     *
     * @param string $filename file path you wish to retrieve its extension
     *
     * @return string file extension
     */
    public static function extension(string $filename): ?string
    {
        $mime = static::mimetype($filename);
        if (isset($mime)) {
            return static::extensionFromMime($mime);
        }

        return null;
    }

    /**
     * Gets the name of a file from its file path.
     *
     * @param string $filename file you wish to retrieve its name
     *
     * @return string file name
     */
    public static function name(string $filename): string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Get a safe file basename (sanitized).
     *
     * Based on the given options, this function generates a safe to use sanitized file name.
     *
     * @param string $filename candidate file name
     * @param array  $options  see (static::NAME_DEFAULT_OPTIONS)
     *
     * @return string sanitized file name
     */
    // public static function safeName(string $filename, array $options = []): string
    // {
    //FIXME: Rebuilt
    // if ($options != null) {
    //     $validation = [
    //         static::METHOD => [
    //             function ($v) {
    //                 return (is_callable($v) || in_array($v, static::NAMING_OPTIONS));
    //             },
    //             "Invalid naming method %s, try with <code>" . implode('</code>, <code>', static::NAMING_OPTIONS) . "</code>"
    //         ],
    //         static::MAX_LENGTH => [
    //             'is_int',
    //             'Expecting integer value, <code>%t</code> provided'
    //         ]
    //     ];
    //     $validation[static::RANDOM_LENGTH] = $validation[static::MAX_LENGTH];
    //     $validation[static::RANDOM_FILL_LENGTH] = $validation[static::MAX_LENGTH];
    //     try {
    //         $Validation = new Validation();
    //         foreach ($options as $k => $v) {
    //             $Validation->add($k, $validation[$k][0], $validation[$k][1])->value($v);
    //         }
    //         $Validation->validate();
    //     } catch (Exception $e) {
    //         throw new ValidationException($e);
    //     }
    // }
    // $options = array_merge(static::NAME_DEFAULT_OPTIONS, $options);
    // $fileExtension = static::extension($filename);
    // $clean = substr($filename, 0, -(strlen($fileExtension) + 1));
    // $clean = Utils\Str::unaccent($clean); // change áéíóú to aeiou
    // $clean = preg_replace('/[^\.\w\d-]/i', '', $clean); // remove any non alphanumeric, non underscore, non hyphen and non period
    // // Non alphanumeric name uh..
    // if (strlen($clean) == 0) {
    //     $clean = Utils\Random::string($options[static::RANDOM_LENGTH] * .5);
    // }
    // // $unlimitedFilename = $clean; // No max_length limit
    // $clean = substr($clean, 0, $options[static::MAX_LENGTH]);

    // if (is_callable($options[static::METHOD])) {
    //     $name = $options[static::METHOD]($filename, $clean, $fileExtension, $options);
    // } else {
    //     switch ($options[static::METHOD]) {
    //         default:
    //         case static::NAME_ORIGINAL:
    //             $name = $clean;
    //         break;
    //         case static::NAME_RANDOM:
    //             $name = Utils\Random::string($options[static::RANDOM_LENGTH]);
    //         break;
    //         case static::NAME_MIXED:
    //             if (strlen($clean) >= $options[static::MAX_LENGTH]) {
    //                 $name = substr($clean, 0, $options[static::MAX_LENGTH] - 5);
    //             } else {
    //                 $name = $clean;
    //             }
    //             $name .= Utils\Random::string($options[static::RANDOM_FILL_LENGTH]);
    //         break;
    //     }
    // }
    // return $name . '.' . $fileExtension;
    // }

    /**
     * Gets an unique and available file name in the given file path.
     *
     * Checks if $filename exists and if so, it will determinate an alternative
     * file path by slightly randomizing the file basename.
     *
     * @param string $filename File path
     * @param array  $options  static::NAME_DEFAULT_OPTIONS
     *
     * @return string available file path
     */
    public static function uniqueFilePath(string $filename, array $options = []): ?string
    {
        $pathinfo = pathinfo($filename);
        $path = $pathinfo['dirname'];
        $filename = $pathinfo['basename'];
        // Note: Options will get validated by ::safeName
        $uniqueFilePath = null;
        $options = array_merge(static::NAME_DEFAULT_OPTIONS, $options);
        while (static::exists($filename)) {
            if ($options[static::METHOD] == static::NAME_ORIGINAL) {
                $options[static::METHOD] = static::NAME_MIXED;
            }
            $uniqueFilePath = rtrim($path, '/').'/'.static::safeName($filename, $options);
        }

        return $uniqueFilePath;
    }

    /**
     * Fast wat to determine if a file or directory exists using stream_resolve_include_path.
     *
     * @param string $filename   Absolute file path
     * @param bool   $clearCache TRUE to call clearstatcache
     *
     * @return bool TRUE if the $filename exists
     */
    public static function exists(string $filename, bool $clearCache = true): bool
    {
        if ($clearCache) {
            clearstatcache(true);
        }
        // Only tweak relative paths, without wrappers or anything else
        // Note that stream_resolve_include_path won't work with relative paths if no chdir().
        if (!Path::isAbsolute($filename)) {
            $filename = Path::absolute($filename);
        }

        return stream_resolve_include_path($filename) !== false;
    }

    /**
     * Remove all the files in the target $path.
     *
     * @param string $path target directory path
     *
     * @throws Exception
     */
    public static function removeAll(string $path): void
    {
        if (!is_dir($path)) {
            throw new Exception(
                (new Message('String %s is not recognized as a directory'))->code('%s', $path)
            );
        }
        $iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
        $failed = [];
        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $file = $fileinfo->getRealPath();
            if (File::exists($file)) {
                try {
                    $res = $todo($file);
                } catch (ErrorException $e) {
                    $res = false;
                    $errorMessage = $e->getMessage();
                }
                if ($res == false) {
                    $failed[$file] = $errorMessage ?? 'No error available';
                }
            }
        }
        if ($failed != false) {
            $exceptionMessage = [];
            foreach ($failed as $k => $v) {
                $exceptionMessage[] = '<b>'.$k.'</b>:'.$v;
            }
            // FIXME: Usar new Message()
            throw new Exception('Unable to remove '.implode('; ', $exceptionMessage));
        }
    }

    /**
     * Retrieves information about a file.
     *
     * @param string $filename file
     *
     * @return array file info array
     */
    public static function info(string $filename): array
    {
        $filesize = filesize($filename);
        if (!$filesize) {
            return [];
        }
        $mime = static::mimetype($filename);
        $basename = basename($filename); // file.ext
        $name = Utils\Str::replaceLast('.'.File::extension($filename), null, $basename); // file
        $info = [
            'basename' => $basename,
            'name' => $name,
            'extension' => $mime != null ? File::extensionFromMime($mime) : null,
            'size' => intval($filesize),
            'size_format' => Utils\Bytes::format((string) $filesize),
            'mime' => $mime,
            // 'url'		=> absolutePathToUrl($filename),
            'md5' => md5_file($filename),
        ];

        return $info;
    }

    /**
     * Get file path identifier.
     *
     * Path identifiers are always relative to App\PATH.
     *
     * @param string $file file path, if null it will detect file caller
     */
    public static function identifier(string $file = null): string
    {
        if ($file == null) {
            $file = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'];
        }
        $pathinfo = pathinfo($file);
        $dirname = $pathinfo['dirname'];
        // Relativize to App\PATH
        $dirname = Utils\Str::replaceFirst(App\PATH, null, $dirname);
        if ($dirname == rtrim(App\PATH, '/')) { // Means that $file is at App\PATH
            $dirname = null;
        }

        return $dirname.':'.$pathinfo['filename'];
    }
}
