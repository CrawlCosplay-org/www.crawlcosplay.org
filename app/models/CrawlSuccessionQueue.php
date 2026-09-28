<?php

namespace app\models;

class CrawlSuccessionQueue extends BaseModel
{
    static $connection = "default";
    static $table = "crawl_succession_queue";

    static $fields = [
        'id',
        'game_id',
        'user_id',
        'position',
        'active',
        'joined'
    ];
}
