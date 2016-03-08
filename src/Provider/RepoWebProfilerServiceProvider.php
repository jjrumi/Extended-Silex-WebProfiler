<?php

namespace jjrumi\SilexWebProfiler\Provider;

use jjrumi\SilexWebProfiler\Collector\GitDataCollector;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use \LogicException;

class RepoWebProfilerServiceProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    protected $gitDataCollector;

    public function register(Application $app)
    {
        $this->gitDataCollector = $app->share(function ($app) {
            return new GitDataCollector();
        });

        $data_collectors = $app['data_collectors'];
        $data_collectors['git'] = $this->gitDataCollector;
        $app['data_collectors'] = $data_collectors;

        $templates = $app['data_collector.templates'];
        $templates[] = array('git', '@GitWebProfiler/profiler.html.twig');
        $app['data_collector.templates'] = $templates;

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
            $loader->addPath(__DIR__ . '/../../Resources/views', 'GitWebProfiler');

            return $loader;
        }));
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        return $controllers;
    }

    public function boot(Application $app)
    {
        if (!$app['profiler'] instanceof Profiler) {
            throw new LogicException('You must enable the WebProfilerServiceProvider to be able to use this Profiler. See https://github.com/sensiolabs/Silex-WebProfiler');
        }
        $gitDataCollector = $this->gitDataCollector;
        $app['profiler']->add($gitDataCollector($app));
    }

}
