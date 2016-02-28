<?php

/*
 * This file is part of Boombox package
 *
 * Copyright (c) 2016 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/boombox
 *
 */

namespace Test\Mapper\Statsd;

use Boombox\Statsd\Metric\Gauge;
use Boombox\Statsd\Metric\Timing;

use Boombox\Statsd\Adapter as StatsdAdapter;
use Domnikl\Statsd\Connection\InMemory as InMemoryConnection;
use Domnikl\Statsd\Client as StatsdClient;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSendMetrics()
    {

        $connection = new InMemoryConnection;
        $statsd = new StatsdClient($connection, "test.namespace");
        $adapter = new StatsdAdapter($statsd);

        $metrics = [
            "foo.bar" => new Timing(200),
            "foo.pop" => new Timing(123),
            "foo.com" => new Gauge(91233)
        ];

        $adapter->send($metrics);
        $messages = $connection->getMessages();

        $this->assertEquals("test.namespace.foo.bar:200|ms", $messages[0]);
        $this->assertEquals("test.namespace.foo.pop:123|ms", $messages[1]);
        $this->assertEquals("test.namespace.foo.com:91233|g", $messages[2]);

    }
}
