<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.delete.done
  [END_COT_EXT]
==================== */


/* 
 * Файл не подключен и сохранен только для образовательных целей!!!!
 *
 * The file is not connected and is saved only for educational purposes!!!!
*/



/**
 * Удаление связанных данных при удалении страницы
 * Хук market.delete.done
 * 
 * Filename: plugins/xtradbrowmarket/_xtradbrowmarket.market.delete.done.php
 *
 * Custom Extrafields Market i18n plugin for Cotonti v1.+, PHP 8.4+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Jul 18, 2026
 * @package xtradbrowmarket
 * @version 3.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */
 


/**
 * Дополнительные действия после удаления товара: плагин xtradbrowmarket
 * Хук market.delete.done. Вызывается после физического удаления товара из cot_market,
 * но до завершения транзакции. Здесь можно удалить связанные данные, которые
 * не обрабатываются каскадным удалением.
 */

defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

if (isset($id) && $id > 0) {
    // Прямое удаление основной записи (если бы не было каскада)
    Cot::$db->delete(Cot::$db->xtradbrowmarket, "itempagid = ?", [$id]);

    // Прямое удаление переводов (если бы каскад не сработал)
    Cot::$db->delete(Cot::$db->xtradbrowmarket_i18n, "itempagid = ?", [$id]);

    // Можно также очистить файловый кэш, обновить счётчики и т.д.
}