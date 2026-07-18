<?php
/**
 * центральные функции плагина (v3.0.0 с поддержкой i18n)
 *
 * Файл:     plugins/xtradbrowmarket/inc/xtradbrowmarket.functions.php
 * Назначение: предоставляет базовые операции чтения/записи в таблицу `cot_xtradbrowmarket`,
 *            а также инициализирует глобальную переменную таблицы и подключает
 *            языковые файлы и API экстраполей.
 *
 * Важные замечания по архитектуре:
 *   - Таблица `cot_xtradbrowmarket` намеренно создана с первичным ключом `itempagid`.
 *     Это гарантирует, что при вызове `cot_extrafield_add()` для этой таблицы Cotonti НЕ
 *     добавляет префикс к именам создаваемых колонок. Физические имена колонок
 *     точно совпадают с именами экстраполей (`event_name`, `interests` и т.д.).
 *   - Связь с элементом модуля Market осуществляется через значение `itempagid`, которое всегда равно
 *     `fieldmrkt_id` из таблицы `cot_market`. Благодаря ON DELETE CASCADE удаление товара
 *     автоматически удаляет строку из нашей таблицы на уровне БД.
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


defined('COT_CODE') or die('Wrong URL');

// Подключаем языковой файл плагина и стандартный API экстраполей
require_once cot_langfile('xtradbrowmarket', 'plug');
require_once cot_incfile('market', 'module'); 
require_once cot_incfile('extrafields');

// Регистрируем таблицы в реестре Cotonti, чтобы можно было обращаться
// через Cot::$db->xtradbrowmarket и Cot::$db->xtradbrowmarket_i18n
Cot::$db->registerTable('xtradbrowmarket');
Cot::$db->registerTable('xtradbrowmarket_i18n');

/**
 * Возвращает массив зарегистрированных экстраполей для таблицы cot_xtradbrowmarket
 *
 * Данные берутся из глобального реестра Cot::$extrafields, который заполняется
 * системой при загрузке.
 *
 * @return array Ассоциативный массив, где ключ — имя поля, значение — конфигурация поля.
 */
function xtradbrowmarket_getExtrafields()
{
    return Cot::$extrafields[Cot::$db->xtradbrowmarket] ?? [];
}

/**
 * Загружает запись дополнительных полей товара из таблицы cot_xtradbrowmarket
 *
 * @param int $page_id ID страницы из модуля Market (равен `itempagid`)
 * @return array|null Ассоциативный массив всех полей строки или null, если запись не найдена.
 */
function xtradbrowmarket_load($page_id)
{
    $res = Cot::$db->query(
        "SELECT * FROM " . Cot::$db->xtradbrowmarket . " WHERE itempagid = ?",
        [$page_id]
    );
    return $res->fetch();
}

/**
 * Сохраняет (INSERT или UPDATE) запись дополнительных полей товара
 *
 * Логика работы:
 *   1. Проверяет, существует ли уже запись для данного `$page_id`.
 *   2. Если существует — выполняет UPDATE по первичному ключу `itempagid`.
 *   3. Если не существует — выполняет INSERT, вручную задавая значение `itempagid`.
 *
 * Обратите внимание: массив `$data` должен содержать ключи, соответствующие
 * физическим именам колонок в таблице (без префикса). Например:
 * `['phone_extra' => '+123456789', 'interests' => 'IT,Спорт']`.
 *
 * @param int   $page_id ID страницы (будет записан в колонку `itempagid`)
 * @param array $data    Ассоциативный массив значений экстраполей
 */
function xtradbrowmarket_save($page_id, $data)
{
    // Проверяем, есть ли уже запись
    $exists = Cot::$db->query(
        "SELECT COUNT(*) FROM " . Cot::$db->xtradbrowmarket . " WHERE itempagid = ?",
        [$page_id]
    )->fetchColumn() > 0;

    if ($exists) {
        // Обновляем существующую запись
        Cot::$db->update(Cot::$db->xtradbrowmarket, $data, "itempagid = ?", [$page_id]);
    } else {
        // Вставляем новую запись, обязательно указываем itempagid
        $data['itempagid'] = $page_id;
        Cot::$db->insert(Cot::$db->xtradbrowmarket, $data);
    }
}


