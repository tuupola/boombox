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

namespace Boombox\InfluxDB;

use InfluxDB\Client;

class Adapter
{
    use \Witchcraft\Hydrate;
    use \Witchcraft\MagicProperties;

    private $options = [
        "client" => null
    ];

    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    public function send(array $points)
    {
        return $this
                ->client
                ->writePoints($points, Database::PRECISION_SECONDS);
    }

    public function setClient(Client $client)
    {
        $this->options["client"] = $client;
        return $this;
    }

    public function getClient()
    {
        return $this->options["client"];
    }
}
