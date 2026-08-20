<?php
/**
 * English Language File for xtradbrowmarket Plugin with i18n support
 *
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.en.lang.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 21Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

// use the global variable $db_x, which is defined in datas/config.php
// and is available at all times, even before loading any plugins
// $db_x is not a deprecated global variable, but a lesser-known,
// key variable for tasks such as correct linking.
// It is set in config datas/config.php and propagated through
// Cot::init() in system/common.php using class Cot from Cot.php.
// It works both before and after plugin installation.
// There are no other reliable ways to get the table prefix at the language file loading stage in Cotonti.
// Cot::$db_x and Cot::$db->tablePrefix are not part of the public API and do not guarantee availability at the required moment.
// The variable $db_x, defined in datas/config.php and accessible via global, is the only correct and documented way.
// Therefore the expression with $db_x is correct and the only correct one for this situation.

global $db_x;

$main_url = rtrim(Cot::$cfg['mainurl'], '/');
$url = $main_url . '/' . cot_url('admin', 'm=extrafields&n=' . $db_x . 'xtradbrowmarket', '', true);

$L['xtradbrowmarket'] = 'Extrafields Market Custom i18n';

// Custom localization file for Cotonti using via function cot_langfile_custom() in system/functions.custom.php
// include File from Path: plugins/xtradbrowmarket/lang/xtradbrowmarket.custom.ru.lang.php
// How is works:  https://github.com/webitproff/functions.custom.php-cotonti
// How is works:  https://abuyfile.com/ru/cotonti/reading/rukovodstvo-po-polzovatelskim-funkciyam-cotonti
if (function_exists('cot_langfile_custom')) {
    cot_langfile_custom('xtradbrowmarket', 'plug');
}

// ========================
// PLUGIN SETTINGS (ADMIN)
// ========================
$L['cfg_perpage']          = 'Items per page in list/table';
$L['cfg_perpage_hint']     = 'Number of items on a page in the mass editing list';

$L['cfg_xtradbrowmarket_i18n_use'] = 'Activate and use multilingual fields';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Enables support for translations of extrafield values. When disabled, all translations are kept but not displayed.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Default site language code';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Must match the global setting <code>$cfg[\'defaultlang\']</code>. Values for this language are stored in the main table and considered original.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Code of the first additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Use the first additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'If active, fields for entering translation into this language will appear in editing forms.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Code of the second additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Use the second additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'If active, fields for entering translation into this language will appear in editing forms.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Code of the third additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Use the third additional language';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'If active, fields for entering translation into this language will appear in editing forms.';
$L['cfg_xtradbrowmarket_showallitems'] = 'Show all items in admin panel';
$L['cfg_xtradbrowmarket_showallitems_hint'] = 'When enabled, all items will be displayed in the editing tables, even those for which extrafield records have not been created yet.';

$L['cfg_help_info'] = 'Developer Help';
$L['xtradbrowmarket_setup_help_text'] = 'Detailed guide, links to related materials, or ask for help: <a href="https://github.com/webitproff/xtradbrowmarket-cotonti" target="_blank" title="Opens in a new tab"><strong><u>plugin repository page</u></strong></a> on GitHub.com';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'The plugin adds extrafields for the "Market PRO v.5" module into its own database table with multilingual support.';

$L['info_notes'] = 
    'Newcomers ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Introduction. Description and principles of extrafields in Cotonti" class="initialism">' .
    '<strong>must read the forum section about API ExtraFields</strong></abbr></a>. <br>' . 
    'After installing the plugin, open the plugin extrafields ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// ========================
// TITLES AND DESCRIPTIONS (same values, pulled by other keys)
// ========================
$L['xtradbrowmarket_title'] = $L['info_name'];
$L['xtradbrowmarket_desc']  = $L['info_desc'];
$L['xtradbrowmarket_name']  = $L['info_name'];

// ----------------------------------------------------------------
// Admin panel
// ----------------------------------------------------------------
$L['xtradbrowmarket_tab_stats'] = 'Statistics';
$L['xtradbrowmarket_tab_edit'] = 'Edit';
$L['xtradbrowmarket_tab_i18n'] = 'Edit + Translations';
$L['xtradbrowmarket_stats_total_items'] = 'Total items';
$L['xtradbrowmarket_stats_xtra_rows'] = 'Records in xtradbrowmarket';
$L['xtradbrowmarket_stats_filled'] = 'Filled records';
$L['xtradbrowmarket_extrafields_info'] = 'Extrafield parameters';
$L['xtradbrowmarket_field_name'] = 'Field name';
$L['xtradbrowmarket_field_type'] = 'Type';
$L['xtradbrowmarket_field_description'] = 'Description';
$L['xtradbrowmarket_field_variants'] = 'Variants';
$L['xtradbrowmarket_field_params'] = 'Parameters';
$L['xtradbrowmarket_field_default'] = 'Default';
$L['xtradbrowmarket_field_required'] = 'Required';
$L['xtradbrowmarket_field_enabled'] = 'Enabled';
$L['xtradbrowmarket_market_title'] = 'Item title';
$L['xtradbrowmarket_no_extrafields'] = 'No registered extrafields';
$L['xtradbrowmarket_no_records'] = 'No records';
$L['xtradbrowmarket_saved'] = 'Changes saved';
$L['xtradbrowmarket_i18n_active'] = 'Multilingual support is enabled';
$L['xtradbrowmarket_i18n_disabled'] = 'Multilingual support is disabled';
$L['xtradbrowmarket_search_sq'] = 'Search by title/text';
$L['xtradbrowmarket_search_cat'] = 'Category';
$L['xtradbrowmarket_filter_id'] = 'Item ID';
$L['xtradbrowmarket_filter_state'] = 'Status';
$L['xtradbrowmarket_search_btn'] = 'Filter';
$L['xtradbrowmarket_search_reset'] = 'Reset';
$L['xtradbrowmarket_search_in_title'] = 'Title';
$L['xtradbrowmarket_search_in_full'] = 'Everywhere (title+text)';
$L['xtradbrowmarket_search_in_pcod'] = 'Code (SKU)';
$L['xtradbrowmarket_search_result_msg'] = 'Found %s for query %s';
$L['xtradbrowmarket_search_result_none'] = 'Nothing found for query %s';
$L['xtradbrowmarket_search_declen'] = 'records,record,records';
$L['xtradbrowmarket_updated'] = 'Updated records: %d';

// ----------------------------------------------------------------
// TPL headers in some output places
// ----------------------------------------------------------------
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Dynamic output</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Individual output</span> in product card';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Administrator, for the product card it is recommended to use exactly the individual output of extra fields for flexible customization';

// ----------------------------------------------------------------
// Localization of titles (_TITLE) for demo fields
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Event name';
$L['xtra_event_description_title'] = 'Event description';
$L['xtra_event_start_title'] = 'Event start';
$L['xtra_event_ticketprice_title'] = 'Ticket price';
$L['xtra_event_seson_title'] = 'Season';
$L['xtra_demo_int_title'] = 'Sample integer';
$L['xtra_demo_double_title'] = 'Sample floating-point number';
$L['xtra_demo_select_title'] = 'Sample dropdown';
$L['xtra_demo_radio_title'] = 'Sample radio buttons';
$L['xtra_demo_datetime_title'] = 'Sample date and time';
$L['xtra_demo_file_title'] = 'Sample file upload';
$L['xtra_demo_country_title'] = 'Sample country selection';
$L['xtra_demo_range_title'] = 'Sample number range';
$L['xtra_demo_checklistbox_title'] = 'Sample checklistbox';

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

// Demo checklistbox (values already in English, but kept for consistency)
$L['demo_checklistbox_option1'] = 'Option 1';
$L['demo_checklistbox_option2'] = 'Option 2';
$L['demo_checklistbox_option3'] = 'Option 3';
