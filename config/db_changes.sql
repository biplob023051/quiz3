ALTER TABLE `users` ADD `customer_id` VARCHAR(255) NULL AFTER `imported_ids`;
ALTER TABLE `users` ADD `plan_switched` ENUM('DOWNGRADE','UPGRADE','CANCEL_DOWNGRADE','CANCEL_UPGRADE') NULL DEFAULT NULL AFTER `customer_id`;