<?php

namespace app\models;

class CCCSeasons extends BaseModel
{

    static $connection = "default";
    static $table = "ccc_seasons";
    static $fields = [
        'id', 'name', 'description', 'year', 'season', 'week',
        'icon', 'reddit', 'wiki', 'shortform', 'special_rule',
        'background', 'gods', 'species', 'bonus',
        'conduct_1', 'conduct_2', 'conduct_3', 'bonus_1', 'bonus_2',
        'conduct_name_1', 'conduct_name_2', 'conduct_name_3', 'bonus_name_1', 'bonus_name_2',
        'active', 'draft', 'created'
    ];

    public static function active(): ?CCCSeasons
    {
        $query = "SELECT * FROM `ccc_seasons` WHERE `active` = 1 AND `draft` = 0 ".
            "ORDER BY `year` DESC, `season` DESC, `week` DESC LIMIT 1;";
        $result = static::db()->query($query);
        if ($result) {
            $data = array_pop($result);
            return new CCCSeasons($data);
        } else {
            return null;
        }
    }

    public static function deactivateAll(): bool
    {
        $res = static::db()->update(static::$table, ['active' => 1], ['active' => 0]);
        return $res > 0;
    }

    public static function list()
    {
        $all = static::find(['draft' => 0], ['order' => '`year` DESC, `season` DESC, `week` DESC']);
        $list = [];
        foreach ($all as $p) {
            $list[$p->id] = $p->year . ' Season ' . $p->season . ' Week ' . $p->week . ' : ' . $p->name;
        }
        return $list;
    }

    public static function findBySeasons(
        bool $include_drafts,
        int $limit = 50,
        int $offset = 0,
        bool $chronological = false
    ): array
    {
        $query = 'SELECT * FROM `ccc_seasons` ';

        $ascdesc = "DESC";
        if ($chronological) $ascdesc = "ASC";

        $query .= ($include_drafts) ? '' : 'WHERE `draft` = 0 ';
        $query .= 'ORDER BY `year` ' . $ascdesc . ' , `season` ' . $ascdesc . ' , `week` ' . $ascdesc . ' '.
            "LIMIT {$offset},{$limit};";

        $result = static::db()->query($query);
        $all = [];
        foreach ($result as $row) {
            $all[$row['id']] = new CCCSeasons($row);
        }
        return $all;
    }
}
