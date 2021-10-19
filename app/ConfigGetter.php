<?php

namespace App;

class ConfigGetter
{
    public static function getConfig(): array
    {
        return json_decode(file_get_contents("config.json"), true);
    }
}