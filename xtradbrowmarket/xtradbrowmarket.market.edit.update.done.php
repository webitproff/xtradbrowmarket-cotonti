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
 * Date: Aug 20Th, 2026
 * @package xtradbrowmarket
 * @version 4.1.1
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

        // 1. Сохраняем основные значения (оригинал) – с проверкой наличия в запросе
        foreach ($extrafields as $exfld) {
            $fieldName = $exfld['field_name'];
            $inputName = 'rxtra_' . $fieldName;
            $oldValue = $xtra_data[$fieldName] ?? '';
            // Проверяем, был ли отправлен соответствующий элемент формы
			if (
				isset($_POST[$inputName]) ||
				isset($_POST['rdel_' . $inputName]) ||
				(isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE)
			) {
                $data[$fieldName] = cot_import_extrafields($inputName, $exfld, 'P', $oldValue, 'xtra_');
            } else {
                $data[$fieldName] = $oldValue; // поле не отправлено — сохраняем прежнее значение
            }
        }
        xtradbrowmarket_save($id, $data);
        cot_extrafield_movefiles();

        // 2. Мультиязычные переводы (если включены)
        if (!empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])) {
            $langDefault = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default'])
                ? Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default']
                : Cot::$cfg['defaultlang'];

            // Собираем массив активных дополнительных языков
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

            // Типы полей, для которых разрешено сохранять переводы
            $i18nAllowedTypes = ['input', 'textarea'];

            // Для каждого поля обрабатываем переводы, только если тип подходящий
            foreach ($extrafields as $exfld) {
                if (!in_array($exfld['field_type'], $i18nAllowedTypes)) {
                    continue; // пропускаем не-текстовые поля
                }
                $fieldName = $exfld['field_name'];
                foreach ($activeLangs as $lang) {
                    // Пропускаем язык по умолчанию – его значение уже в основной таблице
                    if ($lang === $langDefault) continue;

                    $i18nInputName = 'rxtra_' . $fieldName . '_' . $lang;
                    $i18nValue = cot_import($i18nInputName, 'P', 'HTM');
                    if ($i18nValue === null) continue; // поле не передавалось

                    xtradbrowmarket_i18n_save($id, $fieldName, $lang, $i18nValue);
                }
            }
        }
    }
}
