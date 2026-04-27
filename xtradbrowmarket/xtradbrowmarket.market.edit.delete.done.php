<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.edit.delete.done
  [END_COT_EXT]
==================== */

/**
 * Удаление связанных данных при удалении страницы: plugins/xtradbrowmarket/xtradbrowmarket.market.edit.delete.done.php
 * Хук market.edit.delete.done.
 * 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Apr 27Th, 2026
 * @package xtradbrowmarket
 * @version 2.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

 
defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

if (isset($id) && $id > 0) {
    Cot::$db->delete(Cot::$db->xtradbrowmarket, "itempagid = ?", [$id]);
}