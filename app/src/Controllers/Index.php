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

use Chevere\Contracts\Controller\JsonApiContract;
use Chevere\Controller\Controller;
use Chevere\Controller\Traits\JsonApiTrait;
use Chevere\JsonApi\EncodedDocument;
use Chevere\Globals\Globals;
use Exception;
use JsonApiPhp\JsonApi\Attribute;
use JsonApiPhp\JsonApi\DataDocument;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

use function ChevereFn\stringReplaceFirst;

class Index extends Controller implements JsonApiContract
{
    use JsonApiTrait;

    /** @var ResourceObject */
    private $api;

    /** @var ResourceObject */
    private $cli;

    public function __invoke(): void
    {
        throw new Exception('eee');
        // $gg = new Globals($GLOBALS);
        // dd($gg->server());
        // dd(stringReplaceFirst('e', '@', 'eee'));
        // die(1000 * (microtime(true) - BOOTSTRAP_TIME));
        $this->api = new ResourceObject(
            'info',
            'api',
            new Attribute('entry', 'HTTP GET /api'),
            new Attribute('description', 'Retrieves the exposed API.'),
            new SelfLink('/api')
        );
        $this->cli = new ResourceObject(
            'info',
            'cli',
            new Attribute('entry', 'php chevere.php list'),
            new Attribute('description', 'Retrieves the console command list.'),
        );
    }
    public function getDocument(): EncodedDocument
    {
        return
            new EncodedDocument(
                new DataDocument(
                    new ResourceCollection($this->api, $this->cli),
                    new JsonApi(),
                )
            );
    }
}
