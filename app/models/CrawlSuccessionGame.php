<?php

namespace app\models;

class CrawlSuccessionGame extends BaseModel
{
    static $connection = "default";
    static $table = "crawl_succession_games";

    static $fields = [
        'id',
        'character_name',
        'species',
        'background',
        'god',
        'server',
        'min_players',
        'max_players',
        'description',
        'created_by',
        'current_user_id',
        'turn_number',
        'status',
        'result',
        'created',
        'started',
        'concluded'
    ];
}
