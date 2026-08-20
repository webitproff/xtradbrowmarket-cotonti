<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.flags
Order=10
[END_COT_EXT]
==================== */
/**
 * Установка флага видимости колонок xtradbrowmarket в шаблоне массового редактирования.
 *
 * Файл подключается к хуку `marketmassedit.massedit.flags`, который вызывается
 * в плагине `marketmassedit` (файл marketmassedit.admin.php) после определения
 * основных флагов видимости колонок таблицы массового редактирования.
 *
 * В момент вызова хука доступна переменная:
 *   - `$t` (XTemplate) — объект шаблона, в который присваиваются флаги.
 *
 * Назначение файла:
 *   - Установить переменную `SHOW_XTRA = 1`.
 *   - Благодаря этому в шаблоне `marketmassedit.admin.tpl` срабатывает условие
 *     `<!-- IF {SHOW_XTRA} -->` и отображаются дополнительные колонки
 *     плагина xtradbrowmarket:
 *       * заголовки — блок `XTRA_HEADER` (хук `massedit.headers`);
 *       * ячейки данных — блок `XTRA_COLUMN` (хук `massedit.loop`).
 *
 * Прямые связи:
 *   - Хук:            `marketmassedit.massedit.flags` (определён в плагине marketmassedit).
 *   - Основной файл:  plugins/marketmassedit/marketmassedit.admin.php
 *   - Шаблон:         plugins/marketmassedit/tpl/marketmassedit.admin.tpl
 *   - Парный хук:     `marketmassedit.massedit.headers` — выводит заголовки.
 *   - Парный хук:     `marketmassedit.massedit.loop` — выводит поля.
 *   - Парный хук:     `marketmassedit.massedit.sql.fields` — добавляет данные в выборку.
 *
 * Важно: файл не выводит данные и не изменяет SQL, а только управляет
 * отображением колонок через флаг SHOW_XTRA.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.flags.php
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
$t->assign('SHOW_XTRA', 1);
