<?php
/**
 * Ukrainian Language File for xtradbrowmarket Plugin
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

$L['info_desc'] = 'Плагін додає екстраполя для модуля "Market PRO v.5", але у власну таблицю БД';

$L['info_notes'] = 
    'Новачкам ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Вступ. Опис та принципи роботи екстраполів у Cotonti" class="initialism">' .
    '<strong>обов\'язково прочитати розділ форуму про API ExtraFields</strong></abbr></a>. <br>' . 
    'Після встановлення плагіна відкрийте його екстраполя ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';
	

$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Динамічний вивід</span>';
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Індивідуальний вивід</span> у картці товару';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Адміністраторе, для картки товару рекомендується використовувати саме індивідуальний вивід додаткових полів для їх гнучкого налаштування.';