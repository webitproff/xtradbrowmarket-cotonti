<?php
/**
 * Ukrainian Language File for xtradbrowmarket Plugin with i18n support
 *
 * Filename: plugins/xtradbrowmarket/lang/xtradbrowmarket.uk.lang.php
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

defined('COT_CODE') or die('Wrong URL.');

// використовуємо глобальну змінну $db_x, яка визначена в datas/config.php
// і доступна абсолютно завжди, ще до завантаження будь-яких плагінів
// $db_x — це не застаріла глобальна змінна, а маловідома,
// ключова змінна для, наприклад, таких завдань, як коректне посилання.
// задається в конфізі datas/config.php і пробрасується через
// Cot::init() в system/common.php використовуючи клас Cot з Cot.php.
// Вона працює і до встановлення плагіну, і після.
// У Cotonti немає інших надійних способів отримати префікс таблиць на етапі завантаження мовного файлу.
// Cot::$db_x і Cot::$db->tablePrefix не є частиною публічного API і не гарантують доступність у потрібний момент.
// Змінна $db_x, визначена в datas/config.php і доступна через global, — це єдиний коректний і документований спосіб.
// Тому вираз із $db_x є правильним і єдино вірним для даної ситуації.

global $db_x;

$main_url = rtrim(Cot::$cfg['mainurl'], '/');
$url = $main_url . '/' . cot_url('admin', 'm=extrafields&n=' . $db_x . 'xtradbrowmarket', '', true);

$L['xtradbrowmarket'] = 'Extrafields Market Custom i18n';

// ========================
// НАЛАШТУВАННЯ ПЛАГІНА (АДМІНКА)
// ========================
$L['cfg_perpage']          = 'Товарів у списку/таблиці';
$L['cfg_perpage_hint']     = 'Елементів на сторінці в списку масового редагування';

$L['cfg_xtradbrowmarket_i18n_use'] = 'Активувати та використовувати мультимовність полів';
$L['cfg_xtradbrowmarket_i18n_use_hint'] = 'Вмикає підтримку перекладів значень екстраполів. При вимкненні всі переклади зберігаються, але не відображаються.';

$L['cfg_xtradbrowmarket_i18n_lang_code_default'] = 'Код основної мови сайту';
$L['cfg_xtradbrowmarket_i18n_lang_code_default_hint'] = 'Повинен збігатися з глобальним налаштуванням <code>$cfg[\'defaultlang\']</code>. Значення для цієї мови зберігаються в основній таблиці і вважаються оригіналом.';

$L['cfg_xtradbrowmarket_i18n_lang_code_first'] = 'Код першої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use'] = 'Використовувати першу додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_first_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу на цю мову.';

$L['cfg_xtradbrowmarket_i18n_lang_code_second'] = 'Код другої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use'] = 'Використовувати другу додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_second_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу на цю мову.';

$L['cfg_xtradbrowmarket_i18n_lang_code_third'] = 'Код третьої додаткової мови';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use'] = 'Використовувати третю додаткову мову';
$L['cfg_xtradbrowmarket_i18n_lang_code_third_use_hint'] = 'Якщо активно, у формах редагування з\'являться поля для введення перекладу на цю мову.';

$L['cfg_xtradbrowmarket_showallitems'] = 'Показувати всі товари в адмінці';
$L['cfg_xtradbrowmarket_showallitems_hint'] = 'Якщо увімкнено, у таблицях редагування відображатимуться всі товари, навіть ті, для яких ще не створено записи додаткових полів.';
/**
 * Plugin Info
 */
$L['info_name'] = 'Extrafields Market Custom i18n';

$L['info_desc'] = 'Плагін додає екстраполя для модуля "Market PRO v.5" у власну таблицю БД з підтримкою мультимовності.';

$L['info_notes'] = 
    'Новачкам ' .
    '<a href="https://abuyfile.com/ru/forums/cotonti/original/extrafields" target="_blank">' .
    '<abbr title="Вступ. Опис і принципи роботи екстраполів у Cotonti" class="initialism">' .
    '<strong>обов\'язково читати розділ форуму про API ExtraFields</strong></abbr></a>. <br>' . 
    'Після встановлення плагіна відкрити екстраполя плагіна ' .
    '<a href="' . $url . '" target="_blank">' .
    '<strong> ' . $L['xtradbrowmarket'] . ' </strong></a>.';

// ========================
// TITLES AND DESCRIPTIONS (same values, pulled by other keys)
// ========================
$L['xtradbrowmarket_title'] = $L['info_name'];
$L['xtradbrowmarket_desc']  = $L['info_desc'];
$L['xtradbrowmarket_name']  = $L['info_name'];

