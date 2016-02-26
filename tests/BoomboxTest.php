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

namespace Test;

use Boombox\Boombox;
use Boombox\Metric\Gauge;
use Boombox\Metric\Timing;

use Boombox\Adapter\Statsd as StatsdAdapter;
use Boombox\Mapper\Boomerang as BoomerangMapper;
use Domnikl\Statsd\Connection\InMemory as InMemoryConnection;
use Domnikl\Statsd\Client as StatsdClient;

class BoomboxTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldSendMetrics()
    {

        $connection = new InMemoryConnection;
        $statsd = new StatsdClient($connection, "test.namespace");
        $adapter = new StatsdAdapter($statsd);

        $mapper = new BoomerangMapper([
            "input" => "foo=bar"
        ]);

        $boombox = new Boombox([
            "mapper" => $mapper
        ]);

        $this->assertInstanceOf("Boombox\Mapper\Boomerang", $boombox->mapper());
    }
}
