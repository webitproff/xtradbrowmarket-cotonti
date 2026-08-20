<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.headers
Order=10
[END_COT_EXT]
==================== */

/**
 * Вывод заголовков колонок xtradbrowmarket в таблице массового редактирования.
 *
 * Файл подключается к хуку `marketmassedit.massedit.headers`, который вызывается
 * в плагине `marketmassedit` (файл marketmassedit.admin.php) на этапе формирования
 * строки заголовков таблицы массового редактирования, после вывода заголовков
 * основных экстраполей модуля Market (если они включены).
 *
 * В момент вызова хука доступны переменные:
 *   - `$t`           (XTemplate) — объект шаблона.
 *   - `$extrafields` (array)     — массив экстраполей xtradbrowmarket (загружается ниже).
 *
 * Назначение файла:
 *   - Загрузить все экстраполя плагина через `xtradbrowmarket_getExtrafields()`.
 *   - Для каждого экстраполя сформировать локализованное название через
 *     `cot_extrafield_title($exfld, 'xtra_')`.
 *   - Присвоить это название шаблонной переменной `XTRA_HEADER_TITLE`
 *     и распарсить блок `MAIN.XTRA_HEADER`, добавив тем самым заголовок
 *     соответствующей колонки.
 *
 * Прямые связи:
 *   - Хук:                `marketmassedit.massedit.headers` (определён в плагине marketmassedit).
 *   - Основной файл:      plugins/marketmassedit/marketmassedit.admin.php
 *   - Функция полей:      `xtradbrowmarket_getExtrafields()` — plugins/xtradbrowmarket/inc/xtradbrowmarket.functions.php
 *   - API extrafields:    `cot_extrafield_title()` — system/extrafields.php
 *   - Парный хук:         `marketmassedit.massedit.loop` — выводит ячейки данных.
 *   - Парный хук:         `marketmassedit.massedit.flags` — устанавливает SHOW_XTRA,
 *                         благодаря которому шаблон отображает блок XTRA_HEADER.
 *
 * Важно: файл не формирует данные, а только выводит заголовки. Сами поля
 * выводятся в хуке `marketmassedit.massedit.loop`.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.headers.php
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
        $t->assign('XTRA_HEADER_TITLE', htmlspecialchars(cot_extrafield_title($exfld, 'xtra_')));
        $t->parse('MAIN.XTRA_HEADER');
    }
}