// ----------------------------------------------------------------
// Адмінка плагіна
// ----------------------------------------------------------------
$L['xtradbrowmarket_tab_stats'] = 'Статистика';
$L['xtradbrowmarket_tab_edit'] = 'Редагувати';
$L['xtradbrowmarket_tab_i18n'] = 'Редагувати + Переклади';
$L['xtradbrowmarket_stats_total_items'] = 'Усього товарів';
$L['xtradbrowmarket_stats_xtra_rows'] = 'Записів у xtradbrowmarket';
$L['xtradbrowmarket_stats_filled'] = 'Заповнених записів';
$L['xtradbrowmarket_extrafields_info'] = 'Параметри екстраполів';
$L['xtradbrowmarket_field_name'] = 'Ім\'я поля';
$L['xtradbrowmarket_field_type'] = 'Тип';
$L['xtradbrowmarket_field_description'] = 'Опис';
$L['xtradbrowmarket_field_variants'] = 'Варіанти';
$L['xtradbrowmarket_field_params'] = 'Параметри';
$L['xtradbrowmarket_field_default'] = 'За замовчуванням';
$L['xtradbrowmarket_field_required'] = 'Обов\'язкове';
$L['xtradbrowmarket_field_enabled'] = 'Увімкнено';
$L['xtradbrowmarket_market_title'] = 'Назва товару';
$L['xtradbrowmarket_no_extrafields'] = 'Немає зареєстрованих екстраполів';
$L['xtradbrowmarket_no_records'] = 'Немає записів';
$L['xtradbrowmarket_saved'] = 'Зміни збережено';
$L['xtradbrowmarket_i18n_active'] = 'Мультимовність увімкнена';
$L['xtradbrowmarket_i18n_disabled'] = 'Мультимовність вимкнена';
$L['xtradbrowmarket_search_sq'] = 'Пошук за назвою/текстом';
$L['xtradbrowmarket_search_cat'] = 'Категорія';
$L['xtradbrowmarket_filter_id'] = 'ID товару';
$L['xtradbrowmarket_filter_state'] = 'Статус';
$L['xtradbrowmarket_search_btn'] = 'Фільтр';
$L['xtradbrowmarket_search_reset'] = 'Скидання';
$L['xtradbrowmarket_search_in_title'] = 'Назва';
$L['xtradbrowmarket_search_in_full'] = 'Скрізь (назва+текст)';
$L['xtradbrowmarket_search_in_pcod'] = 'Код (артикул)';
$L['xtradbrowmarket_search_result_msg'] = 'Знайдено %s за запитом %s';
$L['xtradbrowmarket_search_result_none'] = 'Нічого не знайдено за запитом %s';
$L['xtradbrowmarket_search_declen'] = 'записів,запис,записи';
$L['xtradbrowmarket_updated'] = 'Оновлено записів: %d';

// ----------------------------------------------------------------
// TPL-заголовки в деяких місцях виведення
// ----------------------------------------------------------------
$L['xtradbrowmarket_edittpl_dynamic_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Динамічний вивід</span>'; 
$L['xtradbrowmarket_pagetpl_custom_title'] = '<span class="fw-semibold text-danger" style="letter-spacing: 1px;">Екстраполя <code>xtradbrowmarket</code>. Індивідуальний вивід</span> в картці товару';
$L['xtradbrowmarket_pagetpl_custom_desc'] = 'Адміністратор, для картки товару рекомендується використовувати саме індивідуальний вивід додаткових полів для їх гнучкої кастомізації';

// ----------------------------------------------------------------
// Локалізація заголовків (_TITLE) для демонстраційних полів
// ----------------------------------------------------------------
$L['xtra_event_name_title'] = 'Назва події';
$L['xtra_event_description_title'] = 'Опис події';
$L['xtra_event_start_title'] = 'Початок події';
$L['xtra_event_ticketprice_title'] = 'Вартість квитка';
$L['xtra_event_seson_title'] = 'Сезон';
$L['xtra_demo_int_title'] = 'Приклад цілого числа';
$L['xtra_demo_double_title'] = 'Приклад числа з плаваючою точкою';
$L['xtra_demo_select_title'] = 'Приклад випадаючого списку';
$L['xtra_demo_radio_title'] = 'Приклад радіокнопок';
$L['xtra_demo_datetime_title'] = 'Приклад дати та часу';
$L['xtra_demo_file_title'] = 'Приклад завантаження файлу';
$L['xtra_demo_country_title'] = 'Приклад вибору країни';
$L['xtra_demo_range_title'] = 'Приклад діапазону чисел';
$L['xtra_demo_checklistbox_title'] = 'Приклад чекбоксів із множинним вибором';

// ----------------------------------------------------------------
// Локалізація значень для select, radio, checklistbox
// ----------------------------------------------------------------
// Сезон
$L['event_seson_unknown'] = 'Невідомо';
$L['event_seson_winter'] = 'Зима';
$L['event_seson_summer'] = 'Літо';
$L['event_seson_autumn'] = 'Осінь';
$L['event_seson_spring'] = 'Весна';

// Демонстраційний select
$L['demo_select_Option 1'] = 'Варіант 1';
$L['demo_select_Option 2'] = 'Варіант 2';
$L['demo_select_Option 3'] = 'Варіант 3';

// Демонстраційні radio
$L['demo_radio_Yes'] = 'Так';
$L['demo_radio_No'] = 'Ні';

// Демонстраційний checklistbox (українські значення)
$L['demo_checklistbox_option1'] = 'Опція 1';
$L['demo_checklistbox_option2'] = 'Опція 2';
$L['demo_checklistbox_option3'] = 'Опція 3';