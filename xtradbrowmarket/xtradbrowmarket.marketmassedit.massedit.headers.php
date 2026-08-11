<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.headers
Order=10
[END_COT_EXT]
==================== */

// plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.headers.php


defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('xtradbrowmarket', 'plug');

$extrafields = xtradbrowmarket_getExtrafields();
if (!empty($extrafields)) {
    foreach ($extrafields as $exfld) {
        $t->assign('XTRA_HEADER_TITLE', htmlspecialchars(cot_extrafield_title($exfld, 'xtra_')));
        $t->parse('MAIN.XTRA_HEADER');
    }
}