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

namespace App\Controllers;

use Chevere\Controller\Controller;
use Chevere\JsonApi\EncodedDocument;
use JsonApiPhp\JsonApi\Attribute;
use JsonApiPhp\JsonApi\DataDocument;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

class Index extends Controller
{
    public function __invoke()
    {
        $api = new ResourceObject(
            'info',
            'api',
            new Attribute('entry', 'HTTP GET /api'),
            new Attribute('description', 'Retrieves the exposed API.'),
            new SelfLink('/api')
        );
        $cli = new ResourceObject(
            'info',
            'cli',
            new Attribute('entry', 'php chevere.php list'),
            new Attribute('description', 'Retrieves the console command list.'),
        );
        $document = new DataDocument(
            new ResourceCollection($api, $cli),
            new JsonApi(),
        );
        $encodedDocument = new EncodedDocument($document);
        $this->setContent($encodedDocument->toString());
    }
}
