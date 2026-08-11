<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Admin panel for xtradbrowmarket – Statistics, Edit extrafields, Edit with i18n
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.admin.php
 *
 * Custom Extrafields Market i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 11Th, 2026
 * @package xtradbrowmarket
 * @version 4.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('xtradbrowmarket', 'plug');
require_once cot_incfile('xtradbrowmarket', 'plug');
require_once cot_incfile('market', 'module');
require_once cot_incfile('extrafields');

$tab = cot_import('tab', 'G', 'ALP') ?: 'stats';
$a   = cot_import('a',   'G', 'ALP');

$t = new XTemplate(cot_tplfile('xtradbrowmarket.admin', 'plug', true));

$t->assign([
    'TAB_STATS_ACTIVE' => $tab === 'stats' ? 'active' : '',
    'TAB_EDIT_ACTIVE'  => $tab === 'edit'  ? 'active' : '',
    'TAB_I18N_ACTIVE'  => $tab === 'i18n'  ? 'active' : '',
    'URL_STATS'        => cot_url('admin', ['m'=>'other','p'=>'xtradbrowmarket','tab'=>'stats']),
    'URL_EDIT'         => cot_url('admin', ['m'=>'other','p'=>'xtradbrowmarket','tab'=>'edit']),
    'URL_I18N'         => cot_url('admin', ['m'=>'other','p'=>'xtradbrowmarket','tab'=>'i18n']),
]);

$perPage = (int) (Cot::$cfg['plugin']['xtradbrowmarket']['perpage'] ?? 20);
if ($perPage < 1) $perPage = 20;

/* ========== ВКЛАДКА СТАТИСТИКА ========== */
if ($tab === 'stats') {
    $totalItems = Cot::$db->query(
        "SELECT COUNT(*) FROM $db_market WHERE fieldmrkt_title != ''"
    )->fetchColumn();

    $totalXtraRows = Cot::$db->query(
        "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket
    )->fetchColumn();

    $extrafields = xtradbrowmarket_getExtrafields();
    $filledCount = 0;
    if (!empty($extrafields)) {
        $nonEmpty = [];
        foreach ($extrafields as $exfld) {
            $fname = $exfld['field_name'];
            $nonEmpty[] = "$fname IS NOT NULL AND $fname != ''";
        }
        if (!empty($nonEmpty)) {
            $where = implode(' OR ', $nonEmpty);
            $filledCount = Cot::$db->query(
                "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket . " WHERE $where"
            )->fetchColumn();
        }
    }

    $t->assign([
        'STATS_TOTAL_ITEMS'     => $totalItems,
        'STATS_TOTAL_XTRA_ROWS' => $totalXtraRows,
        'STATS_FILLED_COUNT'    => $filledCount,
    ]);

    if (!empty($extrafields)) {
        foreach ($extrafields as $exfld) {
            $t->assign([
                'FIELD_NAME'        => htmlspecialchars($exfld['field_name']),
                'FIELD_TYPE'        => htmlspecialchars($exfld['field_type']),
                'FIELD_DESCRIPTION' => htmlspecialchars($exfld['field_description'] ?? ''),
                'FIELD_VARIANTS'    => htmlspecialchars($exfld['field_variants'] ?? ''),
                'FIELD_PARAMS'      => htmlspecialchars($exfld['field_params'] ?? ''),
                'FIELD_DEFAULT'     => htmlspecialchars($exfld['field_default'] ?? ''),
                'FIELD_REQUIRED'    => $exfld['field_required'] ? Cot::$L['Yes'] : Cot::$L['No'],
                'FIELD_ENABLED'     => $exfld['field_enabled'] ? Cot::$L['Yes'] : Cot::$L['No'],
            ]);
            $t->parse('MAIN.STATS_EXTRAFIELDS_ROW');
        }
    } else {
        $t->parse('MAIN.STATS_NO_EXTRAFIELDS');
    }
}

/* ========== ВКЛАДКА РЕДАКТИРОВАНИЕ (основные данные) ========== */
if ($tab === 'edit') {
    list($pg, $d, $durl) = cot_import_pagenav('d', $perPage);

    // Параметры поиска – из POST (скрытые поля) при отправке, иначе из GET
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sq        = cot_import('sq', 'P', 'TXT');
        $c         = cot_import('c', 'P', 'TXT');
        $search_in = cot_import('search_in', 'P', 'ALP');
        $filter_id = cot_import('filter_id', 'P', 'INT');
        $filter    = cot_import('filter', 'P', 'ALP');
    } else {
        $sq        = cot_import('sq', 'G', 'TXT');
        $c         = cot_import('c', 'G', 'TXT');
        $search_in = cot_import('search_in', 'G', 'ALP', 8);
        $filter_id = cot_import('filter_id', 'G', 'INT');
        $filter    = cot_import('filter', 'G', 'ALP');
    }
    $sq = ($sq !== null) ? trim($sq) : '';
    if (!in_array($search_in, ['title', 'full', 'pcod'])) {
        $search_in = 'title';
    }
    $filter = empty($filter) ? 'all' : $filter;

    // Все параметры сохраняем в URL
    $urlParams = [
        'm'         => 'other',
        'p'         => 'xtradbrowmarket',
        'tab'       => 'edit',
        'sq'        => $sq,
        'c'         => $c,
        'search_in' => $search_in,
        'filter_id' => $filter_id,
        'filter'    => $filter,
    ];

    // Читаем настройку: показывать все товары или только с заполненными полями
    $showAllItems = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_showallitems']);

    // Сохранение изменений
    if ($a === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        $updatedCount = 0;
        foreach ($ids as $id) {
            $data = [];
            $oldData = xtradbrowmarket_load($id);
            if (!$oldData) {
                $oldData = [];
            }
            $extrafields = xtradbrowmarket_getExtrafields();
            foreach ($extrafields as $exfld) {
                $fname = $exfld['field_name'];
                $postKey = 'rxtra_' . $fname;
                if (isset($_POST[$postKey][$id])) {
                    $newValue = $_POST[$postKey][$id];
                    $oldValue = $oldData[$fname] ?? '';
                    if ($newValue != $oldValue) {
                        $data[$fname] = $newValue;
                    }
                }
            }
            if (!empty($data)) {
                xtradbrowmarket_save($id, $data);
                $updatedCount++;
            }
        }
        $msg = '';
        if ($updatedCount > 0) {
            $msg .= sprintf(Cot::$L['xtradbrowmarket_updated'], $updatedCount);
        }
        if (!empty($msg)) {
            cot_message($msg);
        }

        // Редирект с сохранением всех фильтров и пагинации
        $backUrl = cot_url('admin', array_merge($urlParams, ['d' => $durl]));
        $backUrl = str_replace('&amp;', '&', $backUrl);
        cot_redirect($backUrl);
    }

    // Условия WHERE для товаров
    $sqlwhere = "m.fieldmrkt_title IS NOT NULL AND m.fieldmrkt_title != ''";
    $params = [];

    if (!empty($sq)) {
        $sq_escaped = "%$sq%";
        if ($search_in == 'title') {
            $sqlwhere .= " AND m.fieldmrkt_title LIKE :sq";
        } elseif ($search_in == 'full') {
            $sqlwhere .= " AND (m.fieldmrkt_title LIKE :sq OR m.fieldmrkt_text LIKE :sq)";
        } elseif ($search_in == 'pcod') {
            $sqlwhere .= " AND m.fieldmrkt_pcod LIKE :sq";
        }
        $params['sq'] = $sq_escaped;
    }

    if (!empty($filter_id)) {
        $sqlwhere .= " AND m.fieldmrkt_id = :fid";
        $params['fid'] = $filter_id;
    }

    if (!empty($c)) {
        $catsub = cot_structure_children('market', $c);
        if (!empty($catsub)) {
            $sqlwhere .= " AND m.fieldmrkt_cat IN ('" . implode("','", $catsub) . "')";
        }
    }

    if ($filter == 'valqueue') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 1';
    } elseif ($filter == 'validated') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 0';
    } elseif ($filter == 'drafts') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 2';
    }

    // Подсчёт количества записей в зависимости от режима
    if ($showAllItems) {
        $total = Cot::$db->query(
            "SELECT COUNT(*) FROM $db_market AS m WHERE $sqlwhere", $params
        )->fetchColumn();
    } else {
        $total = Cot::$db->query(
            "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket . " AS t
             LEFT JOIN $db_market AS m ON m.fieldmrkt_id = t.itempagid
             WHERE $sqlwhere", $params
        )->fetchColumn();
    }

    $searchMsg = '';
    if (!empty($sq) || !empty($filter_id)) {
        $totalFound = (int)$total;
        $queryDesc = [];
        if (!empty($sq)) $queryDesc[] = '«'.htmlspecialchars($sq).'»';
        if (!empty($filter_id)) $queryDesc[] = 'ID '.$filter_id;
        if ($totalFound > 0) {
            $searchMsg = sprintf(Cot::$L['xtradbrowmarket_search_result_msg'], cot_declension($totalFound, Cot::$L['xtradbrowmarket_search_declen']), implode(', ', $queryDesc));
        } else {
            $searchMsg = sprintf(Cot::$L['xtradbrowmarket_search_result_none'], implode(', ', $queryDesc));
        }
    }

    // Получение данных
    if ($showAllItems) {
        $items = Cot::$db->query(
            "SELECT m.fieldmrkt_id, m.fieldmrkt_title, m.fieldmrkt_alias, m.fieldmrkt_cat, t.*
             FROM $db_market AS m
             LEFT JOIN " . Cot::$db->xtradbrowmarket . " AS t ON t.itempagid = m.fieldmrkt_id
             WHERE $sqlwhere
             ORDER BY m.fieldmrkt_id DESC
             LIMIT $d, $perPage",
            $params
        )->fetchAll();
    } else {
        $items = Cot::$db->query(
            "SELECT t.*, m.fieldmrkt_title, m.fieldmrkt_alias, m.fieldmrkt_cat
             FROM " . Cot::$db->xtradbrowmarket . " AS t
             LEFT JOIN $db_market AS m ON m.fieldmrkt_id = t.itempagid
             WHERE $sqlwhere
             ORDER BY t.itempagid DESC
             LIMIT $d, $perPage",
            $params
        )->fetchAll();
    }

    $t->assign([
        'SEARCH_ACTION_URL'     => cot_url('admin'),
        'SEARCH_SQ'             => cot_inputbox('text', 'sq', !empty($sq) ? htmlspecialchars($sq) : '', 'class="form-control" autofocus'),
        'SEARCH_CAT'            => cot_selectbox_structure('market', $c, 'c', Cot::$L['All']),
        'SEARCH_CAT_SELECT2'    => cot_market_selectcat_select2($c, 'c'),
        'SEARCH_FILTER_ID'      => cot_inputbox('number', 'filter_id', !empty($filter_id) ? $filter_id : '', 'class="form-control" placeholder="ID"'),
        'FILTER_STATE_SELECT'   => cot_selectbox($filter, 'filter', ['all', 'valqueue', 'validated', 'drafts'],
            [Cot::$L['All'], Cot::$L['market_status_pending'], Cot::$L['market_status_published'], Cot::$L['market_status_draft']], false),
        'SEARCH_RESULT_MSG'     => $searchMsg,
        'SEARCH_IN_TITLE_CHECKED' => ($search_in == 'title') ? 'checked="checked"' : '',
        'SEARCH_IN_FULL_CHECKED'  => ($search_in == 'full')  ? 'checked="checked"' : '',
        'SEARCH_IN_PCOD_CHECKED'  => ($search_in == 'pcod')  ? 'checked="checked"' : '',
        'SEARCH_SQ_VALUE'        => htmlspecialchars($sq ?? ''),
        'SEARCH_C_VALUE'         => htmlspecialchars($c ?? ''),
        'SEARCH_IN_VALUE'        => htmlspecialchars($search_in ?? ''),
        'SEARCH_FILTER_ID_VALUE' => htmlspecialchars($filter_id ?? ''),
        'FILTER_VALUE'           => htmlspecialchars($filter),
    ]);

    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        foreach ($extrafields as $exfld) {
            $t->assign('FIELD_HEADER_TITLE', htmlspecialchars(cot_extrafield_title($exfld, 'xtra_')));
            $t->parse('MAIN.EDIT_FIELDS_HEADER');
        }
    }

    if (!empty($items)) {
        foreach ($items as $row) {
            $id = $row['fieldmrkt_id'] ?? $row['itempagid']; // в зависимости от режима id находится в разных полях
            $itemUrl = cot_url('market', !empty($row['fieldmrkt_alias'])
                ? ['c' => $row['fieldmrkt_cat'], 'al' => $row['fieldmrkt_alias']]
                : ['c' => $row['fieldmrkt_cat'], 'id' => $id]
            );
            $t->assign([
                'MANAGE_ID'    => $id,
                'MANAGE_TITLE' => htmlspecialchars($row['fieldmrkt_title'] ?? ''),
                'MANAGE_URL'   => $itemUrl,
            ]);

            foreach ($extrafields as $exfld) {
                $fname = $exfld['field_name'];
                $value = $row[$fname] ?? '';
                $inputName = 'rxtra_' . $fname . '[' . $id . ']';
                $fieldHtml = cot_build_extrafields($inputName, $exfld, $value);
                $t->assign('FIELD_HTML', $fieldHtml);
                $t->parse('MAIN.EDIT_ROW.FIELD_CELL');
            }
            $t->parse('MAIN.EDIT_ROW');
        }
    } else {
        $t->parse('MAIN.EDIT_EMPTY');
    }

    $pagenav = cot_pagenav('admin', $urlParams, $d, $total, $perPage, 'd');
    $t->assign(cot_generatePaginationTags($pagenav));
    $t->assign('EDIT_FORM_URL', cot_url('admin', ['m'=>'other','p'=>'xtradbrowmarket','tab'=>'edit','a'=>'update','d'=>$durl]));
}
/* ========== ВКЛАДКА РЕДАКТИРОВАНИЕ С ПЕРЕВОДАМИ (i18n) ========== */
if ($tab === 'i18n') {
    list($pg, $d, $durl) = cot_import_pagenav('d', $perPage);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sq        = cot_import('sq', 'P', 'TXT');
        $c         = cot_import('c', 'P', 'TXT');
        $search_in = cot_import('search_in', 'P', 'ALP');
        $filter_id = cot_import('filter_id', 'P', 'INT');
        $filter    = cot_import('filter', 'P', 'ALP');
    } else {
        $sq        = cot_import('sq', 'G', 'TXT');
        $c         = cot_import('c', 'G', 'TXT');
        $search_in = cot_import('search_in', 'G', 'ALP', 8);
        $filter_id = cot_import('filter_id', 'G', 'INT');
        $filter    = cot_import('filter', 'G', 'ALP');
    }
    $sq = ($sq !== null) ? trim($sq) : '';
    if (!in_array($search_in, ['title', 'full', 'pcod'])) {
        $search_in = 'title';
    }
    $filter = empty($filter) ? 'all' : $filter;

    $urlParams = [
        'm'         => 'other',
        'p'         => 'xtradbrowmarket',
        'tab'       => 'i18n',
        'sq'        => $sq,
        'c'         => $c,
        'search_in' => $search_in,
        'filter_id' => $filter_id,
        'filter'    => $filter,
    ];

    $showAllItems = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_showallitems']);

    $i18nActive = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use']);
    $activeLangs = [];
    $langDefault = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default'])
        ? Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default']
        : Cot::$cfg['defaultlang'];

    if ($i18nActive) {
        $pairs = [
            ['use' => 'xtradbrowmarket_i18n_lang_code_first_use',  'code' => 'xtradbrowmarket_i18n_lang_code_first'],
            ['use' => 'xtradbrowmarket_i18n_lang_code_second_use', 'code' => 'xtradbrowmarket_i18n_lang_code_second'],
            ['use' => 'xtradbrowmarket_i18n_lang_code_third_use',  'code' => 'xtradbrowmarket_i18n_lang_code_third'],
        ];
        foreach ($pairs as $pair) {
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket'][$pair['use']])
                && !empty(Cot::$cfg['plugin']['xtradbrowmarket'][$pair['code']])) {
                $lang = Cot::$cfg['plugin']['xtradbrowmarket'][$pair['code']];
                if ($lang !== $langDefault) {
                    $activeLangs[] = $lang;
                }
            }
        }
    }

    // Сохранение
    if ($a === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        $updatedCount = 0;
        foreach ($ids as $id) {
            $data = [];
            $oldData = xtradbrowmarket_load($id);
            if (!$oldData) {
                $oldData = [];
            }
            $extrafields = xtradbrowmarket_getExtrafields();
            foreach ($extrafields as $exfld) {
                $fname = $exfld['field_name'];
                $postKey = 'rxtra_' . $fname;
                if (isset($_POST[$postKey][$id])) {
                    $newValue = $_POST[$postKey][$id];
                    $oldValue = $oldData[$fname] ?? '';
                    if ($newValue != $oldValue) {
                        $data[$fname] = $newValue;
                    }
                }
            }
            if (!empty($data)) {
                xtradbrowmarket_save($id, $data);
                $updatedCount++;
            }

            if ($i18nActive && !empty($activeLangs)) {
                $i18nChanged = false;
                foreach ($extrafields as $exfld) {
                    if (!in_array($exfld['field_type'], ['input', 'textarea'])) continue;
                    $fname = $exfld['field_name'];
                    foreach ($activeLangs as $lang) {
                        $i18nKey = 'rxtra_' . $fname . '_' . $lang;
                        if (isset($_POST[$i18nKey][$id])) {
                            $newI18nValue = $_POST[$i18nKey][$id];
                            $oldI18nValue = xtradbrowmarket_i18n_load($id, $fname, $lang) ?? '';
                            if ($newI18nValue != $oldI18nValue) {
                                xtradbrowmarket_i18n_save($id, $fname, $lang, $newI18nValue);
                                $i18nChanged = true;
                            }
                        }
                    }
                }
                if ($i18nChanged && empty($data)) {
                    $updatedCount++;
                }
            }
        }
        $msg = '';
        if ($updatedCount > 0) $msg .= sprintf(Cot::$L['xtradbrowmarket_updated'], $updatedCount);
        if (!empty($msg)) cot_message($msg);

        $backUrl = cot_url('admin', array_merge($urlParams, ['d'=>$durl]));
        $backUrl = str_replace('&amp;', '&', $backUrl);
        cot_redirect($backUrl);
    }

    // WHERE для товаров
    $sqlwhere = "m.fieldmrkt_title IS NOT NULL AND m.fieldmrkt_title != ''";
    $params = [];

    if (!empty($sq)) {
        $sq_escaped = "%$sq%";
        if ($search_in == 'title') {
            $sqlwhere .= " AND m.fieldmrkt_title LIKE :sq";
        } elseif ($search_in == 'full') {
            $sqlwhere .= " AND (m.fieldmrkt_title LIKE :sq OR m.fieldmrkt_text LIKE :sq)";
        } elseif ($search_in == 'pcod') {
            $sqlwhere .= " AND m.fieldmrkt_pcod LIKE :sq";
        }
        $params['sq'] = $sq_escaped;
    }

    if (!empty($filter_id)) {
        $sqlwhere .= " AND m.fieldmrkt_id = :fid";
        $params['fid'] = $filter_id;
    }

    if (!empty($c)) {
        $catsub = cot_structure_children('market', $c);
        if (!empty($catsub)) {
            $sqlwhere .= " AND m.fieldmrkt_cat IN ('" . implode("','", $catsub) . "')";
        }
    }

    if ($filter == 'valqueue') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 1';
    } elseif ($filter == 'validated') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 0';
    } elseif ($filter == 'drafts') {
        $sqlwhere .= ' AND m.fieldmrkt_state = 2';
    }

    if ($showAllItems) {
        $total = Cot::$db->query(
            "SELECT COUNT(*) FROM $db_market AS m WHERE $sqlwhere", $params
        )->fetchColumn();
    } else {
        $total = Cot::$db->query(
            "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket . " AS t
             LEFT JOIN $db_market AS m ON m.fieldmrkt_id = t.itempagid
             WHERE $sqlwhere", $params
        )->fetchColumn();
    }

    $searchMsg = '';
    if (!empty($sq) || !empty($filter_id)) {
        $totalFound = (int)$total;
        $queryDesc = [];
        if (!empty($sq)) $queryDesc[] = '«'.htmlspecialchars($sq).'»';
        if (!empty($filter_id)) $queryDesc[] = 'ID '.$filter_id;
        if ($totalFound > 0) {
            $searchMsg = sprintf(Cot::$L['xtradbrowmarket_search_result_msg'], cot_declension($totalFound, Cot::$L['xtradbrowmarket_search_declen']), implode(', ', $queryDesc));
        } else {
            $searchMsg = sprintf(Cot::$L['xtradbrowmarket_search_result_none'], implode(', ', $queryDesc));
        }
    }

    if ($showAllItems) {
        $items = Cot::$db->query(
            "SELECT m.fieldmrkt_id, m.fieldmrkt_title, m.fieldmrkt_alias, m.fieldmrkt_cat, t.*
             FROM $db_market AS m
             LEFT JOIN " . Cot::$db->xtradbrowmarket . " AS t ON t.itempagid = m.fieldmrkt_id
             WHERE $sqlwhere
             ORDER BY m.fieldmrkt_id DESC
             LIMIT $d, $perPage",
            $params
        )->fetchAll();
    } else {
        $items = Cot::$db->query(
            "SELECT t.*, m.fieldmrkt_title, m.fieldmrkt_alias, m.fieldmrkt_cat
             FROM " . Cot::$db->xtradbrowmarket . " AS t
             LEFT JOIN $db_market AS m ON m.fieldmrkt_id = t.itempagid
             WHERE $sqlwhere
             ORDER BY t.itempagid DESC
             LIMIT $d, $perPage",
            $params
        )->fetchAll();
    }

    $t->assign([
        'SEARCH_ACTION_URL'  => cot_url('admin'),
        'SEARCH_SQ'          => cot_inputbox('text', 'sq', !empty($sq) ? htmlspecialchars($sq) : '', 'class="form-control" autofocus'),
        'SEARCH_CAT'         => cot_selectbox_structure('market', $c, 'c', Cot::$L['All']),
        'SEARCH_CAT_SELECT2' => cot_market_selectcat_select2($c, 'c'),
        'SEARCH_FILTER_ID'   => cot_inputbox('number', 'filter_id', !empty($filter_id) ? $filter_id : '', 'class="form-control" placeholder="ID"'),
        'FILTER_STATE_SELECT' => cot_selectbox($filter, 'filter', ['all', 'valqueue', 'validated', 'drafts'],
            [Cot::$L['All'], Cot::$L['market_status_pending'], Cot::$L['market_status_published'], Cot::$L['market_status_draft']], false),
        'SEARCH_RESULT_MSG'  => $searchMsg,
        'SEARCH_IN_TITLE_CHECKED' => ($search_in == 'title') ? 'checked="checked"' : '',
        'SEARCH_IN_FULL_CHECKED'  => ($search_in == 'full')  ? 'checked="checked"' : '',
        'SEARCH_IN_PCOD_CHECKED'  => ($search_in == 'pcod')  ? 'checked="checked"' : '',
        'I18N_ACTIVE'        => $i18nActive,
        'SEARCH_SQ_VALUE'        => htmlspecialchars($sq ?? ''),
        'SEARCH_C_VALUE'         => htmlspecialchars($c ?? ''),
        'SEARCH_IN_VALUE'        => htmlspecialchars($search_in ?? ''),
        'SEARCH_FILTER_ID_VALUE' => htmlspecialchars($filter_id ?? ''),
        'FILTER_VALUE'           => htmlspecialchars($filter),
    ]);

    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        foreach ($extrafields as $exfld) {
            $t->assign('FIELD_HEADER_TITLE', htmlspecialchars(cot_extrafield_title($exfld, 'xtra_')));
            if ($i18nActive && in_array($exfld['field_type'], ['input', 'textarea'])) {
                foreach ($activeLangs as $lang) {
                    $t->assign('LANG_HEADER', strtoupper($lang));
                    $t->parse('MAIN.I18N_FIELDS_HEADER.LANG_HEADER_ROW');
                }
            }
            $t->parse('MAIN.I18N_FIELDS_HEADER');
        }
    }

    if (!empty($items)) {
        foreach ($items as $row) {
            $id = $row['fieldmrkt_id'] ?? $row['itempagid'];
            $itemUrl = cot_url('market', !empty($row['fieldmrkt_alias'])
                ? ['c' => $row['fieldmrkt_cat'], 'al' => $row['fieldmrkt_alias']]
                : ['c' => $row['fieldmrkt_cat'], 'id' => $id]
            );
            $t->assign([
                'MANAGE_ID'    => $id,
                'MANAGE_TITLE' => htmlspecialchars($row['fieldmrkt_title'] ?? ''),
                'MANAGE_URL'   => $itemUrl,
            ]);

            foreach ($extrafields as $exfld) {
                $fname = $exfld['field_name'];
                $value = $row[$fname] ?? '';
                $inputName = 'rxtra_' . $fname . '[' . $id . ']';
                $fieldHtml = cot_build_extrafields($inputName, $exfld, $value);
                $t->assign('FIELD_HTML', $fieldHtml);

                if ($i18nActive && in_array($exfld['field_type'], ['input', 'textarea'])) {
                    foreach ($activeLangs as $lang) {
                        $langValue = xtradbrowmarket_i18n_load($id, $fname, $lang) ?? '';
                        $langInputName = 'rxtra_' . $fname . '_' . $lang . '[' . $id . ']';
                        $langHtml = cot_inputbox('text', $langInputName, $langValue, 'class="form-control form-control-sm"');
                        $t->assign('LANG_FIELD', $langHtml);
                        $t->parse('MAIN.I18N_ROW.FIELD_CELL.LANG_ROW');
                    }
                }
                $t->parse('MAIN.I18N_ROW.FIELD_CELL');
            }
            $t->parse('MAIN.I18N_ROW');
        }
    } else {
        $t->parse('MAIN.I18N_EMPTY');
    }

    $pagenav = cot_pagenav('admin', $urlParams, $d, $total, $perPage, 'd');
    $t->assign(cot_generatePaginationTags($pagenav));
    $t->assign('I18N_FORM_URL', cot_url('admin', ['m'=>'other','p'=>'xtradbrowmarket','tab'=>'i18n','a'=>'update','d'=>$durl]));
}

cot_display_messages($t);
$t->parse('MAIN');
$pluginBody = $t->text('MAIN');