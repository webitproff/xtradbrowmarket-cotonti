<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=markettags.main
[END_COT_EXT]
==================== */

/**
 * Overrides market tags in cot_generate_markettags() function
 * Теги для использования в cot_generate_markettags() (категории, товары, etc.): плагин xtradbrowmarket
 * Хук markettags.main. Добавляет в общий массив тегов переменные $temp_array + XTRA + ИМЯПОЛЯ и т.д.
 * например {LIST_ROW_XTRA_XXXXX} и {LIST_ROW_XTRA_XXXXX_TITLE}
 *
 * С версии 3.0.0 добавлена поддержка мультиязычности:
 *  - для типов без встроенной локализации (input, textarea, double, inputint и т.д.)
 *    значение подменяется переводом из xtradbrowmarket_i18n, если он существует для текущего языка.
 *  - для select, radio, checklistbox по‑прежнему используется языковой массив $L.
 *
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.markettags.php
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
 *
 * @see cot_generate_markettags()
 * Хук markettags.main вызывается внутри функции cot_generate_markettags(). 
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
        // Типы, для которых встроенная локализация уже работает через $L
        $builtInI18nTypes = ['select', 'radio', 'checklistbox', 'checkbox'];

        foreach ($extrafields as $exfld) {
            $tag = 'XTRA_' . strtoupper($exfld['field_name']);
            $value = $xtra_data[$exfld['field_name']] ?? null;

            // Подмена значения на перевод, если мультиязычность включена и тип поля не
            // поддерживает собственную языковую локализацию
            $displayValue = $value;
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])
                && !in_array($exfld['field_type'], $builtInI18nTypes)) {
                $displayValue = xtradbrowmarket_i18n_get_value($item_data['fieldmrkt_id'], $exfld['field_name'], $value);
            }

            $temp_array[$tag] = cot_build_extrafields_data('xtra', $exfld, $displayValue, $item_data['fieldmrkt_parser']);
            $temp_array[$tag . '_TITLE'] = cot_extrafield_title($exfld, 'xtra_');
            $temp_array[$tag . '_VALUE'] = $displayValue;

            // Название страны, если поле — country (используем оригинальный код страны)
            if ($exfld['field_type'] === 'country') {
                $country_lang = cot_langfile('countries', 'core');
                if (file_exists($country_lang)) {
                    include $country_lang;
                }
                // $value содержит код страны (ua, us), а не переведённое название
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
