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

namespace Chevereto\Chevere;

/**
 * Utils\Image.
 */
class Image
{
    /**
     * Retrieves information about an image file.
     *
     * @param string $filename image file you wish to retrieve information about
     *
     * @return array fileinfo
     */
    public static function info(string $filename): array
    {
        $fileInfo = File::info($filename);
        $imagesize = getimagesize($filename);
        if ($fileInfo == false && $imagesize == false) {
            return [];
        }

        return array_merge($fileInfo, [
            'width' => intval($imagesize[0]),
            'height' => intval($imagesize[1]),
            'ratio' => $imagesize[0] / $imagesize[1],
            'bits' => $imagesize['bits'],
            'channels' => $imagesize['channels'],
        ]);
    }
}
