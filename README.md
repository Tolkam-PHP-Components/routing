# tolkam/routing

PSR-15 routing and dispatching based on Aura.Router.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\Routing\Resolver\CallableResolver;
use Tolkam\Routing\RouterContainer;
use Tolkam\Routing\RoutingMiddleware;
use Tolkam\Routing\Runner\CallableRunner;

// configure routes
$routerContainer = new RouterContainer;
$routerContainer
    ->getMap()
    ->get('myRoute', '/myRoute', function () {
        return (new ResponseFactory)
            ->createResponse(200, 'Hello from "/myRoute" handler!');
    });

// create and configure middleware
$middleware = new RoutingMiddleware($routerContainer);

// set the resolver to resolve route handler type
// and possibly instantiate it
$middleware->addHandlerResolver(new CallableResolver);

// set the route handler runner
$middleware->addRunner(new CallableRunner);

// dummy request handler for demo purposes to run the middleware directly
// the actual behavior of the middleware is to "short-circuit" to self and to throw on errors
$dummyRequestHandler = new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new ResponseFactory)->createResponse();
    }
};

// create a GET request to "/myRoute"
$request = (new ServerRequestFactory)
    ->createServerRequest('GET', '/myRoute');

// process the request and get the route handler result
echo $middleware
    ->process($request, $dummyRequestHandler)
    ->getReasonPhrase();
````

## License

Proprietary / Unlicensed 🤷
