<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
[END_COT_EXT]
==================== */

/**
 * Вывод в «шапке» сайта: plugins/xtradbrowmarket/xtradbrowmarket.header.tags.php
 * Хук header.tags. Позволяет использовать теги {MARKET_HEADER_XTRA_ИМЯПОЛЯ} для SEO-тегов и других элементов <head>.
 * Пример вывода:
 * <!-- IF {MARKET_HEADER_XTRA_EVENT_NAME} -->
 * <meta name="event" content="{MARKET_HEADER_XTRA_EVENT_NAME}" />
 * <!-- ENDIF -->
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

if ($env['ext'] == 'market' && isset($id) && $id > 0) {
    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        // Загрузка стран (как в market.tags)
        $country_lang = cot_langfile('countries', 'core');
        if (file_exists($country_lang)) {
            include $country_lang;
        }

        $xtra_data = xtradbrowmarket_load($id);
        $parser = Cot::$cfg['market']['marketparser'];
        if ($xtra_data) {
            $page_info = Cot::$db->query(
                "SELECT fieldmrkt_parser FROM " . Cot::$db->market . " WHERE fieldmrkt_id = ?",
                [$id]
            )->fetch();
            if ($page_info) {
                $parser = $page_info['fieldmrkt_parser'] ?? $parser;
            }
        }

        foreach ($extrafields as $exfld) {
            $tag = 'MARKET_HEADER_XTRA_' . strtoupper($exfld['field_name']);
            $value = $xtra_data[$exfld['field_name']] ?? '';

            $t->assign([
                $tag => htmlspecialchars(
                    cot_build_extrafields_data('xtra', $exfld, $value, $parser),
                    ENT_QUOTES,
                    'UTF-8'
                ),
                $tag . '_TITLE' => cot_extrafield_title($exfld, 'xtra_'),
                $tag . '_VALUE' => $value,
            ]);

            // Название страны
            if ($exfld['field_type'] === 'country') {
                $t->assign(
                    $tag . '_NAME',
                    isset($cot_countries[$value]) ? $cot_countries[$value] : $value
                );
            }
        }
    }
}
