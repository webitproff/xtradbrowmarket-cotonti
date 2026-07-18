<?php
/**
 * Russian Language File for xtradbrowmarket Plugin with i18n support
 *
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.ru.lang.php
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
 */

defined('COT_CODE') or die('Wrong URL.');

// использовать глобальную переменную $db_x, которая определена в datas/config.php 
// и доступна абсолютно всегда, ещё до загрузки любых плагинов 
// $db_x — это не устаревшая глобальная переменная, а малоизвестная, 
// ключевая переменная для например таких задач для корректно ссылки.
// задаётся в конфиге datas/config.php и пробрасывается через 
// Cot::init() в system/common.php используя class Cot из Cot.php . 
// Она работает и до установки плагина, и после.
// В Cotonti нет других надёжных способов получить префикс таблиц на этапе загрузки языкового файла. 
// Cot::$db_x и Cot::$db->tablePrefix не являются частью публичного API и не гарантируют доступность в нужный момент. 
// Переменная $db_x, определённая в datas/config.php и доступная через global, — это единственный корректный и документированный способ. 
// Поэтому выражение с $db_x является правильным и единственно верным для данной ситуации.

global $db_x;

$main_url = rtrim(Cot::$cfg['mainurl'], '/');
$url = $main_url . '/' . cot_url('admin', 'm=extrafields&n=' . $db_x . 'xtradbrowmarket', '', true);

$L['xtradbrowmarket'] = 'Custom Extrafields Market';

/**
 * Plugin Config (i18n)
 */
$L['cfg_xtradbrowmarket_i18n_use'] = 'Мультиязычность полей активировать и использовать';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Включает поддержку переводов значений экстраполей. При отключении все переводы сохраняются, но не отображаются.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Код основного языка сайта';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Должен совпадать с глобальной настройкой <code>$cfg[\'defaultlang\']</code>. Значения для этого языка хранятся в основной таблице и считаются оригиналом.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Код первого дополнительного языка';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Использовать первый дополнительный язык';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'Если активно, в формах редактирования появятся поля для ввода перевода на этот язык.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Код второго дополнительного языка';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Использовать второй дополнительный язык';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'Если активно, в формах редактирования появятся поля для ввода перевода на этот язык.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Код третьего дополнительного языка';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Использовать третий дополнительный язык';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'Если активно, в формах редактирования появятся поля для ввода перевода на этот язык.';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'Плагин добавляет экстраполя для модуля "Market PRO v.5" в свою таблицу БД с поддержкой мультиязычности.';

$L['info_notes'] = 
    'Новичкам ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Введение. Описание и принципы работы экстраполей в Cotonti" class="initialism">' .
    '<strong>обязательно читать раздел форума об API ExtraFields</strong></abbr></a>. <br>' . 
    'После установки плагина, открыть экстраполя плагина ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// TPL-заголовки
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Экстраполя <code>xtradbrowmarket</code>. Динамический вывод</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Экстраполя <code>xtradbrowmarket</code>. Индивидуальный вывод</span> в карточке товара';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Администратор, для карточки товара, рекомендуется использовать именно индивидуальный вывод дополнительных полей для их гибкой кастомизации';

// ----------------------------------------------------------------
// Локализация заголовков (_TITLE) для демонстрационных полей
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Название события';
$L['xtra_event_description_title'] = 'Описание события';
$L['xtra_event_start_title'] = 'Начало события';
$L['xtra_event_ticketprice_title'] = 'Стоимость билета';
$L['xtra_event_seson_title'] = 'Сезон';
$L['xtra_demo_int_title'] = 'Пример целого числа';
$L['xtra_demo_double_title'] = 'Пример числа с плавающей точкой';
$L['xtra_demo_select_title'] = 'Пример выпадающего списка';
$L['xtra_demo_radio_title'] = 'Пример радиокнопок';
$L['xtra_demo_datetime_title'] = 'Пример даты и времени';
$L['xtra_demo_file_title'] = 'Пример загрузки файла';
$L['xtra_demo_country_title'] = 'Пример выбора страны';
$L['xtra_demo_range_title'] = 'Пример диапазона чисел';
$L['xtra_demo_checklistbox_title'] = 'Пример чекбоксов с множественным выбором';

// ----------------------------------------------------------------
// Локализация значений для select, radio, checklistbox
// ----------------------------------------------------------------
// Сезон
$L['event_seson_unknown'] = 'Неизвестно';
$L['event_seson_winter'] = 'Зима';
$L['event_seson_summer'] = 'Лето';
$L['event_seson_autumn'] = 'Осень';
$L['event_seson_spring'] = 'Весна';

// Демонстрационный select
$L['demo_select_Option 1'] = 'Вариант 1';
$L['demo_select_Option 2'] = 'Вариант 2';
$L['demo_select_Option 3'] = 'Вариант 3';

// Демонстрационные radio
$L['demo_radio_Yes'] = 'Да';
$L['demo_radio_No'] = 'Нет';

// Демонстрационный checklistbox (русские значения уже, но оставляем для единообразия)
$L['demo_checklistbox_option1'] = 'Опция 1';
$L['demo_checklistbox_option2'] = 'Опция 2';
$L['demo_checklistbox_option3'] = 'Опция 3';
