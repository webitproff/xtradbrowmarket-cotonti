<?php
/**
 * xtradbrowmarket API
 * Функции плагина: plugins/xtradbrowmarket/inc/xtradbrowmarket.functions.php
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

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('xtradbrowmarket', 'plug');
require_once cot_incfile('market', 'module'); 
require_once cot_incfile('extrafields');
Cot::$db->registerTable('xtradbrowmarket');

/**
 * Возвращает массив зарегистрированных extrafields для таблицы cot_xtradbrowmarket
 */
function xtradbrowmarket_getExtrafields() {
    return Cot::$extrafields[Cot::$db->xtradbrowmarket] ?? [];
}

/**
 * Загружает запись из cot_xtradbrowmarket по itempagid
 * @param int $page_id ID страницы из модуля Market
 * @return array|null
 */
function xtradbrowmarket_load($page_id) {
    $res = Cot::$db->query("SELECT * FROM " . Cot::$db->xtradbrowmarket . " WHERE itempagid = ?", [$page_id]);
    return $res->fetch();
}

/**
 * Сохраняет данные (INSERT или UPDATE) в таблицу cot_xtradbrowmarket
 * @param int $page_id ID страницы из модуля Market
 * @param array $data Ассоциативный массив с именами полей extrafields (без префикса `item_` или `pag_`)
 */
function xtradbrowmarket_save($page_id, $data) {
    $exists = Cot::$db->query(
        "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket . " WHERE itempagid = ?",
        [$page_id]
    )->fetchColumn() > 0;

    if ($exists) {
        Cot::$db->update(Cot::$db->xtradbrowmarket, $data, "itempagid = ?", [$page_id]);
    } else {
        $data['itempagid'] = $page_id;
        Cot::$db->insert(Cot::$db->xtradbrowmarket, $data);
    }
}