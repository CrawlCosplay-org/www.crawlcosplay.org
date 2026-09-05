-- Migration: Add game_score column to submissions table
-- This column is used for sorting submissions on the scoreboard

ALTER TABLE `submissions` ADD COLUMN `game_score` int(11) DEFAULT NULL AFTER `score`;
