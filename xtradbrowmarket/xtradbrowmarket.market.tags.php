<?php
/* ====================
[BEGIN_COT_EXT] 
Hooks=market.tags
[END_COT_EXT]
==================== */

/**
 * Вывод на странице просмотра товара: плагин xtradbrowmarket
 * Хук market.tags. Позволяет вывести все поля через блок <!-- BEGIN: XTRA_EXTRAFLD -->, 
 * а также назначает индивидуальные теги {MARKET_XTRA_ИМЯПОЛЯ}
 *
 * С версии 4.1.1 добавлена поддержка мультиязычности:
 *  - для типов, не имеющих встроенной локализации (input, textarea, double, inputint,
 *    datetime, range, file, country), значение автоматически подменяется переводом
 *    из таблицы xtradbrowmarket_i18n, если он существует для текущего языка.
 *  - для select, radio, checklistbox по‑прежнему используется языковой массив $L.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.market.tags.php
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

if (!empty($item['fieldmrkt_id'])) {
    // Загрузка стран 
    $country_lang = cot_langfile('countries', 'core');
    if (file_exists($country_lang)) {
        include $country_lang;
    }

    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        $xtra_data = xtradbrowmarket_load($item['fieldmrkt_id']);
        if ($xtra_data) {
            // Типы, для которых встроенная локализация уже работает через $L
            $builtInI18nTypes = ['select', 'radio', 'checklistbox', 'checkbox'];

            foreach ($extrafields as $exfld) {
                $tag = mb_strtoupper($exfld['field_name']);
                $value = $xtra_data[$exfld['field_name']] ?? null;

                // Если мультиязычность включена и тип поля не имеет собственного перевода,
                // пытаемся подставить перевод из xtradbrowmarket_i18n
                $displayValue = $value;
                if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])
                    && !in_array($exfld['field_type'], $builtInI18nTypes)) {
                    $displayValue = xtradbrowmarket_i18n_get_value($item['fieldmrkt_id'], $exfld['field_name'], $value);
                }

                $t->assign([
                    'MARKET_XTRA_' . $tag             => cot_build_extrafields_data('xtra', $exfld, $displayValue, $item['fieldmrkt_parser']),
                    'MARKET_XTRA_' . $tag . '_TITLE'  => cot_extrafield_title($exfld, 'xtra_'),
                    'MARKET_XTRA_' . $tag . '_VALUE'  => $displayValue,
                    'MARKET_XTRA_EXTRAFIELD_TITLE'    => cot_extrafield_title($exfld, 'xtra_'),
                    'MARKET_XTRA_EXTRAFIELD_VALUE'    => cot_build_extrafields_data('xtra', $exfld, $displayValue, $item['fieldmrkt_parser']),
                    'MARKET_XTRA_EXTRAFIELD_NAME'     => $exfld['field_name'],
                ]);

                // Название страны, если поле — country
                if ($exfld['field_type'] === 'country') {
                    $t->assign('MARKET_XTRA_' . $tag . '_NAME', isset($cot_countries[$displayValue]) ? $cot_countries[$displayValue] : $displayValue);
                }

                $t->parse('MAIN.XTRA_EXTRAFLD');
            }
        } else {
            // Нет записи в xtradbrowmarket – очищаем теги
            foreach ($extrafields as $exfld) {
                $tag = mb_strtoupper($exfld['field_name']);
                $t->assign([
                    'MARKET_XTRA_' . $tag => '',
                    'MARKET_XTRA_' . $tag . '_TITLE' => '',
                    'MARKET_XTRA_' . $tag . '_VALUE' => '',
                    'MARKET_XTRA_EXTRAFIELD_TITLE'   => '',
                    'MARKET_XTRA_EXTRAFIELD_VALUE'   => '',
                    'MARKET_XTRA_EXTRAFIELD_NAME'    => '',
                ]);
                if ($exfld['field_type'] === 'country') {
                    $t->assign('MARKET_XTRA_' . $tag . '_NAME', '');
                }
                $t->parse('MAIN.XTRA_EXTRAFLD');
            }
        }
    }
}
