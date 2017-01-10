ALTER TABLE `quizzes` CHANGE `random_id` `random_id` BIGINT(20) NULL;
ALTER TABLE `students` CHANGE `fname` `fname` CHAR(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `students` CHANGE `lname` `lname` CHAR(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;
ALTER TABLE `students` CHANGE `class` `class` CHAR(40) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;