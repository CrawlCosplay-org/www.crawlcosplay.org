<?php

namespace app\models;

class CrawlSuccessionUser extends BaseModel
{
    static $connection = "default";
    static $table = "crawl_succession_users";

    static $fields = [
        'id',
        'username',
        'password_hash',
        'discord_id',
        'created'
    ];
}
