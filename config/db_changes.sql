ALTER TABLE `quizzes` ADD `parent_quiz_id` INT(11) NULL AFTER `comment`;

-- Language distingush
ALTER TABLE `subjects` ADD `language` CHAR(10) NOT NULL DEFAULT 'fi' AFTER `type`;
ALTER TABLE `helps` ADD `language` CHAR(10) NOT NULL DEFAULT 'fi' AFTER `photo`;

ALTER TABLE `quizzes` ADD `language` CHAR(10) NOT NULL DEFAULT 'fi' AFTER `parent_quiz_id`;

UPDATE `users` SET language = 'fi' WHERE language = 'fin';

ALTER TABLE `settings` ADD `language` CHAR(10) NOT NULL DEFAULT 'fi' AFTER `value`;