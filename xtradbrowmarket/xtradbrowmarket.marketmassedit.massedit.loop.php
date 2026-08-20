<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.loop
Order=10
[END_COT_EXT]
==================== */

/**
 * Вывод полей плагина xtradbrowmarket в строку таблицы массового редактирования.
 *
 * Файл подключается к хуку `marketmassedit.massedit.loop`, который вызывается
 * в плагине `marketmassedit` (файл marketmassedit.admin.php) внутри цикла
 * формирования каждой строки таблицы массового редактирования.
 *
 * В момент вызова хука доступны переменные:
 *   - `$row` (array) — данные текущей строки товара, включая поля из
 *     таблицы `cot_xtradbrowmarket` (благодаря SQL-хуку
 *     `marketmassedit.massedit.sql.fields`).
 *   - `$id`  (int)   — ID текущего товара.
 *   - `$t`   (XTemplate) — объект шаблона, в который выводятся данные.
 *
 * Назначение файла:
 *   - Загрузить все экстраполя плагина через `xtradbrowmarket_getExtrafields()`.
 *   - Для каждого экстраполя получить значение из текущей строки `$row[$fname]`.
 *   - Сформировать HTML-элемент формы с помощью `cot_build_extrafields()`,
 *     используя имя поля `rxtra_<имя_поля>[<id>]` для корректной передачи
 *     в POST при массовом сохранении.
 *   - Присвоить полученный HTML шаблонной переменной `XTRA_COLUMN_HTML`
 *     и распарсить блок `MAIN.MANAGE_ROW.XTRA_COLUMN`, чтобы поле отобразилось
 *     в дополнительной колонке таблицы массового редактирования.
 *
 * Прямые связи:
 *   - Хук:                `marketmassedit.massedit.loop` (определён в плагине marketmassedit).
 *   - Основной файл:      plugins/marketmassedit/marketmassedit.admin.php
 *   - Функция списка полей: `xtradbrowmarket_getExtrafields()` — plugins/xtradbrowmarket/inc/xtradbrowmarket.functions.php
 *   - API extrafields:    `cot_build_extrafields()` — system/extrafields.php
 *   - Таблица:            `cot_xtradbrowmarket` (данные из `$row`)
 *   - Парный хук:         `marketmassedit.massedit.headers` — формирует заголовки колонок.
 *   - Парный хук:         `marketmassedit.massedit.sql.fields` — добавляет данные в выборку.
 *
 * Важно: файл не сохраняет данные, а только строит форму. Само сохранение
 * выполняется в хуке `marketmassedit.massedit.save`.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.loop.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 21Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('xtradbrowmarket', 'plug');

$extrafields = xtradbrowmarket_getExtrafields();
if (!empty($extrafields)) {
    foreach ($extrafields as $exfld) {
        $fname = $exfld['field_name'];
        $value = $row[$fname] ?? null;
        $inputName = 'rxtra_' . $fname . '[' . $id . ']';
        $fieldHtml = cot_build_extrafields($inputName, $exfld, $value);
        $t->assign('XTRA_COLUMN_HTML', $fieldHtml);
        $t->parse('MAIN.MANAGE_ROW.XTRA_COLUMN');
    }
}
