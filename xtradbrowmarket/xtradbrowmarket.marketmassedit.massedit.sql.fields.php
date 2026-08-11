<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.sql.fields
Order=10
[END_COT_EXT]
==================== */

// plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.sql.fields.php

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('xtradbrowmarket', 'plug');

// Добавляем все колонки таблицы xtradbrowmarket в общий список полей
$selectFields[] = Cot::$db->xtradbrowmarket . '.*';
// Сообщаем marketmassedit, что нужен LEFT JOIN с этой таблицей
$needXtraJoin = Cot::$db->xtradbrowmarket;