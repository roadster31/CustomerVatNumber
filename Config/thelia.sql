
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- customer_vat_number
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_vat_number`;

CREATE TABLE `customer_vat_number`
(
    `id` INTEGER NOT NULL,
    `vat_number` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_customer_vat_number_customer_id`
        FOREIGN KEY (`id`)
        REFERENCES `customer` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
