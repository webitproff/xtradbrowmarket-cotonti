<?php
/**
 * Polish Language File for xtradbrowmarket Plugin with i18n support
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.pl.lang.php
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
$L['cfg_xtradbrowmarket_i18n_use'] = 'Włącz i używaj wielojęzyczności pól';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Włącza obsługę tłumaczeń wartości pól dodatkowych. Po wyłączeniu wszystkie tłumaczenia są zachowywane, ale nie wyświetlane.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Domyślny kod języka strony';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Musi być zgodny z ustawieniem globalnym <code>$cfg[\'defaultlang\']</code>. Wartości dla tego języka są przechowywane w tabeli głównej i traktowane jako oryginalne.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Kod pierwszego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Używaj pierwszego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'Jeśli aktywne, w formularzach edycji pojawią się pola do wprowadzania tłumaczeń w tym języku.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Kod drugiego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Używaj drugiego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'Jeśli aktywne, w formularzach edycji pojawią się pola do wprowadzania tłumaczeń w tym języku.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Kod trzeciego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Używaj trzeciego dodatkowego języka';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'Jeśli aktywne, w formularzach edycji pojawią się pola do wprowadzania tłumaczeń w tym języku.';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'Wtyczka dodaje dodatkowe pola dla modułu "Market PRO v.5" do własnej tabeli bazy danych z obsługą wielojęzyczności.';

$L['info_notes'] = 
    'Początkującym zaleca się zapoznanie się z ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Wprowadzenie. Opis i zasady działania pól dodatkowych w Cotonti" class="initialism">' .
    '<strong>działem forum na temat API ExtraFields</strong></abbr></a>. <br>' . 
    'Po zainstalowaniu wtyczki otwórz dodatkowe pola wtyczki ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// TPL headers
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Pola dodatkowe <code>xtradbrowmarket</code>. Wyjście dynamiczne</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Pola dodatkowe <code>xtradbrowmarket</code>. Wyjście niestandardowe</span> w karcie produktu';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Administratorze, w karcie produktu zaleca się korzystanie z indywidualnego wyświetlania pól dodatkowych w celu elastycznej personalizacji.';

// ----------------------------------------------------------------
// Localization of titles (_TITLE) for demonstration fields
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Nazwa wydarzenia';
$L['xtra_event_description_title'] = 'Opis wydarzenia';
$L['xtra_event_start_title'] = 'Początek wydarzenia';
$L['xtra_event_ticketprice_title'] = 'Cena biletu';
$L['xtra_event_seson_title'] = 'Sezon';
$L['xtra_demo_int_title'] = 'Przykład liczby całkowitej';
$L['xtra_demo_double_title'] = 'Przykład liczby zmiennoprzecinkowej';
$L['xtra_demo_select_title'] = 'Przykład listy rozwijanej';
$L['xtra_demo_radio_title'] = 'Przykład przycisków radiowych';
$L['xtra_demo_datetime_title'] = 'Przykład daty/czasu';
$L['xtra_demo_file_title'] = 'Przykład przesyłania pliku';
$L['xtra_demo_country_title'] = 'Przykład wyboru kraju';
$L['xtra_demo_range_title'] = 'Przykład zakresu';
$L['xtra_demo_checklistbox_title'] = 'Przykład pól wyboru';

// ----------------------------------------------------------------
// Localization of values for select, radio, checklistbox
// ----------------------------------------------------------------
// Season
$L['event_seson_unknown'] = 'Nieznany';
$L['event_seson_winter'] = 'Zima';
$L['event_seson_summer'] = 'Lato';
$L['event_seson_autumn'] = 'Jesień';
$L['event_seson_spring'] = 'Wiosna';

// Demo select
$L['demo_select_Option 1'] = 'Opcja 1';
$L['demo_select_Option 2'] = 'Opcja 2';
$L['demo_select_Option 3'] = 'Opcja 3';

// Demo radio
$L['demo_radio_Yes'] = 'Tak';
$L['demo_radio_No'] = 'Nie';

// Demo checklistbox (warianty po łacinie)
$L['demo_checklistbox_option1'] = 'Opcja 1';
$L['demo_checklistbox_option2'] = 'Opcja 2';
$L['demo_checklistbox_option3'] = 'Opcja 3';
