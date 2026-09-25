<?php

namespace app\models;

class Player extends BaseModel
{

    static $connection = "default";
    static $table = "players";
    static $fields = [
        'id', 'name', 'reddit', 'discord', 'created'
    ];
    static $relations = [
        'submissions' => ['type' => 'has_many', 'class' => Submission::class, 'local' => 'id', 'foreign' => 'player_id'],
    ];

    public static function list()
    {
    	$all = static::find([],['order' => '`name` ASC']);
    	$list = [];
    	foreach ($all as $p) {
            $list[$p->id] = $p->listName();
    	}
    	return $list;
    }

    public function listName(): string
    {
        return $this->name;
    }

    public function listNameAdmin(): string
    {
        $name = $this->name;
        if ($this->name != $this->reddit || $this->name != $this->discord) {
            $name .= " (";
        }
        if ($this->name != $this->reddit) {
            $name .= $this->reddit;
        }
        if ($this->name != $this->discord) {
            if ($this->name != $this->reddit) {
                $name .= " ";
            }
            $name .= $this->discord;
        }
        if ($this->name != $this->reddit || $this->name != $this->discord) {
            $name .= ")";
        }
        return $name;
    }
	
    public static function scoreboard()
    {
        $all = static::findAsArray([], ['with' => 'submissions', 'order' => '`name` ASC']);
        foreach ($all as $key => &$player) {
            $score = 0; $stars = 0; $subs = 0;
            foreach ($player->submissions() as $sub) {
                if ($sub->accepted && $sub->hs) {
                    $score += $sub->score;
                    $stars += $sub->stars;
                    $subs += 1;
                }
            }
            $player->subs = $subs;
            $player->score = $score;
            $player->stars = $stars;
        }
        return $all;
    }



		
public static function scoreboardForSet($set)
{
    $s = (int) $set;

    $challenges_in_set = Challenge::findAsArray(
        ['setnr' => $set, 'draft' => 0],
        ['order' => '`week` ASC']
    );

    /*
     * Find the Week 5 challenge for the final game-score tie-break.
     */
    $tie_breaker_id = 0;

    foreach ($challenges_in_set as $c) {
        if ((int) $c->week === 5 && !$c->bonus) {
            $tie_breaker_id = (int) $c->id;
            break;
        }
    }

    /*
     * Get all accepted submissions in this tournament.
     *
     * For each player + challenge, keep only the best submission each week:
     *
     *   1. Highest meta score
     *   2. Highest stars
     *   3. Earliest created
     *
     * The hs flag is deliberately not used here. The best submission
     * is now selected automatically instead of manually.
     */
    $q = "
        SELECT
            `p`.`id` AS `pid`,
            `p`.`name` AS `player`,
            `s`.`id` AS `submission_id`,
            `s`.`challenge_id`,
            `s`.`score`,
            `s`.`stars`,
            `s`.`game_score`,
            `s`.`morgue_url`,
            `s`.`created`,
            `c`.`bonus`
        FROM `submissions` AS `s`
        INNER JOIN `challenges` AS `c`
            ON `s`.`challenge_id` = `c`.`id`
        INNER JOIN `players` AS `p`
            ON `s`.`player_id` = `p`.`id`
        WHERE `s`.`accepted` = 1
          AND `c`.`setnr` = {$s}
          AND `c`.`draft` = 0
          AND NOT EXISTS (
              SELECT 1
              FROM `submissions` AS `s2`
              WHERE `s2`.`player_id` = `s`.`player_id`
                AND `s2`.`challenge_id` = `s`.`challenge_id`
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
            `p`.`name` ASC,
            `c`.`week` ASC
    ";

    $result = static::db()->query($q);

    /*
     * Build the scoreboard from the selected best submission
     * for each player + challenge.
     */
    $players = [];

    foreach ($result as $row) {
        $pid = (int) $row['pid'];
        $cid = (int) $row['challenge_id'];

        if (!isset($players[$pid])) {
            $players[$pid] = [
                'pid' => $pid,
                'player' => $row['player'],
                'total' => 0,
                'stars' => 0,
                'game_score' => null,
                'week' => []
            ];
        }

        /*
         * Store the selected best submission for this week.
         */
        $players[$pid]['week'][$cid] = [
            'score' => $row['score'],
            'stars' => $row['stars'],
            'morgue' => $row['morgue_url']
        ];

        /*
         * Bonus challenges are displayed but do not contribute
         * to the tournament total or total stars.
         */
        if (!$row['bonus']) {
            $players[$pid]['total'] += (int) $row['score'];
            $players[$pid]['stars'] += (int) $row['stars'];
        }

        /*
         * Week 5 game score comes from the SAME selected Week 5
         * submission. We do not independently select the highest
         * game_score.
         */
        if ($cid === $tie_breaker_id) {
            $players[$pid]['game_score'] = $row['game_score'];
        }
    }

    /*
     * Add null entries for challenges where a player has no submission.
     */
    foreach ($players as &$player) {
        foreach ($challenges_in_set as $c) {
            $cid = (int) $c->id;

            if (!isset($player['week'][$cid])) {
                $player['week'][$cid] = null;
            }
        }
    }
    unset($player);

    /*
     * Sort:
     *
     *   1. Total meta score descending
     *   2. Total stars descending
     *   3. Week 5 game score descending
     *   4. Player name ascending
     */
    $out = array_values($players);

    usort($out, function ($a, $b) {
        if ($a['total'] != $b['total']) {
            return ($a['total'] > $b['total']) ? -1 : 1;
        }

        if ($a['stars'] != $b['stars']) {
            return ($a['stars'] > $b['stars']) ? -1 : 1;
        }

        $a_game = ($a['game_score'] === null) ? null : (int) $a['game_score'];
        $b_game = ($b['game_score'] === null) ? null : (int) $b['game_score'];

        if ($a_game !== $b_game) {
            if ($a_game === null) {
                return 1;
            }

            if ($b_game === null) {
                return -1;
            }

            return ($a_game > $b_game) ? -1 : 1;
        }

        return strcasecmp($a['player'], $b['player']);
    });

    return $out;
}
	}
	
