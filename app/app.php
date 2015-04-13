<?php
namespace geography\webapp;

$loader = require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/../config/config.php';

use geography\db as db;

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
    }

}

return new Application($loader, $config);
