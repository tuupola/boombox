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

namespace Boombox;

use Boombox\Mapper\Boomerang;

class Boombox
{
    use \Witchcraft\Hydrate;
    use \Witchcraft\MagicMethods;

    private $options = [
        "mapper" => null
    ];

    public function __construct($options)
    {
        $this->hydrate($options);
    }

    public function setMapper($mapper)
    {
        $this->options["mapper"] = $mapper;
        return $this;
    }

    public function getMapper()
    {
        return $this->options["mapper"];
    }
}
