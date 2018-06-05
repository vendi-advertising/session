<?php

/*
 * This file is part of the laraport/session package.
 *
 * (c) 2016 Kamal Khan <shout@bhittani.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Laraport\Session;
use Illuminate\Database\Capsule\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class SessionTest extends TestCase
{
    protected $defaultConfig;

    public function setUp()
    {
        $this->defaultConfig = require __DIR__ . '/../src/config.php';
    }

    public function assertions($Session)
    {
        $Session->put('hello', 'world');
        $this->assertEquals('world', $Session->get('hello'));
        $Session->flash('lights', 'green');
        $this->assertEquals('green', $Session->get('lights'));
        $Session->flush();
        $this->assertEquals([], $Session->all());
        $Session->put('hello', 'again');
        $this->assertEquals('again', $Session->get('hello'));
    }

    /** @test */
    public function it_should_init_with_default_configuration()
    {
        $Session = new Session;
        $this->assertEquals($this->defaultConfig, $Session->getConfig());
    }

    /** @test */
    public function it_should_allow_construction_with_config()
    {
        $config = [
            'driver' => 'database',
            'foo' => 'bar'
        ];
        $Session = new Session($config);
        $assertion = array_replace($this->defaultConfig, $config);
        $this->assertEquals($assertion, $Session->getConfig());
    }

    /** @test */
    public function it_should_allow_construction_with_custom_handler()
    {
        $Session = new Session(new NullSessionHandler);
        $this->assertTrue(
            $Session->getSessionHandler() instanceof SessionHandlerInterface
        );
    }

    /** @test */
    public function it_should_default_to_array_session_handler()
    {
        $Session = new Session;
        $Session->start();
        $this->assertions($Session);
    }

    /** @test */
    public function it_should_support_the_file_session_handler()
    {
        $fileSessionPath = __DIR__;

        $Session = new Session([
            'driver' => 'file',
            'files' => $fileSessionPath
        ]);

        $Session->start();
        $this->assertions($Session);

        $filename = $Session->getId();
        $this->assertEquals(40, strlen($filename));

        $filepath = $fileSessionPath . '/' . $filename;

        register_shutdown_function(function() use($filepath){
            $this->assertFileExists($filepath);
            unlink($filepath);
        });
    }

    /** @test */
    public function it_should_support_the_database_session_handler()
    {
        $database = __DIR__ . '/session.sqlite';
        if(file_exists($database)) unlink($database);
        touch($database);

        $connection = [
            'driver'   => 'sqlite',
            'database' => $database,
            'prefix'   => ''
        ];

        $table = 'sessions';

        $Session = new Session([
            'driver' => 'database',
            'connection' => $connection,
            'table' => $table
        ]);

        $Session->start();
        $this->assertions($Session);

        $sid = $Session->getId();

        register_shutdown_function(function() use($sid, $connection, $table){
            $Database = new Manager;
            $Database->addConnection($connection);

            $Db = $Database->getDatabaseManager();
            $results = $Db->select('select id from ' . $table . ' LIMIT 1');

            $this->assertEquals(1, count($results));
            $row = array_shift($results);
            $this->assertEquals($sid, $row['id']);

            unlink($connection['database']);
        });
    }
}
