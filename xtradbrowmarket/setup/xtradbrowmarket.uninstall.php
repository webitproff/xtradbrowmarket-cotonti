<?php
/**
 * xtradbrowmarket.uninstall.php – Полное удаление данных плагина при деинсталляции
 *
 * Удаляет:
 * - Все записи в таблице cot_extra_fields, относящиеся к таблице $db_xtradbrowmarket
 * - Саму таблицу $db_xtradbrowmarket (DROP TABLE IF EXISTS)
 *
 * @package xtradbrowmarket
 * @version 2.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Подключаем файл плагина для регистрации глобальных переменных ($db_xtradbrowmarket)
require_once cot_incfile('xtradbrowmarket', 'plug');

global $db, $db_extra_fields, $db_xtradbrowmarket;

// 1. Удаляем все определения экстраполей для нашей таблицы
$db->delete($db_extra_fields, "field_location = ?", [$db_xtradbrowmarket]);

// 2. Удаляем саму таблицу (на случай, если SQL-файл не сработал или префикс отличается)
$db->query("DROP TABLE IF EXISTS `{$db_xtradbrowmarket}`");