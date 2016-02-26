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

namespace Boombox\Adapter;

use Domnikl\Statsd\Client;

class Statsd
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

    public function send(array $metric)
    {
        $this->client->startBatch();
        foreach ($metric as $key => $item) {
            switch ($item->type()) {
                case "timing":
                    $this->client->timing($key, $item->value());
                    break;
                case "gauge":
                    $this->client->gauge($key, $item->value());
                    break;
            }
        }
        $this->client->endBatch();
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
