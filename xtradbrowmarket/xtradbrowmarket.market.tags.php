<?php
/* ====================
[BEGIN_COT_EXT] 
Hooks=market.tags
[END_COT_EXT]
==================== */

/**
 * вывод на странице просмотра: plugins/xtradbrowmarket/xtradbrowmarket.market.tags.php
 * Хук market.tags. Позволяет вывести все поля через блок <!-- BEGIN: XTRA_EXTRAFLD -->, 
 * а также назначает индивидуальные теги {XTRA_ИМЯПОЛЯ}
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
 * Страница просмотра (market.tpl)
 * 
 * Динамический вывод всех полей:
 * 
 * <!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
 *    <!-- BEGIN: XTRA_EXTRAFLD -->
 *       <!-- IF {XTRA_EXTRAFIELD_VALUE} --> 
 *          <div class="extrafield-{XTRA_EXTRAFIELD_NAME}">
 *             <strong>{XTRA_EXTRAFIELD_TITLE}:</strong>
 *             <span>{XTRA_EXTRAFIELD_VALUE}</span>
 *          </div>
 *       <!-- ENDIF -->
 *    <!-- END: XTRA_EXTRAFLD -->
 * <!-- ENDIF -->
 * 
 * Ручной (индивидуальный) вывод конкретного поля:
 * 
 * <!-- IF {MARKET_XTRA_EVENT_NAME} -->
 *     <p>{MARKET_XTRA_EVENT_NAME_TITLE}: {MARKET_XTRA_EVENT_NAME}</p>
 * <!-- ENDIF -->
 * 
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
            foreach ($extrafields as $exfld) {
                $tag = mb_strtoupper($exfld['field_name']);
                $value = $xtra_data[$exfld['field_name']] ?? null;

                $t->assign([
                    'MARKET_XTRA_' . $tag             => cot_build_extrafields_data('xtra', $exfld, $value, $item['fieldmrkt_parser']),
                    'MARKET_XTRA_' . $tag . '_TITLE'  => cot_extrafield_title($exfld, 'xtra_'),
                    'MARKET_XTRA_' . $tag . '_VALUE'  => $value,
                    'MARKET_XTRA_EXTRAFIELD_TITLE'    => cot_extrafield_title($exfld, 'xtra_'),
                    'MARKET_XTRA_EXTRAFIELD_VALUE'    => cot_build_extrafields_data('xtra', $exfld, $value, $item['fieldmrkt_parser']),
                    'MARKET_XTRA_EXTRAFIELD_NAME'     => $exfld['field_name'],
                ]);

                // Название страны, если поле — country
                if ($exfld['field_type'] === 'country') {
                    $t->assign('MARKET_XTRA_' . $tag . '_NAME', isset($cot_countries[$value]) ? $cot_countries[$value] : $value);
                }

                $t->parse('MAIN.XTRA_EXTRAFLD');
            }
        } else {
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

