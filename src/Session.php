<?php

/*
 * This file is part of the laraport/session package.
 *
 * (c) 2016 Kamal Khan <shout@bhittani.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Laraport;

use SessionHandlerInterface;
use Illuminate\Session\Store;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Session\FileSessionHandler;
use Illuminate\Session\DatabaseSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class Session extends Store
{
    protected $Handler;

    protected $config;

    public function __construct($argument = null)
    {
        $this->config = require __DIR__ . '/config.php';

        if(is_array($argument))
            return $this->setConfig($argument);

        if($argument instanceof SessionHandlerInterface)
            return $this->setSessionHandler($argument);
    }

    protected function dispatch()
    {
        $name = $this->getName();

        if(isset($_COOKIE[$name]) && $sessionId = $_COOKIE[$name])
        {
            $this->setId($sessionId);
        }
        else if(!isset($_COOKIE[$name]))
        {
            setcookie(
                $name,
                $this->getId(),
                time() + 60 * $this->config['lifetime'],
                $this->config['path'],
                $this->config['domain'],
                $this->config['secure'],
                $this->config['http_only']
            );
        }

        parent::start();

        register_shutdown_function([$this, 'save']);
    }

    protected function arrayHandler()
    {
        return new NullSessionHandler;
    }

    protected function fileHandler()
    {
        return new FileSessionHandler(new Filesystem, $this->config['files'], $this->config['lifetime']);
    }

    protected function databaseHandler()
    {
        $Database = new Manager;
        $config = $this->config['connection'];
        $Database->addConnection($config);
        $Connection = $Database->getConnection('default');
        $Schema = $Connection->getSchemaBuilder();
		if(!$Schema->hasTable($this->config['table']))
		{
			$Schema->create($this->config['table'], function($Table)
			{
				$Table->string('id')->unique();
				$Table->text('payload');
				$Table->integer('last_activity');
			});
		}
        return new DatabaseSessionHandler($Connection, $this->config['table'], $this->config['lifetime']);
    }

    public function setConfig(array $config)
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setSessionHandler(SessionHandlerInterface $Handler)
    {
        $this->Handler = $Handler;
        return $this;
    }

    public function getSessionHandler()
    {
        return $this->Handler;
    }

    public function start()
    {
        $name = $this->config['cookie'];
        $lifetime = $this->config['lifetime'];
        $sessionId = $this->generateSessionId();

        if($this->config['driver'] instanceof SessionHandlerInterface)
        {
            $this->setSessionHandler($this->config['driver']);
            parent::__construct($name, $this->Handler);
            return $this->dispatch();
        }

        switch($this->config['driver'])
        {
            case 'database':
                parent::__construct($name, $this->databaseHandler());
            break;
            case 'file':
                parent::__construct($name, $this->fileHandler());
            break;
            default:
            case 'array':
                parent::__construct($name, $this->arrayHandler());
            break;
        }

        return $this->dispatch();
    }
}
