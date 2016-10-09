<?php namespace App\Core;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Twig_Loader_Filesystem;


class Core{

    private $dispatcher;
    /**
     * @var Container
     */
    private $container;
    private $request;

    public function __construct(Container $container)
    {

        $this->container = $container;
        $this->setup();
    }


    public function registerRoutes($routes)
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) use ($routes) {

            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    public function handle(Request $request)
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return Response::create("404: Page Not Found", Response::HTTP_NOT_FOUND)->send();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                return Response::create("404: Method Not Allowed", Response::HTTP_NOT_FOUND)->send();
                break;
            case Dispatcher::FOUND:
                $handler = explode('@', $routeInfo[1]);
                $vars = $routeInfo[2];

                $controller = $handler[0];
                $method = $handler[1];

                if(class_exists($controller) && method_exists($controller, $method) ) {

                    $controller_instance = $this->container->get($controller);

                    try {
                        $response = $controller_instance->$method($vars);

                        if ($response instanceof Response) {
                            $response->prepare($request);
                            return $response;
                        }
                    }catch(\Exception $e) {
                        return Response::create("500: An error occured: {$e->getMessage()} ", Response::HTTP_NOT_FOUND)->send();

                    }

                } else {
                    return Response::create("500: Could not call method '$method' on controller '$controller' ", Response::HTTP_NOT_FOUND)->send();
                }

                break;
        }

    }

    public function run(Request $request = null)
    {

        if(! $request) {
            $request = Request::createFromGlobals();
        }

        $this->container->add(Request::class, $request);

        $response = $this->handle($request);
        $response->send();

    }

    public function setup()
    {

        $this->container->delegate(
            new ReflectionContainer()
        );

        $this->container->share('PDO')
            ->withArgument('sqlite:'.DEFAULT_SQLITE_DB_PATH);

        $this->container->add('Twig_Environment')
            ->withArgument(new Twig_Loader_Filesystem(DEFAULT_VIEW_DIRECTORY));

        $this->container->share('Symfony\Component\HttpFoundation\Session\Session')
            ->withArgument(new NativeSessionStorage());

        if(! file_exists(DEFAULT_SQLITE_DB_PATH)) {
            $pdo = $this->container->get('PDO');
            $pdo->exec('
            CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL , 
            email TEXT, 
            created_at TIMESTAMP DEFAULT current_timestamp
            )');
        }
    }

}
