<?php
/**
 * Polish Language File for xtradbrowmarket Plugin
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

$L['info_desc'] = 'Wtyczka dodaje dodatkowe pola dla modułu "Market PRO v.5", ale do własnej tabeli w bazie danych';

$L['info_notes'] = 
    'Dla początkujących ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Wprowadzenie. Opis i zasady działania pól dodatkowych w Cotonti" class="initialism">' .
    '<strong>koniecznie przeczytaj dział forum o API ExtraFields</strong></abbr></a>. <br>' . 
    'Po zainstalowaniu wtyczki otwórz jej dodatkowe pola ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';
	

$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Pola dodatkowe <code>xtradbrowmarket</code>. Wyświetlanie dynamiczne</span>';
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Pola dodatkowe <code>xtradbrowmarket</code>. Wyświetlanie indywidualne</span> w karcie produktu';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Administratorze, dla karty produktu zaleca się używanie indywidualnego wyświetlania dodatkowych pól w celu elastycznej personalizacji.';