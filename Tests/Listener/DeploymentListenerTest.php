<?php

namespace BeSimple\DeploymentBundle\Tests\Listener;


use BeSimple\DeploymentBundle\Deployer\Config;
use BeSimple\DeploymentBundle\Event\DeployerEvent;
use BeSimple\DeploymentBundle\Listener\DeploymentListener;

class DeploymentListenerTest extends \PHPUnit_Framework_TestCase
{
    private $ssh;

    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $this->ssh = $this->getMock('BeSimple\DeploymentBundle\Deployer\Ssh', array(), array(), '', false);

        $this->config = $this->createConfig();
    }

    private function createConfig()
    {
        return new Config(array(), array(), array(
            'staging' => array(
                'host'      => 'host.de',
                'username'  => 'username',
                'password'  => 'password',
                'path'      => 'path/to/project',
                'rules'     => array(),
                'commands'  => array()
            )
        ));
    }

    private function getConnection(Config $config, $server)
    {
        $config = $config->getServerConfig($server);

        return $config['connection'];
    }
    /**
     * @test
     */
    public function everyDeploymentShouldCreateANewReleaseFolder()
    {
        $timestamp = date('Ymd_His');
        $commands = array(array(
            'type' => 'shell',
            'command' => 'mkdir path/to/project/'. $timestamp,
            'env'   => array()
        ));

        $this->ssh->expects($this->once())
            ->method('run')
            ->with($this->getConnection($this->config, 'staging'), $commands, true);


        $listener = new DeploymentListener($this->ssh, $this->config);
        $listener->onDeploymentStart(new DeployerEvent('staging', true));

        $this->assertEquals($timestamp, $this->config->getRelease());
    }
}
 