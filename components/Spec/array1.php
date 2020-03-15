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

$groups = [];
$groups[] = [
    'name' => 'api',
    'spec' => '/spec/api/routes.json',
    'routes' => [
        [
            'name' => 'articles-repo',
            'spec' => '/spec/api/articles-repo/index.json',
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
            'spec' => '/spec/api/article-entity/index.json',
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
            'spec' => '/spec/web/articles/index.json',
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
        './api/articles-repo/index.json',
        './api/articles-repo/GET.json',
        './api/articles-repo/POST.json',
        './api/article-entity/index.json',
        './api/article-entity/GET.json',
        './web/routes.json',
        './web/articles/index.json',
        './web/articles-repo/GET.json',
        './web/articles-repo/POST.json',
    ],
];
