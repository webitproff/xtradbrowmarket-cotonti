<?php
/* ====================
[BEGIN_COT_EXT]
Code=xtradbrowmarket
Name=Extrafields Market Custom i18n
Description=Extrafields Market Custom for pages in Market PRO v.5 with multilingual support. https://github.com/webitproff/marketpro-cotonti
Version=4.1.1
Date=Aug 20Th, 2026
Author=webitproff
Copyright=Copyright (c) 2026 webitproff https://github.com/webitproff/xtradbrowmarket-cotonti
Notes=BSD License
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Lock_members=
Requires_modules=market
[END_COT_EXT]


[BEGIN_COT_EXT_CONFIG]
xtradbrowmarket_i18n_use=01:radio::1:Мультиязычность полей активировать и использовать
xtradbrowmarket_i18n_lang_code_default=02:string::ru:Код основного языка сайта (должен совпадать с <code>$cfg['defaultlang']</code>)
xtradbrowmarket_i18n_lang_code_first=03:string::en:Код первого дополнительного языка
xtradbrowmarket_i18n_lang_code_first_use=04:radio::1:Использовать первый дополнительный язык
xtradbrowmarket_i18n_lang_code_second=05:string::ua:Код второго дополнительного языка
xtradbrowmarket_i18n_lang_code_second_use=06:radio::0:Использовать второй дополнительный язык
xtradbrowmarket_i18n_lang_code_third=07:string::pl:Код третьего дополнительного языка
xtradbrowmarket_i18n_lang_code_third_use=08:radio::0:Использовать третий дополнительный язык
perpage=09:string::5:Items per page in admin list
xtradbrowmarket_showallitems=10:radio::1:Show all items (even without extrafields) in admin list
help_info=11:custom:xtradbrowmarket_setup_help_block()::the reference information block
[END_COT_EXT_CONFIG]
==================== */

defined('COT_CODE') or die('Wrong URL');

/**
 * xtradbrowmarket.setup.php - Register data in $db_core and $db_config. Setup & Config File for the Plugin xtradbrowmarket
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.extrafields.php
 *
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 20Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */
