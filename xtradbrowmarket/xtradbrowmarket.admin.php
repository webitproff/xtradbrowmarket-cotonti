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
	
	// сортировка теперь внутри xtradbrowmarket_getExtrafields()
	// usort($extrafields, function($a, $b) {
	// 	return strcmp($a['field_name'], $b['field_name']);
	// });	
	
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
                'FIELD_NAME'        => htmlspecialchars(mb_strtoupper($exfld['field_name'])),
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

    // ========================
    // Сохранение изменений
    // ========================
    if ($a === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        $updatedCount = 0;

        foreach ($ids as $id) {
            $oldData = xtradbrowmarket_load($id);
            $isNewRecord = empty($oldData);
            if ($isNewRecord) {
                $oldData = [];
            }

            $data = [];
            $changed = false;
            $extrafields = xtradbrowmarket_getExtrafields();

            foreach ($extrafields as $exfld) {
                $fname = $exfld['field_name'];
                $postKey = 'rxtra_' . $fname;

                // Определяем старое значение
				if ($isNewRecord) {
					switch ($exfld['field_type']) {
						case 'checkbox':
							$oldValue = 0;
							break;
						case 'datetime':
							$oldValue = 0;
							break;
						case 'select':
							// Для select форма автоматически подставляет дефолт при пустом значении
							$oldValue = $exfld['field_default'] ?? '';
							break;
						default:
							$oldValue = '';
							break;
					}
				} else {
					$oldValue = $oldData[$fname] ?? '';
				}
				
				// ============ ОСОБАЯ ОБРАБОТКА ДЛЯ ПОЛЯ ТИПА FILE ============
				if ($exfld['field_type'] == 'file') {
					$deleteRequested = isset($_POST['rdel_' . $postKey][$id]) && $_POST['rdel_' . $postKey][$id] == 1;
					$hasNewFile = isset($_FILES[$postKey]['name'][$id]) && $_FILES[$postKey]['error'][$id] !== UPLOAD_ERR_NO_FILE;

					// Если нужно удалить старый файл
					if ($deleteRequested && !empty($oldValue)) {
						cot_extrafield_unlinkfiles($oldValue, $exfld);
						$data[$fname] = '';
						$changed = true;
					}

					// Если загружен новый файл
					if ($hasNewFile) {
						// Формируем временное имя для одиночного файла
						$tmpName = 'rxtra_' . $fname . '_' . $id . '_tmp';
						$singleFile = [
							'name'     => $_FILES[$postKey]['name'][$id],
							'type'     => $_FILES[$postKey]['type'][$id],
							'tmp_name' => $_FILES[$postKey]['tmp_name'][$id],
							'error'    => $_FILES[$postKey]['error'][$id],
							'size'     => $_FILES[$postKey]['size'][$id],
						];

						// Временно подменяем $_FILES
						$oldFiles = $_FILES;
						$_FILES[$tmpName] = $singleFile;

						// Вызываем импорт с передачей старого значения
						$newFileValue = cot_import_extrafields($tmpName, $exfld, 'P', $oldValue, 'xtra_');
						$_FILES = $oldFiles;

						// Проверяем ошибки (если cot_error_found(), не сохраняем)
						if (cot_error_found()) {
							// Можно прервать обработку или пропустить поле
							// Здесь просто не сохраняем значение
						} elseif ($newFileValue !== null && $newFileValue !== '') {
							// Если старый файл существует и не был удалён отдельно, удалим его
							if (!empty($oldValue) && !$deleteRequested) {
								cot_extrafield_unlinkfiles($oldValue, $exfld);
							}
							$data[$fname] = $newFileValue;
							$changed = true;
						}
					}

					// Если ничего не изменилось, сохраняем старое значение
					if (!$deleteRequested && !$hasNewFile) {
						$data[$fname] = $oldValue;
					}

					continue;
				}
				// ============ КОНЕЦ ОБРАБОТКИ FILE ============				
				
                $newValue = $oldValue; // по умолчанию не меняем
                $isPosted = isset($_POST[$postKey][$id]);

                // Обработка в зависимости от типа
                if ($exfld['field_type'] == 'checkbox') {
                    $newValue = $isPosted ? 1 : 0;
                } elseif ($exfld['field_type'] == 'checklistbox') {
                    if ($isPosted && is_array($_POST[$postKey][$id])) {
                        $raw = $_POST[$postKey][$id];
                        unset($raw['nullval']);
                        $filtered = array_filter($raw, 'strlen');
                        sort($filtered);
                        $newValue = implode(',', $filtered);
                    } else {
                        $newValue = '';
                    }
                } elseif ($exfld['field_type'] == 'datetime') {
                    if ($isPosted && is_array($_POST[$postKey][$id])) {
                        $raw = $_POST[$postKey][$id];
                        $year   = isset($raw['year'])   ? (int)$raw['year']   : 0;
                        $month  = isset($raw['month'])  ? (int)$raw['month']  : 0;
                        $day    = isset($raw['day'])    ? (int)$raw['day']    : 0;
                        $hour   = isset($raw['hour'])   ? (int)$raw['hour']   : 0;
                        $minute = isset($raw['minute']) ? (int)$raw['minute'] : 0;
                        if ($year && $month && $day) {
                            $newValue = mktime($hour, $minute, 0, $month, $day, $year);
                        } else {
                            $newValue = 0;
                        }
                    } elseif ($isPosted) {
                        $newValue = (int)$_POST[$postKey][$id];
                    }
                } else {
                    if ($isPosted) {
                        $raw = $_POST[$postKey][$id];
                        $newValue = is_array($raw) ? implode(',', $raw) : trim($raw);
                    }
                }

                // Сравнение с учётом типа
                if ($exfld['field_type'] == 'checklistbox') {
                    $newCompare = $newValue;
                    if (!empty($newCompare)) {
                        $newArray = explode(',', $newCompare);
                        sort($newArray);
                        $newCompare = implode(',', $newArray);
                    }
                    $oldCompare = $oldValue;
                    if (!empty($oldCompare)) {
                        $oldArray = explode(',', $oldCompare);
                        sort($oldArray);
                        $oldCompare = implode(',', $oldArray);
                    }
                    if ($newCompare != $oldCompare) {
                        $data[$fname] = $newValue;
                        $changed = true;
                    }
                } elseif ($exfld['field_type'] == 'datetime') {
                    // Для пустых значений оба будут 0, для заполненных сравним как строки даты
                    $newCompare = is_numeric($newValue) ? date('Y-m-d H:i', (int)$newValue) : $newValue;
                    $oldCompare = is_numeric($oldValue) ? date('Y-m-d H:i', (int)$oldValue) : $oldValue;
                    if ($newCompare != $oldCompare) {
                        $data[$fname] = $newValue;
                        $changed = true;
                    }
                } else {
                    if ($newValue != $oldValue) {
                        $data[$fname] = $newValue;
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                xtradbrowmarket_save($id, $data);
                $updatedCount++;
            }
        }

		// Перемещаем загруженные файлы после всех изменений
		cot_extrafield_movefiles();

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

    // ========================
    // Условия WHERE для товаров
    // ========================
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
                // Для новых записей значение пустое (не показываем дефолты)
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
        'tab'       => 'i18n',
        'sq'        => $sq,
        'c'         => $c,
        'search_in' => $search_in,
        'filter_id' => $filter_id,
        'filter'    => $filter,
    ];

    $showAllItems = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_showallitems']);

    // Языковые настройки
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

    // Типы полей, которые можно редактировать и сохранять
    $i18nEditableTypes = ['input', 'textarea'];
    // Загружаем названия стран (для поля типа country)
    $userLang = !empty(Cot::$usr['lang']) ? Cot::$usr['lang'] : Cot::$cfg['defaultlang'];
    $countryFile = $_SERVER['DOCUMENT_ROOT'] . '/lang/' . $userLang . '/countries.' . $userLang . '.lang.php';
    if (file_exists($countryFile)) {
        include $countryFile;
    }
    // Функция форматирования значения для просмотра
    $formatValue = function($exfld, $value) {
        if ($value === null || $value === '') {
            return '';
        }
        switch ($exfld['field_type']) {
            case 'checkbox':
                return $value ? Cot::$L['Yes'] : Cot::$L['No'];
			case 'datetime':
				if ((int)$value === 0) {
					return '';
				}
				return cot_date('datetime_medium', (int)$value);
            case 'country':
                // Используем массив $cot_countries из файла стран
                global $cot_countries;
                return isset($cot_countries[$value]) ? $cot_countries[$value] : htmlspecialchars($value);
            case 'checklistbox':
                // Значение хранится как строка "значение1,значение2,..."
                $parts = explode(',', $value);
                $localized = [];
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '') continue;
                    // Пытаемся найти языковой ключ: имя_поля_значение
                    $langKey = $exfld['field_name'] . '_' . $part;
                    $localized[] = isset(Cot::$L[$langKey]) ? Cot::$L[$langKey] : $part;
                }
                return htmlspecialchars(implode(', ', $localized));
            default:
                // Для select, radio, range и прочих пробуем локализовать через $L
                $langKey = $exfld['field_name'] . '_' . $value;
                if (isset(Cot::$L[$langKey])) {
                    return Cot::$L[$langKey];
                }
                return htmlspecialchars($value);
        }
    };

    // ========================
    // Сохранение изменений
    // ========================
    if ($a === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = isset($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];
        $updatedCount = 0;

        foreach ($ids as $id) {
            $oldData = xtradbrowmarket_load($id);
            $isNewRecord = empty($oldData);
            if ($isNewRecord) {
                $oldData = [];
            }

            $data = [];
            $changed = false;
            $extrafields = xtradbrowmarket_getExtrafields();

            // 1. Обрабатываем основные поля (только input и textarea)
            foreach ($extrafields as $exfld) {
                if (!in_array($exfld['field_type'], $i18nEditableTypes)) {
                    continue;
                }

                $fname = $exfld['field_name'];
                $postKey = 'rxtra_' . $fname;

                if ($isNewRecord) {
                    $oldValue = $exfld['field_default'] ?? '';
                } else {
                    $oldValue = $oldData[$fname] ?? '';
                }

                $newValue = $oldValue;
                if (isset($_POST[$postKey][$id])) {
                    $raw = $_POST[$postKey][$id];
                    $newValue = is_array($raw) ? implode(',', $raw) : trim($raw);
                }

                if ($newValue != $oldValue) {
                    $data[$fname] = $newValue;
                    $changed = true;
                }
            }

            if ($changed) {
                xtradbrowmarket_save($id, $data);
                $updatedCount++;
            }

            // 2. Обрабатываем переводы (только input и textarea)
            if ($i18nActive && !empty($activeLangs)) {
                $i18nChanged = false;
                foreach ($extrafields as $exfld) {
                    if (!in_array($exfld['field_type'], $i18nEditableTypes)) {
                        continue;
                    }
                    $fname = $exfld['field_name'];
                    foreach ($activeLangs as $lang) {
                        if ($lang === $langDefault) {
                            continue;
                        }
                        $i18nKey = 'rxtra_' . $fname . '_' . $lang;
                        if (isset($_POST[$i18nKey][$id])) {
                            $newI18nValue = trim($_POST[$i18nKey][$id]);
                            $oldI18nValue = xtradbrowmarket_i18n_load($id, $fname, $lang) ?? '';
                            if ($newI18nValue != $oldI18nValue) {
                                xtradbrowmarket_i18n_save($id, $fname, $lang, $newI18nValue);
                                $i18nChanged = true;
                            }
                        }
                    }
                }
                if ($i18nChanged && !$changed) {
                    $updatedCount++;
                }
            }
        }

        $msg = '';
        if ($updatedCount > 0) {
            $msg .= sprintf(Cot::$L['xtradbrowmarket_updated'], $updatedCount);
        }
        if (!empty($msg)) {
            cot_message($msg);
        }

        $backUrl = cot_url('admin', array_merge($urlParams, ['d' => $durl]));
        $backUrl = str_replace('&amp;', '&', $backUrl);
        cot_redirect($backUrl);
    }

    // ========================
    // WHERE для товаров
    // ========================
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
            if ($i18nActive && in_array($exfld['field_type'], $i18nEditableTypes)) {
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
                $isEditable = in_array($exfld['field_type'], $i18nEditableTypes);
                $isNew = (!isset($row['itempagid']) || $row['itempagid'] === null);

                // Определяем значение для отображения
                if ($isNew) {
                    // Для новой записи: для редактируемых полей показываем дефолт,
                    // для остальных — пусто
                    $value = $isEditable ? ($exfld['field_default'] ?? '') : '';
                } else {
                    $value = $row[$fname] ?? '';
                }

                if ($isEditable) {
                    // Редактируемое поле: выводим форму
                    $inputName = 'rxtra_' . $fname . '[' . $id . ']';
                    $fieldHtml = cot_build_extrafields($inputName, $exfld, $value);
                } else {
                    // Нередактируемое поле: выводим читаемое значение
                    $fieldHtml = $formatValue($exfld, $value);
                }
                $t->assign('FIELD_HTML', $fieldHtml);

                // Поля переводов для редактируемых
                if ($i18nActive && $isEditable) {
                    foreach ($activeLangs as $lang) {
                        $langValue = xtradbrowmarket_i18n_load($id, $fname, $lang) ?? '';
                        $langInputName = 'rxtra_' . $fname . '_' . $lang . '[' . $id . ']';
						if ($exfld['field_type'] === 'textarea') {
							$langHtml = cot_textarea($langInputName, $langValue, 5, 40);
						} else {
							$langHtml = cot_inputbox('text', $langInputName, $langValue, 'class="form-control form-control-sm"');
						}
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
