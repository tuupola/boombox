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

use Boombox\Boombox;
use Boombox\Mapper\Boomerang as BoomerangMapper;

class BoomerangMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldTransformQueryString()
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

        $mapper = new BoomerangMapper([
            "input" => $input
        ]);

        $output = $mapper->transform();

        $this->assertEquals("gauge", $output["memory.used"]->type());
        $this->assertEquals(17100000, $output["memory.used"]->value());

        $this->assertEquals("timing", $output["navtiming.request"]->type());
        $this->assertEquals(106, $output["navtiming.request"]->value());
    }
}
