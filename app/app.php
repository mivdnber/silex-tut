<?php
namespace geography\webapp;
error_reporting(E_ERROR | E_WARNING | E_NOTICE);
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/../config/config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use phpCAS;

use geography\db as db;

class SecureRoute extends \Silex\Route {
    use \Silex\Route\SecurityTrait;
}

class Application extends \Silex\Application {
    use \Silex\Application\TwigTrait;
    use \Silex\Application\FormTrait;
    use \Silex\Application\SecurityTrait;
    use \Silex\Application\UrlGeneratorTrait;

    public $config;

    private $base_dir;

    function __construct($loader, $config) {
        parent::__construct();
        $this['route_class'] = 'geography\webapp\SecureRoute';
        $loader->addPsr4('geography\\webapp\\', __DIR__);
        $loader->addPsr4($config['namespace'] . '\\', realpath(__DIR__ . '/../src'));
        $this->config = $config;

        $this->registerServices();
        if(isset($this->config['security'])) {
            $this->setupSecurity();
        }

        $this['debug'] = true;
    }

    protected function registerServices() {
        $db = new \geography\db\Connection($this->config['db']);
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../views',
        ));
        $this->register(new \Silex\Provider\FormServiceProvider());
        $this->register(new \Silex\Provider\SessionServiceProvider());
        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider());
        $this['db'] = $this->share(function() {
            return new db\Connection($this->config['db']);
        });

        $this['users'] = $this->share(function() {
            return new DbUserProvider($this['db']);
        });

    }

    protected function setupSecurity() {
        $app = $this;
        $app->register(new \Silex\Provider\SecurityServiceProvider(), [
            'security.firewalls' => [
                'site' => [
                    'pattern' => '^/.*$',
                    'anonymous' => true,
                    'form' => [
                        'login_path' => '/login',
                        'check_path' => '/check_login'
                    ],
                    'users' => $app['users'],
                    /*'remember_me' => [
                        'key' => 'interior crocodile alligator',
                        // Sound like a Celine Dion song:
                        'always_remember_me' => true,
                    ]*/
                ],
            ],
        ]);
        $this->registerSecurityPaths();
    }

    protected function registerSecurityPaths() {
        $app = $this;

        $app->before(function ($request) {
            $request->getSession()->start();
        });

        $this->get('/login', function(Request $req) use($app) {
            return $this->render('login.html');
        });

        $this->get('/login/cas', function(Request $req) use($app) {
            $returnUrl = $req->get('return', '/');
            phpCAS::client(SAML_VERSION_1_1, 'login.ugent.be', 443, '', true, 'saml');
            phpCAS::setNoCasServerValidation();
            phpCAS::handleLogoutRequests(true, array(
                'cas1.ugent.be', 'cas2.ugent.be', 'cas3.ugent.be',
                'cas4.ugent.be', 'cas5.ugent.be', 'cas6.ugent.be'
            ));
            phpCAS::setNoCasServerValidation();
            phpCAS::setExtraCurlOption(CURLOPT_SSLVERSION, 3);
            phpCAS::forceAuthentication();
            $username = phpCAS::getUser();
            $user = $this['users']->loadUserByUsername($username);
            //error_log('user: ' . print_r($user, true), 4);
            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                'site',
                $user->getRoles()
            );
            $app['security']->setToken($token);
            error_log('token: ' . print_r($app->user(), true), 4);
            $event = new InteractiveLoginEvent($req, $token);
            $app['dispatcher']->dispatch('security.interactive_login', $event);
            //return $app->redirect('/');
            return $app->redirect(
                $app['session']->get('_security.site.target_path') ?: '/'
            );

        })->bind('login_cas');

    }
}

return new Application($loader, $config);
