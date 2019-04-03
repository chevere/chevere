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

namespace Chevereto\Core\Validate;

use Chevereto\Core\Message;
use RuntimeException;

class Image
{
    /**
     * Detects if an image file is animated or not.
     *
     * @param string $filename image file path
     *
     * @return bool TRUE if the image file correspond to an animated image
     *
     * @see http://php.net/manual/en/function.imagecreatefromgif.php#104473
     */
    public static function animated(string $filename): bool
    {
        $fh = @fopen($filename, 'rb');
        if (!$fh) {
            return false;
        }
        $count = 0;
        while (!feof($fh) && $count < 2) {
            //read 100kb at a time
            $chunk = fread($fh, 1024 * 100);
            if (isset($chunk)) {
                $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
            } else {
                break;
            }
        }
        if (!@fclose($fh)) {
            throw new RuntimeException(
                (string) (new Message('Unable to close %f.'))->code('%f', $filename)
            );
        }

        return $count > 1;
    }
}
