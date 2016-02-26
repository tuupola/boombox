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
        $input = "tags=ipad&rt.start=navigation&rt.tstart=1456399799280"
               . "&rt.bstart=1456399799669&rt.end=1456399799897&t_resp=112"
               . "&t_page=505&t_done=617&t_other=t_domloaded%7C527&"
               . "r=http%3A%2F%2Fvagrant-sangar.local%2Fipad%2Fmen&r2="
               . "&nt_red_cnt=0&nt_nav_type=1&nt_nav_st=1456399799280"
               . "&nt_red_st=0&nt_red_end=0&nt_fet_st=1456399799280"
               . "&nt_dns_st=1456399799284&nt_dns_end=1456399799285"
               . "&nt_con_st=1456399799285&nt_con_end=1456399799286"
               . "&nt_req_st=1456399799286&nt_res_st=1456399799392"
               . "&nt_res_end=1456399799394&nt_domloading=1456399799439"
               . "&nt_domint=1456399799783&nt_domcontloaded_st=1456399799783"
               . "&nt_domcontloaded_end=1456399799807&nt_domcomp=1456399799896"
               . "&nt_load_st=1456399799896&nt_load_end=1456399799896"
               . "&nt_unload_st=1456399799429&nt_unload_end=1456399799429"
               . "&nt_spdy=0&nt_cinf=http%2F1&nt_first_paint=0"
               . "&u=http%3A%2F%2Fvagrant-sangar.local%2Fipad%2Fmen&v=0.9"
               . "&vis.st=visible&dom.res=39&dom.doms=3&mem.total=24500000"
               . "&mem.used=17100000&scr.xy=1440x900&scr.bpp=24%2F24"
               . "&scr.orn=0%2Flandscape-primary&scr.dpx=1.5&cpu.cnc=8"
               . "&dom.ln=234&dom.sz=26890&dom.img=0&dom.script=19"
               . "&dom.script.ext=15";

        $connection = new InMemoryConnection;
        $statsd = new StatsdClient($connection, "foo");
        $adapter = new StatsdAdapter($statsd);

        $mapper = new BoomerangMapper([
            "input" => $input
        ]);

        $boombox = new Boombox([
            "mapper" => $mapper,
            "adapter" => $adapter
        ]);

        $boombox->send();
        $messages = $connection->getMessages();

        $this->assertInstanceOf("Boombox\Mapper\Boomerang", $boombox->mapper());
        $this->assertInstanceOf("Boombox\Adapter\Statsd", $boombox->adapter());

        $this->assertEquals("foo.roundtrip.firstbyte:112|ms", $messages[0]);
        $this->assertEquals("foo.roundtrip.lastbyte:505|ms", $messages[1]);
        $this->assertEquals("foo.roundtrip.loadtime:617|ms", $messages[2]);
        $this->assertEquals("foo.navtiming.unload:0|ms", $messages[3]);
        $this->assertEquals("foo.navtiming.redirect:0|ms", $messages[4]);
        $this->assertEquals("foo.navtiming.cache:4|ms", $messages[5]);
        $this->assertEquals("foo.navtiming.dns:1|ms", $messages[6]);
        $this->assertEquals("foo.navtiming.tcp:1|ms", $messages[7]);
        $this->assertEquals("foo.navtiming.request:106|ms", $messages[8]);
        $this->assertEquals("foo.navtiming.response:2|ms", $messages[9]);
        $this->assertEquals("foo.navtiming.processing:457|ms", $messages[10]);
        $this->assertEquals("foo.navtiming.onload:0|ms", $messages[11]);
        $this->assertEquals("foo.navtiming.loadtime:616|ms", $messages[12]);
        $this->assertEquals("foo.memory.total:24500000|g", $messages[13]);
        $this->assertEquals("foo.memory.used:17100000|g", $messages[14]);
    }
}
