<?php declare(strict_types=1);
/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Chevereto\Core\Controllers;

// use function Chevereto\Core\dd;
use App\User;
use Chevereto\Core\App;
use Chevereto\Core\CoreException;
use Chevereto\Core\Console;
use Chevereto\Core\Message;
use Chevereto\Core\Controller;

/**
 * Identical to GET, but without any message-boby in the response.
 */
class ApiHead extends Controller
{
    const OPTIONS = [
        'description' => 'GET without message-body.',
    ];
    public function __invoke(/*string $callable = null*/)
    {
        $app = App::instance();
        $route = $app->getRoute();
        $callable = $route->getCallable('GET');
        //
        if ($callable == null) {
            $message =
                (new Message('You have to provide the %s argument when running this callable without route context.'))
                    ->code('%s', 'callable');
            if (Console::exists()) {
                Console::io()->error($message);
                exit;
            } else {
                throw new CoreException($message);
            }
        }
        //
        $response = $app->runner($callable);
        $response->setContent(null);
        return $response;
    }
}