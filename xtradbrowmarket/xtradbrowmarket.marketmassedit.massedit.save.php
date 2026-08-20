<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=marketmassedit.massedit.save
Order=10
[END_COT_EXT]
==================== */


/**
 * Сохранение данных плагина xtradbrowmarket при массовом редактировании товаров.
 *
 * Файл подключается к хуку `marketmassedit.massedit.save`, который вызывается
 * в плагине `marketmassedit` (marketmassedit.admin.php) после обработки
 * основных полей товара и его экстраполей из таблицы `cot_market`.
 * В момент вызова хука уже известны:
 *   - `$ids`  (array)  — массив ID товаров, выбранных для массового сохранения;
 *   - `$id`   (int)    — ID текущего товара внутри цикла (используется ниже);
 *   - `$_POST`, `$_FILES` — данные формы массового редактирования.
 *
 * Назначение файла:
 *   - Загрузить все экстраполя плагина через `xtradbrowmarket_getExtrafields()`.
 *   - Для каждого товара ($id) загрузить текущую запись из `cot_xtradbrowmarket`
 *     через `xtradbrowmarket_load($id)`.
 *   - Обойти каждое экстраполе и импортировать новое значение с учётом типа:
 *       * `file`       — обрабатывается отдельно: удаление через `rdel_` и
 *                        загрузка нового файла через `cot_import_extrafields()`
 *                        с временной подменой `$_FILES`; старый файл удаляется
 *                        через `cot_extrafield_unlinkfiles()`.
 *       * `checkbox`    — значение 0 или 1.
 *       * `checklistbox` — массив преобразуется в строку с разделителем `,`.
 *       * `datetime`    — собирается timestamp или берётся как int.
 *       * остальные     — значение из `$_POST` (при отсутствии — старое).
 *   - Если хотя бы одно поле изменилось, сохранить данные через
 *     `xtradbrowmarket_save($id, $data)`.
 *   - После обработки всех товаров переместить загруженные файлы
 *     из временной директории в целевую через `cot_extrafield_movefiles()`.
 *
 * Прямые связи:
 *   - Хук:              `marketmassedit.massedit.save` (определён в плагине marketmassedit).
 *   - Основной файл:    plugins/marketmassedit/marketmassedit.admin.php
 *   - Функция загрузки: `xtradbrowmarket_load()` — plugins/xtradbrowmarket/inc/xtradbrowmarket.functions.php
 *   - Функция сохранения: `xtradbrowmarket_save()` — там же.
 *   - API extrafields:  `cot_import_extrafields()`, `cot_extrafield_unlinkfiles()`,
 *                       `cot_extrafield_movefiles()` — system/extrafields.php
 *   - Таблицы:          `cot_xtradbrowmarket`, `cot_xtradbrowmarket_i18n`
 *
 * Важно: переводы (i18n) в этом файле не обрабатываются, так как массовое
 * редактирование переводов реализуется в отдельном инструменте.
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.marketmassedit.massedit.save.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Aug 21Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
require_once cot_incfile('xtradbrowmarket', 'plug');

if (!empty($ids)) {
    $extrafields = xtradbrowmarket_getExtrafields();
    if (empty($extrafields)) return;

    foreach ($ids as $id) {
        $id = (int)$id;
        $xtra_data = xtradbrowmarket_load($id) ?: [];
        $data = [];
        $changed = false;

        foreach ($extrafields as $exfld) {
            $fname    = $exfld['field_name'];
            $postKey  = 'rxtra_' . $fname;
            $oldValue = $xtra_data[$fname] ?? '';
            $newValue = $oldValue;

            // === НАЧАЛО: Обработка файлов (добавлено) ===
            if ($exfld['field_type'] == 'file') {
                $deleteRequested = isset($_POST['rdel_' . $postKey][$id]) && $_POST['rdel_' . $postKey][$id] == 1;
                $hasNewFile = isset($_FILES[$postKey]['name'][$id]) && $_FILES[$postKey]['error'][$id] !== UPLOAD_ERR_NO_FILE;

                // Удаление старого файла
                if ($deleteRequested && !empty($oldValue)) {
                    cot_extrafield_unlinkfiles($oldValue, $exfld);
                    $data[$fname] = '';
                    $changed = true;
                }

                // Загрузка нового файла
                if ($hasNewFile) {
                    $tmpName = $postKey . '_' . $id . '_tmp';
                    $singleFile = [
                        'name'     => $_FILES[$postKey]['name'][$id],
                        'type'     => $_FILES[$postKey]['type'][$id],
                        'tmp_name' => $_FILES[$postKey]['tmp_name'][$id],
                        'error'    => $_FILES[$postKey]['error'][$id],
                        'size'     => $_FILES[$postKey]['size'][$id],
                    ];
                    $oldFiles = $_FILES;
                    $_FILES[$tmpName] = $singleFile;
                    $newFileValue = cot_import_extrafields($tmpName, $exfld, 'P', $oldValue, 'xtra_');
                    $_FILES = $oldFiles;

                    if (!cot_error_found() && $newFileValue !== null && $newFileValue !== '') {
                        if (!empty($oldValue) && !$deleteRequested) {
                            cot_extrafield_unlinkfiles($oldValue, $exfld);
                        }
                        $data[$fname] = $newFileValue;
                        $changed = true;
                    }
                }

                // Если файл не менялся — сохраняем старое значение
                if (!$deleteRequested && !$hasNewFile) {
                    $data[$fname] = $oldValue;
                }
                continue; // файл обработан, переходим к следующему экстраполю
            }
            // === КОНЕЦ: Обработка файлов ===

            // Обработка остальных типов (было ранее)
            if ($exfld['field_type'] == 'checkbox') {
                // Чекбокс не отправляется, если снят – считаем, что 0
                $newValue = isset($_POST[$postKey][$id]) ? 1 : 0;
            } elseif (isset($_POST[$postKey][$id])) {
                $raw = $_POST[$postKey][$id];

                switch ($exfld['field_type']) {
                    case 'checklistbox':
                        if (is_array($raw)) {
                            unset($raw['nullval']);
                            $newValue = implode(',', $raw);
                        } else {
                            $newValue = '';
                        }
                        break;

                    case 'datetime':
                        if (is_array($raw)) {
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
                        } else {
                            $newValue = (int)$raw;
                        }
                        break;

                    default:
                        $newValue = is_array($raw) ? implode(',', $raw) : trim($raw);
                        break;
                }
            } else {
                // Поле не отправлено — сохраняем прежнее значение
                $newValue = $oldValue;
            }

            $data[$fname] = $newValue;
            if ($newValue != $oldValue) {
                $changed = true;
            }
        }

        if ($changed) {
            xtradbrowmarket_save($id, $data);
        }
    }

    // Перемещаем загруженные файлы после обработки всех записей
    cot_extrafield_movefiles();
}
