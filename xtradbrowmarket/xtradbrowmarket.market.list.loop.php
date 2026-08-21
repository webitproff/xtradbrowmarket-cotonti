<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=market.list.loop
[END_COT_EXT]
==================== */

/**
 * Filename: xtradbrowmarket.market.list.loop.php – Вывод полей в списке пользователей (market.list.tpl)
 *
 * Purpose:   Подключается к хуку `market.list.loop`, который вызывается в файле
 *            /modules/market/inc/market.list.php на каждой итерации цикла вывода списка пользователей.
 *            Добавляет в шаблон market.list.tpl теги для каждого экстраполя:
 *            {LIST_ROW_XTRA_ИМЯПОЛЯ}, {LIST_ROW_XTRA_ИМЯПОЛЯ_TITLE},
 *            {LIST_ROW_XTRA_ИМЯПОЛЯ_VALUE}, а также универсальные теги
 *            {LIST_ROW_XTRA_EXTRAFLD} и {LIST_ROW_XTRA_EXTRAFLD_TITLE},
 *            которые позволяют выводить все поля через блок <!-- BEGIN: XTRA_EXTRAFLD -->.
 *            Для полей типа country дополнительно назначается тег _NAME
 *            с локализованным названием страны из файла стран.
 *
 * Мультиязычность в списке товаров:
 *   - select, radio, checklistbox:
 *       локализуются через языковой массив $L (ключи вида $L[{field_name}_{value}]).
 *       Таблица xtradbrowmarket_i18n не используется.
 *   - checkbox:
 *       хранит 1 или 0, локализация отображения («Да»/«Нет») выполняется в шаблоне.
 *   - input, textarea:
 *       при включённой мультиязычности значение может быть заменено переводом
 *       из таблицы xtradbrowmarket_i18n, если перевод для текущего языка был сохранён.
 *   - inputint, double, datetime, range, file, country:
 *       код пытается подменить через xtradbrowmarket_i18n_get_value(),
 *       но переводы для этих типов в админке не сохраняются, поэтому обычно
 *       выводится оригинальное значение.
 *   - country дополнительно получает локализованное название через массив
 *       $cot_countries (файл стран), независимо от таблицы i18n.
 *
 * Path:     plugins/xtradbrowmarket/xtradbrowmarket.market.list.loop.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * Source and updates   https://github.com/webitproff/xtradbrowmarket-cotonti
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 21Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.2
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('xtradbrowmarket', 'plug');

/* 
 * Список товаров (market.list.tpl) - есть групповой, но рекомендуется только индивидуальный вывод каждого экстраполя!!!
*/

$extrafields = xtradbrowmarket_getExtrafields();
if (!empty($extrafields) && !empty($item['fieldmrkt_id'])) {
    $xtra_data = xtradbrowmarket_load($item['fieldmrkt_id']);
    if ($xtra_data) {
        // Типы, для которых встроенная локализация уже работает через $L
        $builtInI18nTypes = ['select', 'radio', 'checklistbox', 'checkbox'];

        foreach ($extrafields as $exfld) {
            $tag = strtoupper($exfld['field_name']);
            $value = $xtra_data[$exfld['field_name']] ?? null;

            // Подмена значения на перевод, если мультиязычность включена и тип поля не
            // поддерживает собственную языковую локализацию
            $displayValue = $value;
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])
                && !in_array($exfld['field_type'], $builtInI18nTypes)) {
                $displayValue = xtradbrowmarket_i18n_get_value($item['fieldmrkt_id'], $exfld['field_name'], $value);
            }

            // Индивидуальные теги для каждого поля
            $t->assign([
                'LIST_ROW_XTRA_' . $tag             => cot_build_extrafields_data('xtra', $exfld, $displayValue),
                'LIST_ROW_XTRA_' . $tag . '_TITLE'  => cot_extrafield_title($exfld, 'xtra_'),
                'LIST_ROW_XTRA_' . $tag . '_VALUE'  => $displayValue,
            ]);

            // === Универсальные теги для группового цикла ===
            // Чтобы работал блок <!-- BEGIN: XTRA_EXTRAFLD --> в users.tpl,
            // присваиваем значения тегам и вызываем parse() на каждой итерации.
            $t->assign([
                'LIST_ROW_XTRA_EXTRAFLD'       => cot_build_extrafields_data('xtra', $exfld, $displayValue),
                'LIST_ROW_XTRA_EXTRAFLD_TITLE' => cot_extrafield_title($exfld, 'xtra_'),
            ]);
            $t->parse('MAIN.LIST_ROW.XTRA_EXTRAFLD');
            // === Конец группового цикла ===

            // Название страны, если поле — country (используем оригинальный код страны)
            if ($exfld['field_type'] === 'country') {
                $country_lang = cot_langfile('countries', 'core');
                if (file_exists($country_lang)) {
                    include $country_lang;
                }
                // $value содержит код страны (null, cn, us), а не переведённое название
				$countryCode = $value ?? '';
				$t->assign('LIST_ROW_XTRA_' . $tag . '_NAME', isset($cot_countries[$countryCode]) ? $cot_countries[$countryCode] : $countryCode);
            }
        }
    } else {
        // Если данных в xtradbrowmarket нет, очищаем все теги
        foreach ($extrafields as $exfld) {
            $tag = strtoupper($exfld['field_name']);
            $t->assign([
                'LIST_ROW_XTRA_' . $tag             => '',
                'LIST_ROW_XTRA_' . $tag . '_TITLE'  => '',
                'LIST_ROW_XTRA_' . $tag . '_VALUE'  => '',
            ]);
            if ($exfld['field_type'] === 'country') {
                $t->assign('LIST_ROW_XTRA_' . $tag . '_NAME', '');
            }
        }
        // Групповой блок XTRA_EXTRAFLD в этом случае не получит итераций
        // и не выведется — это корректно, пользователь не увидит пустых строк.
    }
}
