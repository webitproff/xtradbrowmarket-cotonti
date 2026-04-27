<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.edit.update.done
  [END_COT_EXT]
==================== */

/**
 * Сохранение данных после обновления страницы: plugins/xtradbrowmarket/xtradbrowmarket.market.edit.update.done.php
 * Хук market.edit.update.done. Вызывается после успешного обновления страницы. Сохраняет значения extrafields в cot_xtradbrowmarket.
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
    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        $xtra_data = xtradbrowmarket_load($id) ?: [];
        $data = [];
        foreach ($extrafields as $exfld) {
            $fieldName = $exfld['field_name'];
            $inputName = 'rxtra_' . $fieldName;
            $oldValue = $xtra_data[$fieldName] ?? '';
            $data[$fieldName] = cot_import_extrafields($inputName, $exfld, 'P', $oldValue, 'xtra_');
        }
        xtradbrowmarket_save($id, $data);
        // === ВАЖНО: перемещаем загруженные файлы в целевую папку ===
        cot_extrafield_movefiles();
    }
}
