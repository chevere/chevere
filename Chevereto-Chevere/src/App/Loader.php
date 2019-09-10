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

namespace Chevere\App;

use LogicException;
use RuntimeException;

use const Chevere\CLI;
use const Chevere\DEV_MODE;
use Chevere\ArrayFile\ArrayFile;
use Chevere\Path\PathHandle;
use Chevere\Api\Api;
use Chevere\Api\Maker as ApiMaker;
use Chevere\Console\Console;
use Chevere\Http\Request;
use Chevere\Http\Response;
use Chevere\Router\Maker as RouterMaker;
use Chevere\Runtime\Runtime;
use Chevere\Contracts\App\AppContract;
use Chevere\App\Exceptions\AlreadyBuiltException;
use Chevere\App\Exceptions\NeedsToBeBuiltException;
use Chevere\Cache\Exceptions\CacheNotFoundException;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\Render\RenderContract;
use Chevere\Contracts\Router\RouterContract;
use Chevere\Message;
use Chevere\Path\Path;
use Chevere\Router\Exception\RouteNotFoundException;
use Chevere\Router\Router;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

final class Loader implements LoaderContract
{
    const CACHED = false;

    /** @var Runtime */
    private static $runtime;

    /** @var AppContract */
    public $app;

    /** @var Api */
    private $api;

    /** @var ApiMaker */
    private $apiMaker;

    /** @var string */
    private $controller;

    /** @var Request */
    private $request;

    /** @var RouterContract */
    private $router;

    /** @var RouterMaker */
    private $routerMaker;

    /** @var bool True if run() has been called */
    private $ran;

    /** @var array An array containing the collection of Cache->toArray() data (checksums) */
    private $cacheChecksums;

    /** @var bool True if the console loop ran */
    private $consoleLoop;

    /** @var Parameters */
    private $parameters;

    /** @var array */
    private $arguments;

    /** @var bool True if the App was built (cache) */
    private $isBuilt;

    /** @var Checkout */
    private $checkout;

    /** @var Build */
    private $build;

    public function __construct()
    {
        Console::bind($this);

        $this->build = new Build();

        if (!DEV_MODE && !Console::isBuilding() && !$this->build->exists()) {
            throw new NeedsToBeBuiltException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method.'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::' . 'build')
                    ->toString()
            );
        }

        $this->routerMaker = new RouterMaker();
        $this->app = new App();
        $this->app->setResponse(new Response());

        $this->parameters = new Parameters(
            new ArrayFile(
                new PathHandle(App::FILEHANDLE_PARAMETERS)
            )
        );

        if (DEV_MODE) {
            $this->build();
        }

        $this->applyParameters();
    }

    public function build(): void
    {
        if ($this->isBuilt) {
            throw new AlreadyBuiltException();
        }
        $this->cacheChecksums = [];
        if (!empty($this->parameters->api())) {
            $pathHandle = new PathHandle($this->parameters->api());
            $this->apiMaker = ApiMaker::create($pathHandle, $this->routerMaker);
            $this->api = Api::fromMaker($this->apiMaker);
            $this->cacheChecksums = $this->apiMaker->cache()->toArray();
        }
        if (!empty($this->parameters->routes())) {
            $this->routerMaker->addRoutesArrays($this->parameters->routes());
            $this->router = Router::fromMaker($this->routerMaker);
            $this->cacheChecksums = array_merge($this->routerMaker->cache()->toArray(), $this->cacheChecksums);
        }
        $this->checkout = new Checkout($this->build, $this->cacheChecksums);
        $this->isBuilt = true;
    }

    public function destroy(): void
    {
        unlink($this->build->pathHandle()->path());
        $cachePath = Path::fromIdentifier('cache');
        Path::removeContents($cachePath);
    }

    public function checkout(): Checkout
    {
        return $this->checkout;
    }

    /**
     * {@inheritdoc}
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments): LoaderContract
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
        $pathinfo = ltrim($this->request->getPathInfo(), '/');
        $this->request->attributes->set('requestArray', explode('/', $pathinfo));
        $this->app->setRequest($this->request);
    }

    public function cacheChecksums(): array
    {
        return $this->cacheChecksums;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        $this->handleConsole();
        $this->handleRequest();
        $this->setRan();

        if (!isset($this->controller)) {
            $this->processResolveCallable($this->request->getPathInfo());
        }

        if (!isset($this->controller)) {
            throw new RuntimeException('DESCONTROL');
        }
        $this->runController($this->controller);
    }

    private function handleConsole()
    {
        if (Console::isAvailable() && !isset($this->consoleLoop)) {
            $this->consoleLoop = true;
            Console::run();
        }
    }

    private function handleRequest()
    {
        if (!isset($this->request)) {
            $this->setRequest(Request::createFromGlobals());
        }
    }

    private function setRan()
    {
        if (isset($this->ran)) {
            throw new LogicException(
                (new Message('The method %s has been already called.'))
                    ->code('%s', __METHOD__)
                    ->toString()
            );
        }
        $this->ran = true;
    }

    /**
     * {@inheritdoc}
     */
    public static function runtime(): Runtime
    {
        if (isset(self::$runtime)) {
            return self::$runtime;
        }
        throw new RuntimeException('NO RUNTIME INSTANCE EVERYTHING BURNS!');
    }

    /**
     * {@inheritdoc}
     */
    public static function request(): Request
    {
        if (isset(self::$request)) {
            return self::$request;
        }
        throw new RuntimeException('NO REQUEST INSTANCE EVERYTHING BURNS!');
    }

    /**
     * {@inheritdoc}
     */
    public static function setDefaultRuntime(Runtime $runtime)
    {
        self::$runtime = $runtime;
    }

    private function applyParameters()
    {
        try {
            if (!empty($this->parameters->api()) && !isset($this->api)) {
                $this->api = Console::isBuilding() ? new Api() : Api::fromCache();
            }
            if (!empty($this->parameters->routes()) && !isset($this->router)) {
                $this->router = Console::isBuilding() ? new Router() : Router::fromCache();
            }
        } catch (CacheNotFoundException $e) {
            $message = sprintf('The app must be re-build due to missing cache. %s', $e->getMessage());
            throw new NeedsToBeBuiltException($message, $e->getCode(), $e);
        }
        $this->app->setRouter($this->router);
    }

    private function processResolveCallable(string $pathInfo): void
    {
        try {
            $route = $this->router->resolve($pathInfo);
        } catch (RouteNotFoundException $e) {
            $this->app->response()->setStatusCode(404);
            $this->app->response()->setContent('404');
            $this->app->response()->prepare($this->app->request());
            $this->app->response()->send();
            if (CLI) {
                throw new RouteNotFoundException($e->getMessage());
            } else {
                die();
            }
        }
        $this->app->setRoute($route);
        $this->controller = $this->app->route()->getController($this->request->getMethod());
        $routerArgs = $this->router->arguments();
        if (!isset($this->arguments) && isset($routerArgs)) {
            $this->setArguments($routerArgs);
        }
    }

    private function runController(string $controller): void
    {
        $this->app->setArguments($this->arguments);
        $controller = $this->app->run($controller);
        if ($controller instanceof RenderContract) {
            $controller->render();
        } else {
            $jsonApi = $controller->document();
            $this->app->response()->setJsonContent($jsonApi);
            $this->app->response()->prepare($this->app->request());
            $this->app->response()->send();
        }
    }
}
