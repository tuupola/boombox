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

namespace Boombox\Mapper;

use Boombox\Metric\Timing;
use Boombox\Metric\Gauge;

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

        return [
            /* http://www.lognormal.com/boomerang/doc/api/RT.html */
            "roundtrip.firstbyte" =>
                new Timing($input["t_resp"]),
            "roundtrip.lastbyte" =>
                new Timing($input["t_page"]),
            "roundtrip.loadtime" =>
                new Timing($input["t_done"]),

            "navtiming.unload" =>
                new Timing($input["nt_unload_end"] - $input["nt_unload_st"]),
            "navtiming.redirect" =>
                new Timing($input["nt_red_end"] - $input["nt_red_st"]),
            "navtiming.cache" =>
                new Timing($input["nt_dns_st"] - $input["nt_fet_st"]),
            "navtiming.dns" =>
                new Timing($input["nt_dns_end"] - $input["nt_dns_st"]),
            "navtiming.tcp" =>
                new Timing($input["nt_con_end"] - $input["nt_con_st"]),
            "navtiming.request" =>
                new Timing($input["nt_res_st"] - $input["nt_req_st"]),
            "navtiming.response" =>
                new Timing($input["nt_res_end"] - $input["nt_res_st"]),
            "navtiming.processing" =>
                new Timing($input["nt_domcomp"] - $input["nt_domloading"]),
            "navtiming.onload" =>
                new Timing($input["nt_load_end"] - $input["nt_load_st"]),
            "navtiming.loadtime" =>
                new Timing($input["nt_load_end"] - $input["nt_nav_st"]),

            /* parse_str() converts . to _ */
            "memory.total" =>
                new Gauge($input["mem_total"]),
            "memory.used" =>
                new Gauge($input["mem_used"]),
        ];
    }
}
