<?php

namespace app\models;

class Submission extends BaseModel
{

    static $connection = "default";
    static $table = "submissions";
    static $fields = [
        'id', 'challenge_id', 'player_id', 'hs', 'accepted', 'online', 
        'score', 'game_score', 'stars', 'morgue_url', 'morgue_text', 'comment', 'late',
        'created'
    ];
    static $relations = [
        'player' => ['type' => 'belongs_to', 'class' => Player::class, 'local' => 'player_id', 'foreign' => 'id'],
        'challenge' => ['type' => 'belongs_to', 'class' => Challenge::class, 'local' => 'challenge_id', 'foreign' => 'id']
    ];

public static function scoreboard($challenge_id)
 {
    $id = (int) $challenge_id;

    $q = "
        SELECT
            `s`.*
        FROM `submissions` AS `s`
        WHERE `s`.`challenge_id` = {$id}
          AND `s`.`accepted` = 1
          AND NOT EXISTS (
              SELECT 1
              FROM `submissions` AS `s2`
              WHERE `s2`.`challenge_id` = `s`.`challenge_id`
                AND `s2`.`player_id` = `s`.`player_id`
                AND `s2`.`accepted` = 1
                AND (
                    `s2`.`score` > `s`.`score`
                    OR (
                        `s2`.`score` = `s`.`score`
                        AND `s2`.`stars` > `s`.`stars`
                    )
                    OR (
                        `s2`.`score` = `s`.`score`
                        AND `s2`.`stars` = `s`.`stars`
                        AND `s2`.`created` < `s`.`created`
                    )
                )
          )
        ORDER BY
            `s`.`score` DESC,
            `s`.`stars` DESC,
            `s`.`game_score` DESC,
            `s`.`created` ASC
    ";

    return static::db()->query($q);
 }


    public static function sendToModeration(array $conditions): bool
    {
        return static::db()->update(static::$table, $conditions, ['accepted' => 0, 'hs' => 0]) > 0;
    }

    public function isScoring(): bool
    {
        return $this->hs == 1 && $this->accepted == 1;
    }

    public static function getNumberOfUnscoredSubmissions(): int
    {
        $result = static::findAsArray(['accepted' => 0]);
        return count($result);
    }
}
