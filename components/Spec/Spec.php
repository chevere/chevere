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

use Chevere\Components\Message\Message;
use Chevere\Components\Router\Interfaces\RouterInterface;
use Chevere\Components\Spec\Exceptions\SpecInvalidArgumentException;
use Chevere\Components\Spec\Interfaces\SpecInterface;

/**
 * A collection of application routes and its endpoints.
 */
final class Spec implements SpecInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->assertRouter();
    }

    public function toArray(): array
    {
        $spec = [];
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
        $spec['index.json'] = $groups;
        $spec['files.json'] = [
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

        return $spec;

        // return $this->router->index()->toArray();
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
}
