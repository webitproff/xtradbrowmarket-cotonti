<?php
/**
 * Ukrainian Language File for xtradbrowmarket Plugin with i18n support
 *
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.ua.lang.php
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

global $db_x;

$main_url = rtrim(Cot::$cfg['mainurl'], '/');
$url = $main_url . '/' . cot_url('admin', 'm=extrafields&n=' . $db_x . 'xtradbrowmarket', '', true);

$L['xtradbrowmarket'] = 'Custom Extrafields Market';

/**
 * Plugin Config (i18n)
 */
$L['cfg_xtradbrowmarket_i18n_use'] = 'Увімкнути та використовувати багатомовність полів';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Вмикає підтримку перекладів значень екстраполів. Коли вимкнено, всі переклади зберігаються, але не відображаються.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Код основної мови сайту';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Має збігатися з глобальним налаштуванням <code>$cfg[\'defaultlang\']</code>. Значення для цієї мови зберігаються в основній таблиці та вважаються оригіналом.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Код першої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Використовувати першу додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу цією мовою.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Код другої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Використовувати другу додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу цією мовою.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Код третьої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Використовувати третю додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу цією мовою.';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'Плагін додає екстраполя для модуля "Market PRO v.5" у власну таблицю БД з підтримкою багатомовності.';

$L['info_notes'] = 
    'Початківцям рекомендовано вивчити ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Вступ. Опис та принципи роботи екстраполів у Cotonti" class="initialism">' .
    '<strong>розділ форуму про API ExtraFields</strong></abbr></a>. <br>' . 
    'Після встановлення плагіна відкрийте екстраполя плагіна ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// TPL-заголовки
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Динамічний вивід</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Індивідуальний вивід</span> в картці товару';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Адміністраторе, для картки товару рекомендується використовувати саме індивідуальний вивід додаткових полів для гнучкого налаштування.';

// ----------------------------------------------------------------
// Локалізація заголовків (_TITLE) для демонстраційних полів
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Назва події';
$L['xtra_event_description_title'] = 'Опис події';
$L['xtra_event_start_title'] = 'Початок події';
$L['xtra_event_ticketprice_title'] = 'Вартість квитка';
$L['xtra_event_seson_title'] = 'Сезон';
$L['xtra_demo_int_title'] = 'Приклад цілого числа';
$L['xtra_demo_double_title'] = 'Приклад числа з рухомою крапкою';
$L['xtra_demo_select_title'] = 'Приклад спадного списку';
$L['xtra_demo_radio_title'] = 'Приклад радіокнопок';
$L['xtra_demo_datetime_title'] = 'Приклад дати та часу';
$L['xtra_demo_file_title'] = 'Приклад завантаження файлу';
$L['xtra_demo_country_title'] = 'Приклад вибору країни';
$L['xtra_demo_range_title'] = 'Приклад діапазону чисел';
$L['xtra_demo_checklistbox_title'] = 'Приклад чекбоксів із множинним вибором';

// ----------------------------------------------------------------
// Локалізація значень для select, radio, checklistbox
// ----------------------------------------------------------------
// Сезон
$L['event_seson_unknown'] = 'Невідомо';
$L['event_seson_winter'] = 'Зима';
$L['event_seson_summer'] = 'Літо';
$L['event_seson_autumn'] = 'Осінь';
$L['event_seson_spring'] = 'Весна';

// Демонстраційний select
$L['demo_select_Option 1'] = 'Варіант 1';
$L['demo_select_Option 2'] = 'Варіант 2';
$L['demo_select_Option 3'] = 'Варіант 3';

// Демонстраційні radio
$L['demo_radio_Yes'] = 'Так';
$L['demo_radio_No'] = 'Ні';

// Демонстраційний checklistbox
$L['demo_checklistbox_option1'] = 'Опція 1';
$L['demo_checklistbox_option2'] = 'Опція 2';
$L['demo_checklistbox_option3'] = 'Опція 3';
