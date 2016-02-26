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

namespace Test\Mapper;

use Boombox\Metric\Gauge;
use Boombox\Metric\Timing;

use Boombox\Adapter\Statsd as StatsdAdapter;
use Domnikl\Statsd\Connection\InMemory as InMemoryConnection;
use Domnikl\Statsd\Client as StatsdClient;

class StatsdAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSendMetrics()
    {

        $connection = new InMemoryConnection;
        $statsd = new StatsdClient($connection, "test.namespace");
        $adapter = new StatsdAdapter($statsd);

        $metrics = [
            new Timing(200),
            new Timing(123),
            new Gauge(91233)
        ];

        $adapter->send($metrics);
        $messages = $connection->getMessages();

        $this->assertEquals("test.namespace.0:200|ms", $messages[0]);
        $this->assertEquals("test.namespace.1:123|ms", $messages[1]);
        $this->assertEquals("test.namespace.2:91233|g", $messages[2]);

    }
}
