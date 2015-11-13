<?php

namespace jjrumi\SilexWebProfiler\Collector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class GitDataCollector extends DataCollector
{
    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request A Request instance
     * @param Response $response A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'revision' => trim(shell_exec('git rev-parse --short HEAD')),
            'branch' => trim(shell_exec('git rev-parse --abbrev-ref HEAD'))
        );
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'git';
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->data['revision'];
    }

    /**
     * @return string
     */
    public function getBranch()
    {
        return $this->data['branch'];
    }
}
