# tolkam/routing

PSR-15 routing and dispatching.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Aura\Router\RouterContainer;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\PSR15\Dispatcher\Dispatcher;
use Tolkam\Routing\Adapter\Aura\Adapter\Aura\AuraRouterAdapter;
use Tolkam\Routing\DispatcherMiddleware;
use Tolkam\Routing\MatcherMiddleware;
use Tolkam\Routing\Resolver\CallableResolver;
use Tolkam\Routing\Runner\CallableRunner;

// configure routes
$routerContainer = new RouterContainer();
$routerContainer
    ->getMap()
    ->get('myRoute', '/myRoute', function () {
        return (new ResponseFactory())
            ->createResponse(200, 'Hello from "/myRoute" handler!');
    });

// create and configure middlewares
$matcherMiddleware = new MatcherMiddleware(new AuraRouterAdapter($routerContainer));
$dispatcherMiddleware = new DispatcherMiddleware();

// set the resolver to resolve route handler type
// and possibly instantiate it
$dispatcherMiddleware->addResolver(new CallableResolver());

// set the route handler runner
$dispatcherMiddleware->addRunner(new CallableRunner());

// dummy request handler for demo purposes to run the middleware directly
// the actual behavior of the middleware is to "short-circuit" to self and to throw on errors
$dummyRequestHandler = new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
};

// create a GET request to "/myRoute"
$request = (new ServerRequestFactory())
    ->createServerRequest('GET', '/myRoute');

// process the request and get the route handler result
echo Dispatcher::create($dummyRequestHandler)
    ->middleware($matcherMiddleware)
    ->middleware($dispatcherMiddleware)
    ->handle($request)
    ->getReasonPhrase();
````

## License

Proprietary / Unlicensed 🤷
