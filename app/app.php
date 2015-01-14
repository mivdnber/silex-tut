<?php
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

class Application extends Silex\Application {
    use Silex\Application\TwigTrait;
    use Silex\Application\FormTrait;

    public $config;

    private $base_dir;

    function __construct($loader, $config) {
        parent::__construct();
        error_log(__DIR__ . '/../src', 4);
        $loader->addPsr4($config['namespace'] . '\\', realpath(__DIR__ . '/../src'));
        $this->base_dir = $base_dir;
        $this->namespace = $namespace;
        $this->config = $config;

        $this->registerServices();
    }

    protected function registerServices() {
        $this->register(new Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../views',
        ));
        $this->register(new Silex\Provider\FormServiceProvider());
    }
}

return new Application($loader, $config);
