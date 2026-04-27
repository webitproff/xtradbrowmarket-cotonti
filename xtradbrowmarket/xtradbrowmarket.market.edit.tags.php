<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.edit.tags
  [END_COT_EXT]
==================== */

/**
 * Вывод полей в форме редактирования: plugins/xtradbrowmarket/xtradbrowmarket.market.edit.tags.php
 * Хук market.edit.tags. Отображает все extrafields с их текущими значениями (если есть) в форме редактирования страницы.
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

/* 
 * Форма редактирования страницы (market.edit.tpl)
 * 
 * <!-- BEGIN: XTRA_EXTRAFLD -->
 * <div class="form-group">
 *     <label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
 *     {MARKETEDIT_FORM_XTRA_EXTRAFLD}
 * </div>
 * <!-- END: XTRA_EXTRAFLD -->
 * 
 */
 
defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

$extrafields = xtradbrowmarket_getExtrafields();

if (!empty($extrafields) && isset($row_item['fieldmrkt_id'])) {
    $xtra_data = xtradbrowmarket_load($row_item['fieldmrkt_id']);
    foreach ($extrafields as $exfld) {
        $fieldName = 'rxtra_' . $exfld['field_name'];
        $value = $xtra_data[$exfld['field_name']] ?? null;
        $element = cot_build_extrafields($fieldName, $exfld, $value);
        $title = cot_extrafield_title($exfld, 'xtra_');

        $t->assign([
            'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name'])         => $element,
            'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name']) . '_TITLE' => $title,
            'MARKETEDIT_FORM_XTRA_EXTRAFLD'                                    => $element,
            'MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE'                              => $title,
        ]);
        $t->parse('MAIN.XTRA_EXTRAFLD');
    }
} 
