<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
==================== */

/**
 * xtradbrowmarket Plugin
 *
 * Date: Apr 27Th, 2026
 * @package xtradbrowmarket
 * @version 2.7.9
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

$extra_whitelist[$db_xtradbrowmarket] = [
    'name'    => $db_xtradbrowmarket,
    'caption' => $L['xtradbrowmarket'],
    'type'    => 'plug',
    'code'    => 'xtradbrowmarket',
    'tags'    => [
        'market.edit.tpl' => '{MARKETEDIT_FORM_XTRA_XXXXX}, {MARKETEDIT_FORM_XTRA_XXXXX_TITLE}',
        'market.tpl'      => '{MARKET_XTRA_XXXXX}, {MARKET_XTRA_XXXXX_TITLE}',
        'market.list.tpl' => '{LIST_ROW_XTRA_XXXXX}, {LIST_ROW_XTRA_XXXXX_TITLE}',
        'header.tpl'      => '{MARKET_HEADER_XTRA_XXXXX}, {MARKET_HEADER_XTRA_TITLE}',
    ]
];