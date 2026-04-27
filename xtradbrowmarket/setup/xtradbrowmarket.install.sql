-- xtradbrowmarket
-- plugins/xtradbrowmarket/setup/xtradbrowmarket.install.sql
-- Установочный файл: plugins/xtradbrowmarket/setup/xtradbrowmarket.install.sql
-- Создаёт таблицу только с itemmrkt_id – все остальные столбцы будут добавлены через API Extrafields.

CREATE TABLE IF NOT EXISTS `cot_xtradbrowmarket` (
    `itempagid` int UNSIGNED NOT NULL,
    PRIMARY KEY (`itempagid`),
    CONSTRAINT `fk_xtradbrowmarket_market` 
        FOREIGN KEY (`itempagid`) 
        REFERENCES `cot_market` (`fieldmrkt_id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;