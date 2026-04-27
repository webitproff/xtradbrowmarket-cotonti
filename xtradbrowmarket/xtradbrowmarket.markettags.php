<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=markettags.main
[END_COT_EXT]
==================== */

/**
 * Overrides market tags in cot_generate_markettags() function
 * Теги для использования в cot_generate_markettags() (категории, товары, etc.): plugins/xtradbrowmarket/xtradbrowmarket.markettags.php
 * Хук markettags.main. Добавляет в общий массив тегов переменные $temp_array + XTRA + ИМЯПОЛЯ  и т.д.
 * например {LIST_ROW_XTRA_XXXXX} и {LIST_ROW_XTRA_XXXXX_TITLE}
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
 * @see cot_generate_markettags()
 * Хук pagetags.main вызывается внутри функции cot_generate_markettags(). 
 * В этой функции локально определена переменная $temp_array, и когда через include подключается ваш файл, 
 * он выполняется в том же пространстве имён функции – поэтому $temp_array доступна напрямую, без объявления global.
 * @var array<string, mixed> $item_data
 */
 
/* 
 * Список товаров (market.list.tpl) - только индивидуальный вывод каждого экстраполя!!!
 * 
 * Теги будут иметь префикс, заданный в cot_generate_markettags() и зависит от конкретного шаблона:
 * 
 *         <!-- IF {LIST_ROW_XTRA_DEMO_COUNTRY} -->
 *        <div class="mb-3">
 *             <img src="images/flags/{LIST_ROW_XTRA_DEMO_COUNTRY_VALUE}.svg"
 *                  style="width:24px;height:auto;" class="me-2" alt="">
 *             <strong>{LIST_ROW_XTRA_DEMO_COUNTRY_TITLE}:</strong>
 *             <span>{LIST_ROW_XTRA_DEMO_COUNTRY}</span>    <span>{LIST_ROW_XTRA_DEMO_COUNTRY_NAME}</span>
 *         </div>
 *         <!-- ENDIF -->	
 * 
 * <!-- IF {LIST_ROW_XTRA_EVENT_NAME} -->
 *     <small>{LIST_ROW_XTRA_EVENT_NAME_TITLE}: {LIST_ROW_XTRA_EVENT_NAME}</small>
 * <!-- ENDIF -->
*/


defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('xtradbrowmarket', 'plug');

$extrafields = xtradbrowmarket_getExtrafields();
if (!empty($extrafields) && !empty($item_data['fieldmrkt_id'])) {
    $xtra_data = xtradbrowmarket_load($item_data['fieldmrkt_id']);
    if ($xtra_data) {
        foreach ($extrafields as $exfld) {
            $tag = 'XTRA_' . strtoupper($exfld['field_name']);
            $value = $xtra_data[$exfld['field_name']] ?? null;
            $temp_array[$tag] = cot_build_extrafields_data('xtra', $exfld, $value, $item_data['fieldmrkt_parser']);
            $temp_array[$tag . '_TITLE'] = cot_extrafield_title($exfld, 'xtra_');
            $temp_array[$tag . '_VALUE'] = $value;

            if ($exfld['field_type'] === 'country') {
                $country_lang = cot_langfile('countries', 'core');
                if (file_exists($country_lang)) {
                    include $country_lang;   // именно include, не include_once
                }
                $temp_array[$tag . '_NAME'] = isset($cot_countries[$value]) ? $cot_countries[$value] : $value;
            }
        }
    } else {
        foreach ($extrafields as $exfld) {
            $tag = 'XTRA_' . strtoupper($exfld['field_name']);
            $temp_array[$tag] = '';
            $temp_array[$tag . '_TITLE'] = '';
            $temp_array[$tag . '_VALUE'] = '';
            if ($exfld['field_type'] === 'country') {
                $temp_array[$tag . '_NAME'] = '';
            }
        }
    }
}