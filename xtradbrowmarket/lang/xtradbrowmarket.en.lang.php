<?php
/**
 * English Language File for xtradbrowmarket Plugin
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

global $db_x;

$main_url = rtrim(Cot::$cfg['mainurl'], '/');
$url = $main_url . '/' . cot_url('admin', 'm=extrafields&n=' . $db_x . 'xtradbrowmarket', '', true);

$L['xtradbrowmarket'] = 'Custom Extrafields Market';

/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom';

$L['info_desc'] = 'The plugin adds extrafields for the "Market PRO v.5" module, but into its own database table';

$L['info_notes'] = 
    'For beginners, ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Introduction. Description and principles of extrafields in Cotonti" class="initialism">' .
    '<strong>be sure to read the forum section about API ExtraFields</strong></abbr></a>. <br>' . 
    'After installing the plugin, open the plugin extrafields ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Dynamic output</span>';
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Extrafields <code>xtradbrowmarket</code>. Individual output</span> in product card';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'For the product card, it is recommended to use individual output of extra fields for flexible customization.';