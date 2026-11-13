<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.edit.update.done
  [END_COT_EXT]
==================== */

/**
 * Сохранение данных после обновления товара (администратором): плагин xtradbrowmarket
 * Хук market.edit.update.done. Сохраняет значения extrafields в cot_xtradbrowmarket,
 * а также мультиязычные переводы (если включены).
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.market.edit.update.done.php
 *
 * Extrafields Market Custom i18n plugin for Cotonti v1.+, PHP 8.5+, MySQL 8.4
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Nov 13Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.2
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

if (isset($id) && $id > 0) {
    $extrafields = xtradbrowmarket_getExtrafields();
    if (!empty($extrafields)) {
        $xtra_data = xtradbrowmarket_load($id) ?: [];
        $data = [];

        // Определяем, было ли отправлено хоть одно дополнительное поле.
        // Если форма не содержала extrafields (например, редактирование из
        // другого интерфейса) — не трогаем таблицу, чтобы не затереть данные.
        $hasSubmittedExtrafield = false;
        foreach ($extrafields as $exfld) {
            $inputName = 'rxtra_' . $exfld['field_name'];
            if (
                isset($_POST[$inputName]) ||
                isset($_POST['rdel_' . $inputName]) ||
                (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE)
            ) {
                $hasSubmittedExtrafield = true;
                break;
            }
        }

        // 1. Сохраняем основные значения (оригинал)
        foreach ($extrafields as $exfld) {
            $fieldName = $exfld['field_name'];
            $fieldType = $exfld['field_type'] ?? '';
            $inputName = 'rxtra_' . $fieldName;
            $oldValue  = $xtra_data[$fieldName] ?? '';

            if (
                isset($_POST[$inputName]) ||
                isset($_POST['rdel_' . $inputName]) ||
                (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE)
            ) {
                $data[$fieldName] = cot_import_extrafields($inputName, $exfld, 'P', $oldValue, 'xtra_');
            } elseif ($fieldType === 'checkbox' && $hasSubmittedExtrafield) {
                // Checkbox не приходит в POST, если снят.
                // Без этой ветки в $data попадал $oldValue = '' (строка),
                // что вызывало: Incorrect integer value: '' for column 'demo_checkbox'.
                $data[$fieldName] = 0;
            }
            // Иначе — поле не отправлено и это не чекбокс. Колонку не трогаем,
            // чтобы UPDATE сохранил существующее значение (или сработал DEFAULT в INSERT).
        }

        if (!empty($data)) {
            xtradbrowmarket_save($id, $data);
        }

        // === ВАЖНО: перемещаем загруженные файлы в целевую папку ===
        cot_extrafield_movefiles();

        // 2. Мультиязычные переводы (если включены) — без изменений
        if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])) {
            $langDefault = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default'])
                ? Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default']
                : Cot::$cfg['defaultlang'];

            $activeLangs = [];
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_first_use'])
                && !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_first'])) {
                $activeLangs[] = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_first'];
            }
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_second_use'])
                && !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_second'])) {
                $activeLangs[] = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_second'];
            }
            if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_third_use'])
                && !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_third'])) {
                $activeLangs[] = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_third'];
            }

            $i18nAllowedTypes = ['input', 'textarea'];

            foreach ($extrafields as $exfld) {
                if (!in_array($exfld['field_type'], $i18nAllowedTypes)) {
                    continue;
                }
                $fieldName = $exfld['field_name'];
                foreach ($activeLangs as $lang) {
                    if ($lang === $langDefault) continue;

                    $i18nInputName = 'rxtra_' . $fieldName . '_' . $lang;
                    $i18nValue = cot_import($i18nInputName, 'P', 'HTM');
                    if ($i18nValue === null) continue;

                    xtradbrowmarket_i18n_save($id, $fieldName, $lang, $i18nValue);
                }
            }
        }
    }
}
