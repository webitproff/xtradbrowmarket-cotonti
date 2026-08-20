<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.sql.fields
Order=10
[END_COT_EXT]
==================== */

/**
 * Добавление колонок таблицы xtradbrowmarket в SQL-запрос массового редактирования.
 *
 * Файл подключается к хуку `marketmassedit.massedit.sql.fields`, который вызывается
 * в плагине `marketmassedit` (файл marketmassedit.admin.php) на этапе формирования
 * динамического набора полей для выборки товаров.
 *
 * В момент вызова хука доступны переменные:
 *   - `$selectFields` (array) — текущий список полей, которые будут включены в SELECT.
 *   - `$needXtraJoin`  (bool|string) — если false, JOIN не нужен; если строка — имя таблицы для LEFT JOIN.
 *
 * Назначение файла:
 *   1. Добавить в `$selectFields` все колонки таблицы `cot_xtradbrowmarket` через
 *      `Cot::$db->xtradbrowmarket . '.*'`.
 *   2. Сообщить основному плагину `marketmassedit` о необходимости выполнить
 *      `LEFT JOIN` с таблицей `cot_xtradbrowmarket`, установив
 *      `$needXtraJoin = Cot::$db->xtradbrowmarket`.
 *
 * Прямые связи:
 *   - Хук: `marketmassedit.massedit.sql.fields` (определён в плагине marketmassedit).
 *   - Основной файл: plugins/marketmassedit/marketmassedit.admin.php
 *   - Таблица: `cot_xtradbrowmarket` (зарегистрирована в xtradbrowmarket.functions.php).
 *   - Условие JOIN: `cot_market.fieldmrkt_id = cot_xtradbrowmarket.itempagid`
 *     (реализуется в marketmassedit.admin.php после обработки хука).
 *
 * Важно: файл не производит выборку данных сам, а лишь модифицирует переменные
 * `$selectFields` и `$needXtraJoin`, которые используются в последующем SQL-запросе.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.sql.fields.php
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

// Добавляем все колонки таблицы xtradbrowmarket в общий список полей
$selectFields[] = Cot::$db->xtradbrowmarket . '.*';
// Сообщаем marketmassedit, что нужен LEFT JOIN с этой таблицей
$needXtraJoin = Cot::$db->xtradbrowmarket;
