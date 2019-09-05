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

final class JsonApi
{
    /** @var iterable The document’s “primary data” */
    private $data;

    /** @var Errors An array of error objects */
    private $errors;

    /** @var Included An array of resource objects that are related to the primary data and/or each other (“included resources”). */
    private $included;

    /** @var array Describes the server’s implementation */
    private $jsonapi;

    /** @var Links A links object related to the primary data. */
    private $links;

    /** @var Meta A meta object that contains non-standard meta-information. */
    private $meta;

    public function appendData(Data ...$data)
    {
        foreach ($data as $d) {
            $this->data[] = $data;
        }
    }
}
