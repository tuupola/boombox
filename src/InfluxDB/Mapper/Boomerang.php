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

namespace Boombox\InfluxDB\Mapper;

use InfluxDB\Point;

class Boomerang
{
    use \Witchcraft\Hydrate;
    use \Witchcraft\MagicMethods;

    private $options = [
        "input" => null,
        "tags.default" => [],
        "tags.include" => []
    ];

    public function __construct($options)
    {
        $this->hydrate($options);
    }

    public function setTagsDefault(array $tags)
    {
        $this->options["tags.default"] = $tags;
    }

    public function setTagsInclude(array $tags)
    {
        $this->options["tags.include"] = $tags;

    }

    public function getTags()
    {
        $tags = array_intersect_key(
            $this->options["input"],
            array_flip($this->options["tags.include"])
        );

        return $tags += $this->options["tags.default"];
    }

    public function setInput($input)
    {
        parse_str($input, $this->options["input"]);
        $this->options["input"] = array_map(function ($value) {
            if (is_numeric($value)) {
                return $value + 0;
            }
            return $value;
        }, $this->options["input"]);

        return $this;
    }

    public function getInput()
    {
        return $this->options["input"];
    }

    public function transform()
    {
        $input = $this->getInput();
        $tags = $this->getTags();
        $points = [];

        /* http://www.lognormal.com/boomerang/doc/api/RT.html */
        $roundtrip = [];

        if (isset($input["t_resp"])) {
            $roundtrip["firstbyte"] = $input["t_resp"];
        }
        if (isset($input["t_page"])) {
            $roundtrip["lastbyte"] = $input["t_page"];
        }
        if (isset($input["t_done"])) {
            $roundtrip["loadtime"] = $input["t_done"];
        }

        if (count($roundtrip)) {
            $points[] = new Point(
                "roundtrip",
                null,
                $tags,
                $roundtrip,
                null
            );
        }

        /* http://www.lognormal.com/boomerang/doc/api/navtiming.html */
        $navtiming = [];

        if (isset($input["nt_unload_end"]) && isset($input["nt_unload_st"])) {
            $navtiming["unload"] = $input["nt_unload_end"] - $input["nt_unload_st"];
        }
        if (isset($input["nt_red_end"]) && isset($input["nt_red_st"])) {
            $navtiming["redirect"] = $input["nt_red_end"] - $input["nt_red_st"];
        }
        if (isset($input["nt_dns_st"]) && isset($input["nt_fet_st"])) {
            $navtiming["cache"] = $input["nt_dns_st"] - $input["nt_fet_st"];
        }
        if (isset($input["nt_dns_end"]) && isset($input["nt_dns_st"])) {
            $navtiming["dns"] = $input["nt_dns_end"] - $input["nt_dns_st"];
        }
        if (isset($input["nt_con_end"]) && isset($input["nt_con_st"])) {
            $navtiming["tcp"] = $input["nt_con_end"] - $input["nt_con_st"];
        }
        if (isset($input["nt_res_st"]) && isset($input["nt_req_st"])) {
            $navtiming["request"] = $input["nt_res_st"] - $input["nt_req_st"];
        }
        if (isset($input["nt_res_end"]) && isset($input["nt_res_st"])) {
            $navtiming["response"] = $input["nt_res_end"] - $input["nt_res_st"];
        }
        if (isset($input["nt_domcomp"]) && isset($input["nt_domloading"])) {
            $navtiming["processing"] = $input["nt_domcomp"] - $input["nt_domloading"];
        }
        if (isset($input["nt_load_end"]) && isset($input["nt_load_st"])) {
            $navtiming["onload"] = $input["nt_load_end"] - $input["nt_load_st"];
        }
        if (isset($input["nt_load_end"]) && isset($input["nt_nav_st"])) {
            $navtiming["loadtime"] = $input["nt_load_end"] - $input["nt_nav_st"];
        }

        if (count($navtiming)) {
            $points[] = new Point(
                "navtiming",
                null,
                $tags,
                $navtiming,
                null
            );
        }

        /* https://github.com/lognormal/boomerang/blob/master/plugins/memory.js */
        $memory = [];

        if (isset($input["mem_total"])) {
            $memory["total"] = $input["mem_total"];
        }
        if (isset($input["mem_used"])) {
            $memory["used"] = $input["mem_used"];
        }

        if (count($memory)) {
            $points[] = new Point(
                "memory",
                null,
                $tags,
                $memory,
                null
            );
        }

        return $points;
    }
}
