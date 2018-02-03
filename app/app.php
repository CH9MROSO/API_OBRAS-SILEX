<?php
use Silex\Application;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Api\ServicesLoader;
use Api\RoutesLoader;
use Api\Providers\UserProvider;
use Api\Providers\User;
use Carbon\Carbon;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;


date_default_timezone_set('Europe/Madrid');

define("ROOT_PATH", __DIR__ . "/..");

/*
 Support HTTP methods other than GET and POST y certain browsers.
 In the for include the following hidden input:
     <input type="hidden" id="_method" name="_method" value="PUT">
*/
Request::enableHttpMethodParameterOverride();



//handling CORS preflight request
$app->before(function (Request $request) {
   if ($request->getMethod() == "OPTIONS") {
       $response = new Response();
       $response->setStatusCode(200);
       return $response->send();
   }
}, Application::EARLY_EVENT);

//accepting JSON
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

//handling CORS respons with right headers
/*$app->after(function (Request $request, Response $response) {
   $response->headers->set("Access-Control-Allow-Origin","*");
   $response->headers->set("Access-Control-Allow-Methods","GET,POST,PUT,DELETE,OPTIONS");
   $response->headers->set("Access-Control-Allow-Headers","Content-Type, Authorization");
});*/

$app->register(new ServiceControllerServiceProvider());

$app->register(new DoctrineServiceProvider(), array(
  "dbs.options" => $app["dbs.options"]
));

//$app->register(new HttpCacheServiceProvider(), array("http_cache.cache_dir" => ROOT_PATH . "/storage/cache",));

$app->register(new MonologServiceProvider(), array(
    "monolog.logfile" => ROOT_PATH . "/storage/logs/" . Carbon::now('Europe/Madrid')->format("Y-m-d") . ".log",
    "monolog.level" => $app["log.level"],
    "monolog.name" => "application"
));

//load services
$servicesLoader = new Api\ServicesLoader($app);
$servicesLoader->bindServicesIntoContainer();

//load routes
$routesLoader = new Api\RoutesLoader($app);
$routesLoader->bindRoutesToControllers();

$app->error(function (\Exception $e, $code) use ($app) {
    $app['monolog']->addError($e->getMessage());
    $app['monolog']->addError($e->getTraceAsString());
    return new JsonResponse(array("success" => false, "error" => $e->getMessage(), "statusCode" => $code, "message" => $e->getMessage(), "stacktrace" => $e->getTraceAsString()));
});


//==============================================================================
// JWT Security
//==============================================================================
//add config for security jwt
$app['security.jwt'] = [
    //'secret_key' => 'Very_secret_key',
    'life_time'  => 86400, // 24 hours (in seconds)
    'secret_key' => 'D3d9eG.90121W56y3d012',//'a.3d56Gt9012rT56yu9012',
    //'life_time' => 1800, // 15 minutes
    'algorithm'  => ['HS256'],
    'options'    => [
        'username_claim' => 'name', // default name, option specifying claim containing username
        //'header_name' => 'X-Access-Token', // default null, option for usage normal oauth2 header
        'header_name' => 'Authorization',
        'token_prefix' => 'Bearer',
    ]
];

//Create users, any user provider implementing interface UserProviderInterface
/*$app['users'] = function () use ($app) {
    $users = [
        'admin' => array(
            'roles' => array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'),
            // raw password is foo
            'password' => '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==',
            R/4UNETjf4fg49jt7gRPo9qjBDSHoCe1q9d9IVSZAq2BwKYLyULD6ZUZirKzUS7+nYvKJ++vbkS9VSBnB2BM3Q==
            shqSvh0cHeZoeNXCtVHNNH82/58N0QfwE/Sy3g0BO/AnOLuB+rBpTjxEtvCnlNL7SnP6JErpb36FGRlcgXX46w==
            'enabled' => true
        ),
    ];

    return new InMemoryUserProvider($users);
};*/
$app['users'] = new UserProvider($app['db']);
//Add config for silex security
$app['security.firewalls'] = array(
    'login' => [
        'pattern' => 'login|logout|register|oauth|registrar',
        'anonymous' => true,
    ],
    'secured' => array(
        'pattern' => '^.*$',
        'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
        'users' => $app['users'],
        'jwt' => array(
            'use_forward' => true,
            'require_previous_session' => false,
            'stateless' => true,
        )
    ),
);
//Register silex providers
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\SecurityJWTServiceProvider());

//$app->boot();
//==============================================================================
// End JWT security.
//==============================================================================

// email templates
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => array(ROOT_PATH.'/app/Api/Templates'),
    'twig.options' => array('cache' => ROOT_PATH.'/storage/cache/twig'),
));

// mailer. http://stackoverflow.com/questions/13055907/silex-swiftmailer-not-making-smtp-connection-upon-execution
$app->register(new Silex\Provider\SwiftmailerServiceProvider(), ['swiftmailer.options' => $app['swiftmailer.options']]);
$app['mailer'] = $app->share(function ($app) {
    return new \Swift_Mailer($app['swiftmailer.transport']);
});

return $app;