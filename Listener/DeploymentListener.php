<?php

namespace BeSimple\DeploymentBundle\Listener;

use BeSimple\DeploymentBundle\Deployer\Config;
use BeSimple\DeploymentBundle\Deployer\Ssh;
use BeSimple\DeploymentBundle\Event\DeployerEvent;

class DeploymentListener
{

    /**
     * @var Ssh
     */
    private $ssh;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Ssh $ssh
     * @param Config $config
     */
    public function __construct(Ssh $ssh, Config $config)
    {
        $this->ssh = $ssh;
        $this->config = $config;
    }

    public function onDeploymentStart(DeployerEvent $event)
    {
        $timestamp = date('Ymd_His');

        $config = $this->config->getServerConfig($event->getServer());

        $deployPath = $config['connection']['path'];

        $changeDir = sprintf('mkdir %s/%s', $deployPath, $timestamp);
        $command = array(
            'type'      => 'shell',
            'command'   => $changeDir,
            'env'       => array()
        );

        $this->ssh->run($config['connection'], array($command), true);

        $this->config->setRelease($timestamp);
    }
} 