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

use Chevere\Components\Api\ApiEndpoint;
use Chevere\Components\Api\Tests\_resources\controllers\GetArticleController;
use Chevere\Components\Controller\Interfaces\ControllerInterface;

return new class() extends ApiEndpoint
{
    public function description(): string
    {
        return 'Get article identified by its ID';
    }

    public function parameters(): array
    {
        return [
            'id' => 'The numeric ID (integer)'
        ];
    }

    public function controller(): ControllerInterface
    {
        return new GetArticleController();
    }
};
