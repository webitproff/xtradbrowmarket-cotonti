-- Установочный файл: plugins/xtradbrowmarket/setup/xtradbrowmarket.install.sql
-- Создаёт таблицу только с itempagid – все остальные столбцы будут добавлены через API Extrafields.
-- Добавлена таблица переводов для мультиязычности (cot_xtradbrowmarket_i18n).
-- 
-- Custom Extrafields Market i18n plugin for Cotonti v1.+, PHP 8.4+, MySQL 8.4 
-- 
-- Date: Jul 18, 2026
-- package xtradbrowmarket
-- version 3.0.0
-- author webitproff
-- copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
-- license BSD
-- 


CREATE TABLE IF NOT EXISTS `cot_xtradbrowmarket` (
    `itempagid` int UNSIGNED NOT NULL,
    PRIMARY KEY (`itempagid`),
    CONSTRAINT `fk_xtradbrowmarket_market` 
        FOREIGN KEY (`itempagid`) 
        REFERENCES `cot_market` (`fieldmrkt_id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица переводов значений экстраполей (мультиязычность)
CREATE TABLE IF NOT EXISTS `cot_xtradbrowmarket_i18n` (
    `itempagid` INT UNSIGNED NOT NULL,
    `field_name` VARCHAR(255) NOT NULL,
    `lang` CHAR(2) NOT NULL DEFAULT 'en',
    `value` TEXT,
    PRIMARY KEY (`itempagid`, `field_name`, `lang`),
    CONSTRAINT `fk_xtradbrowmarket_i18n` 
        FOREIGN KEY (`itempagid`) REFERENCES `cot_xtradbrowmarket` (`itempagid`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
