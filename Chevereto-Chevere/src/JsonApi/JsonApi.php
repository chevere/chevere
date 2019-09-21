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

namespace Chevere\JsonApi;

use const Chevere\CLI;

use JsonException;
use InvalidArgumentException;
use Chevere\Message\Message;

// FIXME: Use JsonSerializable https://www.php.net/manual/en/jsonserializable.jsonserialize.php

/**
 * JsonApi document
 */
final class JsonApi
{
    // PROFILE https://jsonapi.org/format/1.1/#profiles

    const VERSION = '1.1';

    const MEDIA_TYPE = 'application/vnd.api+json';
}
