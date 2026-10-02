<?php

namespace app\models;

class CrawlSuccessionTurn extends BaseModel
{
    static $connection = "default";
    static $table = "crawl_succession_turns";

    static $fields = [
        'id',
        'game_id',
        'turn_number',
        'user_id',
        'notes',
        'started',
        'finished'
    ];
}
