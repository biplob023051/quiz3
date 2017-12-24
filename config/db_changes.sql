ALTER TABLE `quizzes` ADD `parent_quiz_id` INT(11) NULL AFTER `comment`;

-- Language distingush
ALTER TABLE `subjects` ADD `language` CHAR(10) NOT NULL DEFAULT 'fin' AFTER `type`;
ALTER TABLE `helps` ADD `language` CHAR(10) NOT NULL DEFAULT 'fin' AFTER `photo`;

ALTER TABLE `quizzes` ADD `language` CHAR(10) NOT NULL DEFAULT 'fin' AFTER `parent_quiz_id`;