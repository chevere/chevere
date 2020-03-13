<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Spec;

use BadMethodCallException;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\Interfaces\SpecCacheInterface;
use Chevere\Components\Spec\Interfaces\SpecInterface;
use LogicException;

final class SpecCache implements SpecCacheInterface
{
    private CacheInterface $cache;

    private array $array;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function put(SpecInterface $spec): void
    {
        // Add this header to all responses: Link: </spec/api/routes.json>; rel="describedby"
        $this->array = [];
        $groups = [];
        $groups[] = [
            'name' => 'api',
            'spec' => '/spec/api/routes.json',
            'routes' => [
                [
                    'name' => 'articles-repo',
                    'spec' => '/spec/api/articles-repo/endpoints.json',
                    'path' => '/api/articles',
                    'wildcards' => [],
                    'endpoints' => [
                        [
                            'method' => 'GET',
                            'spec' => '/spec/api/articles-repo/GET.json',
                            'description' => 'Retrieves articles',
                            'parameters' => [],
                        ],
                        [
                            'method' => 'POST',
                            'spec' => '/spec/api/articles-repo/POST.json',
                            'description' => 'Creates a new article',
                            'parameters' => [],
                        ]
                    ]
                ],
                [
                    'name' => 'article-entity',
                    'spec' => '/spec/api/article-entity/endpoints.json',
                    'path' => '/api/article/{id}',
                    'wildcards' => [
                        [
                            'name' => 'id',
                            'match' => '\d+',
                        ]
                    ],
                    'endpoints' => [
                        [
                            'method' => 'GET',
                            'spec' => '/spec/api/article-entity/GET.json',
                            'description' => 'Retrieves an article',
                        ],
                    ]
                ]
            ]
        ];
        $groups[] = [
            'name' => 'web',
            'spec' => '/spec/web/routes.json',
            'routes' => [
                [
                    'name' => 'articles-repo',
                    'spec' => '/spec/web/articles/endpoints.json',
                    'path' => '/articles',
                    'wildcards' => [],
                    'endpoints' => [
                        [
                            'method' => 'GET',
                            'spec' => '/spec/web/articles-repo/GET.json',
                            'description' => 'Retrieves articles',
                            'parameters' => [],
                        ],
                        [
                            'method' => 'POST',
                            'spec' => '/spec/web/articles-repo/POST.json',
                            'description' => 'Creates a new article',
                            'parameters' => [],
                        ]
                    ]
                ],
            ]
        ];
        $this->array['index.json'] = $groups;
        $this->array['files.json'] = [
            [
                './index.json',
                './files.json',
                './api/routes.json',
                './api/articles-repo/endpoints.json',
                './api/articles-repo/GET.json',
                './api/articles-repo/POST.json',
                './api/article-entity/endpoints.json',
                './api/article-entity/GET.json',
                './web/routes.json',
                './web/articles/endpoints.json',
                './web/articles-repo/GET.json',
                './web/articles-repo/POST.json',
            ],
        ];
    }

    public function get(): SpecInterface
    {
    }

    public function toArray(): array
    {
        if (!isset($this->array)) {
            throw new BadMethodCallException(
                (new Message('Method %methodName% can be called only if %putMethod% has been previously called'))
                    ->code('%methodName%', __METHOD__)
                    ->code('%putMethod%', __CLASS__ . '::' . 'put')
                    ->toString()
            );
        }

        return $this->array;
    }

    private function assertRouter(): void
    {
        $checks = [
            'groups' => $this->router->hasGroups(),
            'index' => $this->router->hasIndex(),
            'named' => $this->router->hasNamed(),
            'regex' => $this->router->hasRegex(),
        ];
        $missing = array_filter($checks, fn (bool $bool) => $bool === false);
        $keys = array_keys($missing);
        if (!empty($keys)) {
            throw new SpecInvalidArgumentException(
                (new Message('Missing %interfaceName% %propertyName% property(s).'))
                    ->code('%interfaceName%', RouterInterface::class)
                    ->code('%propertyName%', implode(',', $keys))
                    ->toString()
            );
        }
    }

    private function assertDir(): void
    {
        if (!$this->cache->exists()) {
            $this->cache->create(0777);
        }
        if (!$this->cache->path()->isWriteable()) {
            throw new LogicException(
                (new Message('Directory %pathName% is not writeable'))
                    ->code('%pathName%', $this->cache->path()->absolute())
                    ->toString()
            );
        }
    }
}
