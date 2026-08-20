<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.delete.first
  [END_COT_EXT]
==================== */

/**
 * Удаление связанных файлов перед удалением товара: плагин xtradbrowmarket
 * Хук market.delete.first. Вызывается до физического удаления товара,
 * позволяет удалить загруженные через экстраполя файлы.
 * После этого каскадный внешний ключ автоматически удалит записи из
 * cot_xtradbrowmarket и cot_xtradbrowmarket_i18n.
 *
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.market.delete.first.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 20Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

if (isset($id) && $id > 0) {
    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        // Загружаем данные записи, чтобы удалить файлы
        $xtra_data = xtradbrowmarket_load($id);
        if ($xtra_data) {
            foreach ($extrafields as $exfld) {
                $fieldValue = $xtra_data[$exfld['field_name']] ?? null;
                cot_extrafield_unlinkfiles($fieldValue, $exfld);
            }
        }
    }
	// Явно удаляем запись из xtradbrowmarket (ЕСЛИ ЧТО-ТО НЕ ПРОШЛО)
    Cot::$db->delete(Cot::$db->xtradbrowmarket, 'itempagid = :id', ['id' => $id]);
	
    // Сама запись в xtradbrowmarket (и её переводы) будет удалена каскадом
    // при удалении товара из cot_market – внешний ключ ON DELETE CASCADE.
}
