<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controllers;

use Chevere\Components\App\Instances\BootstrapInstance;
use Chevere\Components\Controller\Interfaces\JsonApiInterface;
use Chevere\Components\Controller\Controller;
use Chevere\Components\Controller\Traits\JsonApiTrait;
use Chevere\Components\JsonApi\EncodedDocument;
use Chevere\Components\Time\TimeHr;
use Exception;
use JsonApiPhp\JsonApi\Attribute;
use JsonApiPhp\JsonApi\DataDocument;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;
use LogicException;

class Index extends Controller implements JsonApiInterface
{
    use JsonApiTrait;

    /** @var ResourceObject */
    private $api;

    /** @var ResourceObject */
    private $cli;

    public function __invoke(): void
    {
        throw new LogicException('deeeeznuts');
        $took = hrtime(true);
        $arr = ['aaa', $this, (new TimeHr($took - BootstrapInstance::get()->hrTime()))->toReadMs()];
        xdd($arr);
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