/**
 * Загружает перевод значения экстраполя для указанного языка
 *
 * @param int    $page_id   ID страницы (itempagid)
 * @param string $fieldName Имя экстраполя
 * @param string $lang      Двухбуквенный код языка
 * @return string|null      Перевод или null
 */
function xtradbrowmarket_i18n_load($page_id, $fieldName, $lang)
{
    return Cot::$db->query(
        "SELECT value FROM " . Cot::$db->xtradbrowmarket_i18n . " WHERE itempagid = ? AND field_name = ? AND lang = ?",
        [$page_id, $fieldName, $lang]
    )->fetchColumn() ?: null;
}

/**
 * Сохраняет или удаляет перевод значения экстраполя для конкретного языка
 * Если $value === null или '', запись удаляется.
 *
 * @param int    $page_id   ID страницы
 * @param string $fieldName Имя экстраполя
 * @param string $lang      Двухбуквенный код языка
 * @param mixed  $value     Значение перевода
 */
function xtradbrowmarket_i18n_save($page_id, $fieldName, $lang, $value)
{
    if ($value === null || $value === '') {
        Cot::$db->delete(Cot::$db->xtradbrowmarket_i18n, "itempagid = ? AND field_name = ? AND lang = ?", [$page_id, $fieldName, $lang]);
    } else {
        Cot::$db->query(
            "INSERT INTO " . Cot::$db->xtradbrowmarket_i18n . " (itempagid, field_name, lang, value)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE value = VALUES(value)",
            [$page_id, $fieldName, $lang, $value]
        );
    }
}


/**
 * Возвращает значение экстраполя с учётом мультиязычного перевода
 *
 * Если в настройках плагина включена мультиязычность (`xtradbrowmarket_i18n_use`) и
 * текущий язык пользователя отличается от основного языка сайта, пытается найти перевод
 * в таблице `cot_xtradbrowmarket_i18n`. Если перевод не найден, возвращается исходное значение.
 *
 * Если текущий язык совпадает с основным, но оригинальное значение пустое,
 * функция пытается вернуть первый доступный перевод из активных дополнительных языков
 * (первый непустой) в качестве запасного варианта. Это позволяет избежать
 * пустых полей при просмотре на основном языке, если заполнены переводы.
 *
 * @param int    $page_id       ID страницы товара
 * @param string $fieldName     Имя экстраполя
 * @param mixed  $originalValue Исходное значение (из основной таблицы)
 * @return mixed Значение на нужном языке или оригинал
 */
function xtradbrowmarket_i18n_get_value($page_id, $fieldName, $originalValue)
{
    // Выходим сразу, если мультиязычность отключена
    if (empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_use'])) {
        return $originalValue;
    }

    // Определяем основной язык (тот, для которого переводы не хранятся)
    $defaultLang = !empty(Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default'])
        ? Cot::$cfg['plugin']['xtradbrowmarket']['xtradbrowmarket_i18n_lang_code_default']
        : Cot::$cfg['defaultlang'];

    // Язык текущего посетителя
    $currentLang = Cot::$usr['lang'] ?? $defaultLang;

    // Если язык НЕ основной — пытаемся загрузить прямой перевод
    if ($currentLang !== $defaultLang) {
        $translated = xtradbrowmarket_i18n_load($page_id, $fieldName, $currentLang);
        return $translated !== null ? $translated : $originalValue;
    }

    // Основной язык, но основное значение не пустое — возвращаем его
    if ($originalValue !== null && $originalValue !== '') {
        return $originalValue;
    }

    // Основной язык и оригинал пуст — ищем первый непустой перевод среди активных языков
    $activeLangs = [];
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

    foreach ($activeLangs as $lang) {
        $fallback = xtradbrowmarket_i18n_load($page_id, $fieldName, $lang);
        if ($fallback !== null && $fallback !== '') {
            return $fallback;
        }
    }

    // Ничего не нашли — возвращаем оригинал (пустую строку)
    return $originalValue;
}
