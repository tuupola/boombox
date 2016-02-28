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

namespace Boombox\Statsd\Mapper;

use Boombox\Statsd\Metric\Timing;
use Boombox\Statsd\Metric\Gauge;

class Boomerang
{
    use \Witchcraft\Hydrate;
    use \Witchcraft\MagicMethods;

    private $options = [
        "input" => null
    ];

    public function __construct($options)
    {
        $this->hydrate($options);
    }

    public function setInput($input)
    {
        parse_str($input, $this->options["input"]);
        return $this;
    }

    public function getInput()
    {
        return $this->options["input"];
    }

    public function transform()
    {
        $input = $this->getInput();
        $metrics = [];

        /* http://www.lognormal.com/boomerang/doc/api/RT.html */
        if (isset($input["t_resp"])) {
            $metrics["roundtrip.firstbyte"] = new Timing($input["t_resp"]);
        }
        /*
        if (isset($input["t_page"]) && isset($input["t_resp"])) {
            $metrics["roundtrip.lastbyte"] = new Timing($input["t_page"] + $input["t_resp"]);
        }
        */
        if (isset($input["t_page"])) {
            $metrics["roundtrip.lastbyte"] = new Timing($input["t_page"]);
        }
        if (isset($input["t_done"])) {
            $metrics["roundtrip.loadtime"] = new Timing($input["t_done"]);
        }

        if (isset($input["nt_unload_end"]) && isset($input["nt_unload_st"])) {
            $metrics["navtiming.unload"] = new Timing($input["nt_unload_end"] - $input["nt_unload_st"]);
        }
        if (isset($input["nt_red_end"]) && isset($input["nt_red_st"])) {
            $metrics["navtiming.redirect"] = new Timing($input["nt_red_end"] - $input["nt_red_st"]);
        }
        if (isset($input["nt_dns_st"]) && isset($input["nt_fet_st"])) {
            $metrics["navtiming.cache"] = new Timing($input["nt_dns_st"] - $input["nt_fet_st"]);
        }
        if (isset($input["nt_dns_end"]) && isset($input["nt_dns_st"])) {
            $metrics["navtiming.dns"] = new Timing($input["nt_dns_end"] - $input["nt_dns_st"]);
        }
        if (isset($input["nt_con_end"]) && isset($input["nt_con_st"])) {
            $metrics["navtiming.tcp"] = new Timing($input["nt_con_end"] - $input["nt_con_st"]);
        }
        if (isset($input["nt_res_st"]) && isset($input["nt_req_st"])) {
            $metrics["navtiming.request"] = new Timing($input["nt_res_st"] - $input["nt_req_st"]);
        }
        if (isset($input["nt_res_end"]) && isset($input["nt_res_st"])) {
            $metrics["navtiming.response"] = new Timing($input["nt_res_end"] - $input["nt_res_st"]);
        }
        if (isset($input["nt_domcomp"]) && isset($input["nt_domloading"])) {
            $metrics["navtiming.processing"] = new Timing($input["nt_domcomp"] - $input["nt_domloading"]);
        }
        if (isset($input["nt_load_end"]) && isset($input["nt_load_st"])) {
            $metrics["navtiming.onload"] = new Timing($input["nt_load_end"] - $input["nt_load_st"]);
        }
        if (isset($input["nt_load_end"]) && isset($input["nt_nav_st"])) {
            $metrics["navtiming.loadtime"] = new Timing($input["nt_load_end"] - $input["nt_nav_st"]);
        }

        if (isset($input["mem_total"])) {
            $metrics["memory.total"] = new Gauge($input["mem_total"]);
        }
        if (isset($input["mem_used"])) {
            $metrics["memory.used"] = new Gauge($input["mem_used"]);
        }

        return $metrics;
    }
}
