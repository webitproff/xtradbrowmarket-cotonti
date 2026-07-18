<?php
/**
 * English Language File for xtradbrowmarket Plugin with i18n support
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.en.lang.php
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
$L['cfg_xtradbrowmarket_i18n_use'] = 'Enable and use field multilingualism';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Enables support for translations of extrafield values. When disabled, all translations are preserved but not displayed.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Default site language code';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Must match the global setting <code>$cfg[\'defaultlang\']</code>. Values for this language are stored in the main table and treated as the original.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Code of the first additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Use the first additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'If active, fields for entering translations in this language will appear in editing forms.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Code of the second additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Use the second additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'If active, fields for entering translations in this language will appear in editing forms.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Code of the third additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Use the third additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'If active, fields for entering translations in this language will appear in editing forms.';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'Plugin adds extrafields for the "Market PRO v.5" module into its own DB table with multilingual support.';

$L['info_notes'] = 
    'Beginners are advised to study ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Introduction. Description and principles of extrafields in Cotonti" class="initialism">' .
    '<strong>the ExtraFields API forum section</strong></abbr></a>. <br>' . 
    'After installing the plugin, open the plugin extrafields ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// TPL headers
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Dynamic output</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Custom output</span> in product card';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Administrator, for the product card, it is recommended to use individual output of additional fields for flexible customization.';

// ----------------------------------------------------------------
// Localization of titles (_TITLE) for demonstration fields
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Event Name';
$L['xtra_event_description_title'] = 'Event Description';
$L['xtra_event_start_title'] = 'Event Start';
$L['xtra_event_ticketprice_title'] = 'Ticket Price';
$L['xtra_event_seson_title'] = 'Season';
$L['xtra_demo_int_title'] = 'Integer Example';
$L['xtra_demo_double_title'] = 'Double Example';
$L['xtra_demo_select_title'] = 'Select Example';
$L['xtra_demo_radio_title'] = 'Radio Example';
$L['xtra_demo_datetime_title'] = 'Date/Time Example';
$L['xtra_demo_file_title'] = 'File Upload Example';
$L['xtra_demo_country_title'] = 'Country Example';
$L['xtra_demo_range_title'] = 'Range Example';
$L['xtra_demo_checklistbox_title'] = 'Checklist Box Example';

// ----------------------------------------------------------------
// Localization of values for select, radio, checklistbox
// ----------------------------------------------------------------
// Season
$L['event_seson_unknown'] = 'Unknown';
$L['event_seson_winter'] = 'Winter';
$L['event_seson_summer'] = 'Summer';
$L['event_seson_autumn'] = 'Autumn';
$L['event_seson_spring'] = 'Spring';

// Demo select
$L['demo_select_Option 1'] = 'Option 1';
$L['demo_select_Option 2'] = 'Option 2';
$L['demo_select_Option 3'] = 'Option 3';

// Demo radio
$L['demo_radio_Yes'] = 'Yes';
$L['demo_radio_No'] = 'No';

// Demo checklistbox (варианты теперь на латинице)
$L['demo_checklistbox_option1'] = 'Option 1';
$L['demo_checklistbox_option2'] = 'Option 2';
$L['demo_checklistbox_option3'] = 'Option 3';
