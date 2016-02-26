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

namespace Boombox\Metric;

class Timing
{
    use \Witchcraft\Hydrate;
    use \Witchcraft\MagicMethods;

    private $options = [
        "value" => null
    ];

    public function __construct($value)
    {
        $this->setValue($value);
    }

    public function setValue($value)
    {
        $this->options["value"] = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->options["value"];
    }


    public function getType()
    {
        return "timing";
    }
}
