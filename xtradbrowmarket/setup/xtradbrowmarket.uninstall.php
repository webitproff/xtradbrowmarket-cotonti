<?php
/**
 * xtradbrowmarket.uninstall.php – Полное удаление данных плагина при деинсталляции
 *
 * Удаляет:
 * - Все записи в таблице cot_extra_fields, относящиеся к таблице $db_xtradbrowmarket
 * - Таблицу $db_xtradbrowmarket_i18n (переводы)
 * - Таблицу $db_xtradbrowmarket (основная)
 * - Папку с загруженными файлами (datas/exflds/xtradbrowmarket)
 *
 * Custom Extrafields Market i18n plugin for Cotonti v1.+, PHP 8.4+, MySQL 8.4 
 *
 * Date: Jul 18, 2026
 * @package xtradbrowmarket
 * @version 3.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Подключаем файл плагина для регистрации глобальных переменных ($db_xtradbrowmarket)
require_once cot_incfile('xtradbrowmarket', 'plug');

global $db, $db_extra_fields, $db_xtradbrowmarket, $db_xtradbrowmarket_i18n;

// 1. Удаляем все определения экстраполей для нашей таблицы
$db->delete($db_extra_fields, "field_location = ?", [$db_xtradbrowmarket]);

// 2. Удаляем таблицу переводов (на случай, если SQL-файл не сработал или префикс отличается)
$db->query("DROP TABLE IF EXISTS `{$db_xtradbrowmarket_i18n}`");

// 3. Удаляем основную таблицу
$db->query("DROP TABLE IF EXISTS `{$db_xtradbrowmarket}`");

// 4. Удаляем папку с файлами
$exfld_dir = 'datas/exflds/xtradbrowmarket';
if (is_dir($exfld_dir)) {
    // Простая рекурсивная функция удаления директории
    function xtradbrowmarket_removeDir($dir) {
        if (!is_dir($dir)) return;
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? xtradbrowmarket_removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }
    xtradbrowmarket_removeDir($exfld_dir);
}
