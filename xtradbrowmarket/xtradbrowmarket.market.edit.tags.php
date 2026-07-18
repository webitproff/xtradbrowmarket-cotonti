<?php
/* ====================
  [BEGIN_COT_EXT]
  Hooks=market.edit.tags
  [END_COT_EXT]
==================== */

/**
 * Вывод полей в форме редактирования товара (администратором): плагин xtradbrowmarket
 * Хук market.edit.tags. Отображает все extrafields с их текущими значениями,
 * а также поля мультиязычных переводов (если включены и тип поля — input/textarea).
 *
 *
 * Filename: plugins/xtradbrowmarket/xtradbrowmarket.market.edit.tags.php
 *
 * Custom Extrafields Market i18n plugin for Cotonti v1.+, PHP 8.4+, MySQL 8.4 
 *
 * ReadMeMore:       https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom 
 * Support:          https://abuyfile.com/ru/forums/cotonti/original/extrafields
 * API Extrafields:  https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php
 *
 * Date: Jul 18, 2026
 * @package xtradbrowmarket
 * @version 3.0.0
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff/xtradbrowmarket-cotonti
 * @license BSD
 */

/* 
 * Форма редактирования страницы (market.edit.tpl) - групповой вывод. не использовать для мультиязычности.
 * 
 * <!-- BEGIN: XTRA_EXTRAFLD -->
 * <div class="form-group">
 *     <label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
 *     {MARKETEDIT_FORM_XTRA_EXTRAFLD}
 * </div>
 * <!-- END: XTRA_EXTRAFLD -->
 * 
 */


defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('xtradbrowmarket', 'plug');

$extrafields = xtradbrowmarket_getExtrafields();

if (!empty($extrafields) && isset($row_item['fieldmrkt_id'])) {
    $xtra_data = xtradbrowmarket_load($row_item['fieldmrkt_id']);
    
    // Проверяем, включена ли мультиязычность
    $i18nEnabled = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use']);
    $activeLangs = [];
    if ($i18nEnabled) {
        $defaultLang = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default'] ?? Cot::$cfg['defaultlang'];
        // Собираем активные дополнительные языки
        $firstCode  = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_first'] ?? '';
        $firstUse   = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_first_use'] ?? 0;
        $secondCode = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_second'] ?? '';
        $secondUse  = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_second_use'] ?? 0;
        $thirdCode  = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_third'] ?? '';
        $thirdUse   = Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_third_use'] ?? 0;

        if ($firstUse && !empty($firstCode) && $firstCode !== $defaultLang) {
            $activeLangs[] = $firstCode;
        }
        if ($secondUse && !empty($secondCode) && $secondCode !== $defaultLang) {
            $activeLangs[] = $secondCode;
        }
        if ($thirdUse && !empty($thirdCode) && $thirdCode !== $defaultLang) {
            $activeLangs[] = $thirdCode;
        }
    }

    // Типы полей, для которых имеет смысл мультиязычный ввод: только произвольный текст
    $i18nAllowedTypes = ['input', 'textarea'];

    foreach ($extrafields as $exfld) {
        $fieldName = 'rxtra_' . $exfld['field_name'];
        $value = $xtra_data[$exfld['field_name']] ?? null;
        $element = cot_build_extrafields($fieldName, $exfld, $value);
        $title = cot_extrafield_title($exfld, 'xtra_');

        // Основные теги (как раньше)
        $t->assign([
            'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name'])         => $element,
            'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name']) . '_TITLE' => $title,
            'MARKETEDIT_FORM_XTRA_EXTRAFLD'                                    => $element,
            'MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE'                              => $title,
        ]);
        $t->parse('MAIN.XTRA_EXTRAFLD');

        // Если мультиязычность активна – добавляем поля переводов ТОЛЬКО для разрешённых типов
        if ($i18nEnabled && !empty($activeLangs) && in_array($exfld['field_type'], $i18nAllowedTypes)) {
            foreach ($activeLangs as $lang) {
                $i18nFieldName = 'rxtra_' . $exfld['field_name'] . '_' . $lang;
                $i18nValue = xtradbrowmarket_i18n_load($row_item['fieldmrkt_id'], $exfld['field_name'], $lang);
                // если оригинальное поле — textarea, то и поле для перевода должно быть многострочным
                if ($exfld['field_type'] === 'textarea') {
                    $i18nElement = cot_textarea($i18nFieldName, $i18nValue, 5, 40, 'class="form-control"');
                } else {
                    $i18nElement = cot_inputbox('text', $i18nFieldName, $i18nValue, 'class="form-control"');
                }
                $i18nTitle = $title . ' (' . strtoupper($lang) . ')';

                $t->assign([
                    'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name']) . '_' . strtoupper($lang) => $i18nElement,
                    'MARKETEDIT_FORM_XTRA_' . strtoupper($exfld['field_name']) . '_' . strtoupper($lang) . '_TITLE' => $i18nTitle,
                ]);
            }
        }
    }
}
