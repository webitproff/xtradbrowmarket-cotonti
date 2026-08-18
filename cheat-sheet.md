
# Полное руководство по выводу тегов плагина xtradbrowmarket в шаблоны Cotonti

**Оглавление**

1. [Введение](#introduction)  
2. [Основы работы шаблонов Cotonti](#cotonti-templates-basics)  
   2.1. [Как Cotonti обрабатывает шаблоны](#template-engine)  
   2.2. [Синтаксис тегов и блоков](#template-syntax)  
   2.3. [Глобальные переменные и условия](#template-conditions)  
3. [Обзор плагина xtradbrowmarket](#plugin-overview)  
   3.1. [Назначение плагина](#plugin-purpose)  
   3.2. [Демонстрационные поля](#demo-fields)  
   3.3. [Таблицы базы данных](#database-tables)  
4. [Файлы хуков, отвечающие за вывод тегов](#hook-files-overview)  
5. [Хук `market.edit.tags` — форма редактирования товара](#hook-market-edit-tags)  
   5.1. [Когда вызывается и где используется](#edit-when-where)  
   5.2. [Доступные теги](#edit-available-tags)  
   5.3. [Индивидуальные теги каждого поля](#edit-individual-tags)  
   5.4. [Групповой блок `XTRA_EXTRAFLD`](#edit-group-block)  
   5.5. [Теги мультиязычных переводов](#edit-i18n-tags)  
   5.6. [Особенности типов полей в форме](#edit-field-types)  
   5.7. [Пример готового кода для `market.edit.tpl`](#edit-example-code)  
6. [Хук `market.tags` — страница просмотра товара](#hook-market-tags)  
   6.1. [Когда вызывается и где используется](#view-when-where)  
   6.2. [Доступные теги](#view-available-tags)  
   6.3. [Индивидуальные теги каждого поля](#view-individual-tags)  
   6.4. [Групповой блок `XTRA_EXTRAFLD`](#view-group-block)  
   6.5. [Особенности поля «страна»](#view-country)  
   6.6. [Мультиязычность на странице товара](#view-i18n)  
   6.7. [Пример готового кода для `market.tpl`](#view-example-code)  
7. [Хук `markettags.main` — общий массив тегов `cot_generate_markettags`](#hook-markettags-main)  
   7.1. [Когда вызывается](#main-when)  
   7.2. [Как теги попадают в другие плагины и шаблоны](#main-how-tags-work)  
   7.3. [Префикс `XTRADBROWMARKET_`](#main-prefix)  
   7.4. [Пример использования в произвольном шаблоне](#main-example)  
   7.5. [Особенности мультиязычности и типов](#main-i18n)  
8. [Хук `market.list.loop` — вывод в списке товаров](#hook-market-list-loop)  
   8.1. [Когда вызывается и где используется](#list-when-where)  
   8.2. [Доступные теги](#list-available-tags)  
   8.3. [Индивидуальные теги каждого поля](#list-individual-tags)  
   8.4. [Групповой блок внутри списка](#list-group-block)  
   8.5. [Пример готового кода для `market.list.tpl`](#list-example-code)  
9. [Таблицы соответствий](#tables)  
   9.1. [Таблица 1. Префиксы тегов по файлам хуков](#table-prefixes)  
   9.2. [Таблица 2. Типы полей и их отображение](#table-field-types)  
   9.3. [Таблица 3. Суффиксы `_TITLE`, `_VALUE`, `_NAME`](#table-suffixes)  
10. [Обработка пустых значений и безопасность](#empty-values-security)  
11. [Заключение](#conclusion)

---

<a name="introduction"></a>
## 1. Введение

Плагин **xtradbrowmarket** предназначен для добавления дополнительных полей (экстраполей) к товарам модуля **Market PRO** в системе **Cotonti CMF**. Плагин создаёт собственную таблицу в базе данных, регистрирует в ней набор демонстрационных полей и предоставляет удобные хуки для вывода этих полей в шаблонах.

Данное руководство объясняет, **как правильно выводить теги** этих дополнительных полей в различных частях сайта: форма редактирования товара, страница товара, список товаров и любые другие шаблоны, использующие общий массив тегов.

Руководство рассчитано на **начинающих разработчиков**, которые уже имеют базовое представление о системе Cotonti и её шаблонизаторе. Если вы впервые сталкиваетесь с Cotonti, рекомендуем сначала ознакомиться с официальной документацией.

Все примеры в данном руководстве основаны на **демонстрационных полях**, которые устанавливаются по умолчанию. Вы можете адаптировать их под свои собственные поля.

---

<a name="cotonti-templates-basics"></a>
## 2. Основы работы шаблонов Cotonti

Прежде чем переходить к конкретным тегам плагина, необходимо понять, как Cotonti обрабатывает шаблоны и как устроен механизм тегов.

<a name="template-engine"></a>
### 2.1. Как Cotonti обрабатывает шаблоны

Cotonti использует собственный шаблонизатор **XTemplate**, который работает на основе **блоков** и **тегов**. Шаблоны обычно находятся в папках:

- `themes/ваша_тема/modules/market/` — шаблоны модуля Market;
- `themes/ваша_тема/plug/xtradbrowmarket/` — шаблоны плагина (если имеются).

Основной шаблон модуля Market называется `market.tpl`, форма редактирования — `market.edit.tpl`, список товаров — `market.list.tpl`.

Когда Cotonti обрабатывает запрос, он загружает соответствующий шаблон, компилирует его в объекты PHP и затем подставляет значения тегов.

<a name="template-syntax"></a>
### 2.2. Синтаксис тегов и блоков

- **Тег** записывается в фигурных скобках: `{ИМЯ_ТЕГА}`.
- **Блок** — это повторяющаяся секция, ограниченная комментариями `<!-- BEGIN: ИМЯ_БЛОКА -->` и `<!-- END: ИМЯ_БЛОКА -->`.
- **Условие** записывается как `<!-- IF выражение --> ... <!-- ENDIF -->` (или с `<!-- ELSE -->`).
- Внутри условий можно использовать операторы сравнения: `==`, `!=`, `>`, `<`, `>=`, `<=`, `AND`, `OR`, `!`.

Пример:

```html
<!-- IF {MY_VARIABLE} -->
    Переменная существует и не пуста.
<!-- ELSE -->
    Переменная пуста или не определена.
<!-- ENDIF -->
```

Для проверки значения тега можно использовать просто `<!-- IF {MY_TAG} -->` — это вернёт `true`, если значение не равно `false`, `null`, `0` или пустой строке `''`.

Однако **важно**: после обработки некоторыми функциями (например, форматирование даты) пустое значение может превратиться в непустую строку, поэтому для таких полей рекомендуется проверять **сырое значение** с суффиксом `_VALUE`.

<a name="template-conditions"></a>
### 2.3. Глобальные переменные и условия

В шаблонах Cotonti доступны глобальные переменные через префикс `PHP.`:

- `{PHP.L.Ключ}` — языковая строка.
- `{PHP.cfg.Ключ}` — значение конфигурации.
- `{PHP.usr.Поле}` — данные текущего пользователя.
- `{PHP|cot_plugin_active('имя')}` — проверка активности плагина (возвращает `true` или `false`).

Пример проверки, активен ли плагин:

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
    ... вывод полей ...
<!-- ENDIF -->
```

---

<a name="plugin-overview"></a>
## 3. Обзор плагина xtradbrowmarket

<a name="plugin-purpose"></a>
### 3.1. Назначение плагина

Плагин добавляет к товарам модуля Market набор **дополнительных полей** (extrafields). Эти поля хранятся в отдельной таблице `cot_xtradbrowmarket`, связанной с таблицей товаров через внешний ключ `itempagid`. Основная цель — демонстрация работы с API экстраполей Cotonti и предоставление готового функционала с поддержкой мультиязычности.

<a name="demo-fields"></a>
### 3.2. Демонстрационные поля

При установке плагина создаются следующие 15 полей:

| Имя поля в БД | Тип | Описание |
|---------------|------|----------|
| `event_name` | `input` | Название события |
| `event_description` | `textarea` | Описание события |
| `event_start` | `datetime` | Начало события |
| `event_ticketprice` | `double` | Стоимость билета |
| `event_seson` | `select` | Сезон |
| `demo_int` | `inputint` | Пример целого числа |
| `demo_double` | `double` | Пример числа с плавающей точкой |
| `demo_select` | `select` | Пример выпадающего списка |
| `demo_checkbox` | `checkbox` | Пример флажка |
| `demo_radio` | `radio` | Пример радиокнопок |
| `demo_datetime` | `datetime` | Пример даты и времени |
| `demo_file` | `file` | Пример загрузки файла |
| `demo_country` | `country` | Пример выбора страны |
| `demo_range` | `range` | Пример диапазона чисел |
| `demo_checklistbox` | `checklistbox` | Пример чекбоксов с множественным выбором |

Эти имена используются при формировании тегов: они преобразуются в **верхний регистр** и дополняются префиксами и суффиксами.

<a name="database-tables"></a>
### 3.3. Таблицы базы данных

- `cot_xtradbrowmarket` — основная таблица значений полей. Первичный ключ `itempagid` соответствует `fieldmrkt_id` из таблицы `cot_market`.
- `cot_xtradbrowmarket_i18n` — таблица переводов значений для мультиязычных полей (обычно только `input` и `textarea`).

---

<a name="hook-files-overview"></a>
## 4. Файлы хуков, отвечающие за вывод тегов

Плагин использует несколько файлов, подключённых к определённым хукам системы. Каждый файл отвечает за свой контекст вывода:

| Файл хука | Хук в Cotonti | Назначение |
|-----------|---------------|------------|
| `xtradbrowmarket.market.edit.tags.php` | `market.edit.tags` | Вывод полей в форме **редактирования/добавления** товара (шаблон `market.edit.tpl`) |
| `xtradbrowmarket.market.tags.php` | `market.tags` | Вывод полей на **странице просмотра** товара (шаблон `market.tpl`) |
| `xtradbrowmarket.markettags.php` | `markettags.main` | Добавление тегов в **общий массив** функции `cot_generate_markettags()` (используется во многих плагинах, виджетах, корзине, SEO и т.д.) |
| `xtradbrowmarket.market.list.loop.php` | `market.list.loop` | Вывод полей в **списке товаров** (шаблон `market.list.tpl`) внутри цикла |

Каждый из этих файлов формирует набор тегов с определённым префиксом. Рассмотрим их подробно.

---

<a name="hook-market-edit-tags"></a>
## 5. Хук `market.edit.tags` — форма редактирования товара

<a name="edit-when-where"></a>
### 5.1. Когда вызывается и где используется

Файл `xtradbrowmarket.market.edit.tags.php` подключается к хуку `market.edit.tags`. Этот хук срабатывает во время построения формы **добавления/редактирования** товара в модуле Market (шаблон `market.edit.tpl`).

Все значения экстраполей, которые уже сохранены для редактируемого товара, подставляются в поля формы автоматически.

<a name="edit-available-tags"></a>
### 5.2. Доступные теги

В форме редактирования доступны **два типа тегов**:

1. **Индивидуальные теги** для каждого поля — их можно использовать в любом месте шаблона.
2. **Групповой блок** `XTRA_EXTRAFLD` — позволяет вывести все поля циклом без перечисления каждого имени вручную.

Кроме того, если включена мультиязычность, для текстовых полей (`input` и `textarea`) доступны теги переводов.

<a name="edit-individual-tags"></a>
### 5.3. Индивидуальные теги каждого поля

Для каждого зарегистрированного экстраполя доступны следующие теги (имя поля в верхнем регистре):

- `{MARKETEDIT_FORM_XTRA_<ИМЯ_ПОЛЯ>}` — HTML-код поля (например, `<input type="text" ...>` или `<textarea>...`).
- `{MARKETEDIT_FORM_XTRA_<ИМЯ_ПОЛЯ>_TITLE}` — заголовок поля (локализованное описание).

**Пример для поля `event_name`:**

```html
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EVENT_NAME_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME}
</div>
```

**Список всех индивидуальных тегов для демо-полей:**

| Поле | Тег поля | Тег заголовка |
|------|----------|----------------|
| `event_name` | `{MARKETEDIT_FORM_XTRA_EVENT_NAME}` | `{MARKETEDIT_FORM_XTRA_EVENT_NAME_TITLE}` |
| `event_description` | `{MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION}` | `{MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION_TITLE}` |
| `event_start` | `{MARKETEDIT_FORM_XTRA_EVENT_START}` | `{MARKETEDIT_FORM_XTRA_EVENT_START_TITLE}` |
| `event_ticketprice` | `{MARKETEDIT_FORM_XTRA_EVENT_TICKETPRICE}` | `{MARKETEDIT_FORM_XTRA_EVENT_TICKETPRICE_TITLE}` |
| `event_seson` | `{MARKETEDIT_FORM_XTRA_EVENT_SESON}` | `{MARKETEDIT_FORM_XTRA_EVENT_SESON_TITLE}` |
| `demo_int` | `{MARKETEDIT_FORM_XTRA_DEMO_INT}` | `{MARKETEDIT_FORM_XTRA_DEMO_INT_TITLE}` |
| `demo_double` | `{MARKETEDIT_FORM_XTRA_DEMO_DOUBLE}` | `{MARKETEDIT_FORM_XTRA_DEMO_DOUBLE_TITLE}` |
| `demo_select` | `{MARKETEDIT_FORM_XTRA_DEMO_SELECT}` | `{MARKETEDIT_FORM_XTRA_DEMO_SELECT_TITLE}` |
| `demo_checkbox` | `{MARKETEDIT_FORM_XTRA_DEMO_CHECKBOX}` | `{MARKETEDIT_FORM_XTRA_DEMO_CHECKBOX_TITLE}` |
| `demo_radio` | `{MARKETEDIT_FORM_XTRA_DEMO_RADIO}` | `{MARKETEDIT_FORM_XTRA_DEMO_RADIO_TITLE}` |
| `demo_datetime` | `{MARKETEDIT_FORM_XTRA_DEMO_DATETIME}` | `{MARKETEDIT_FORM_XTRA_DEMO_DATETIME_TITLE}` |
| `demo_file` | `{MARKETEDIT_FORM_XTRA_DEMO_FILE}` | `{MARKETEDIT_FORM_XTRA_DEMO_FILE_TITLE}` |
| `demo_country` | `{MARKETEDIT_FORM_XTRA_DEMO_COUNTRY}` | `{MARKETEDIT_FORM_XTRA_DEMO_COUNTRY_TITLE}` |
| `demo_range` | `{MARKETEDIT_FORM_XTRA_DEMO_RANGE}` | `{MARKETEDIT_FORM_XTRA_DEMO_RANGE_TITLE}` |
| `demo_checklistbox` | `{MARKETEDIT_FORM_XTRA_DEMO_CHECKLISTBOX}` | `{MARKETEDIT_FORM_XTRA_DEMO_CHECKLISTBOX_TITLE}` |

<a name="edit-group-block"></a>
### 5.4. Групповой блок `XTRA_EXTRAFLD`

Чтобы не перечислять все 15 полей вручную, можно использовать групповой цикл. Файл хука на каждой итерации присваивает два универсальных тега:

- `{MARKETEDIT_FORM_XTRA_EXTRAFLD}` — HTML-код текущего поля;
- `{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}` — заголовок текущего поля.

В шаблоне этот цикл оформляется так:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EXTRAFLD}
</div>
<!-- END: XTRA_EXTRAFLD -->
```

При этом блок повторится столько раз, сколько зарегистрировано полей, и для каждого поля будут подставлены свои значения.

<a name="edit-i18n-tags"></a>
### 5.5. Теги мультиязычных переводов

Если в настройках плагина включена мультиязычность (`xtradbrowmarket_i18n_use = 1`) и активны дополнительные языки, то **только для полей типов `input` и `textarea`** генерируются дополнительные теги переводов.

Для каждого активного дополнительного языка (например, `en`, `ua`, `pl`) создаются теги:

- `{MARKETEDIT_FORM_XTRA_<ИМЯ_ПОЛЯ>_<КОД_ЯЗЫКА_ВЕРХНИМ_РЕГИСТРОМ>}` — поле ввода перевода;
- `{MARKETEDIT_FORM_XTRA_<ИМЯ_ПОЛЯ>_<КОД_ЯЗЫКА_ВЕРХНИМ_РЕГИСТРОМ>_TITLE}` — заголовок с указанием языка.

**Пример для поля `event_name` и языка `en`:**

```html
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EVENT_NAME_EN_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME_EN}
</div>
```

Для поля `event_description` (textarea) тоже будут созданы подобные теги, но HTML-поле будет многострочным.

**Важно:** для всех остальных типов полей (`select`, `checkbox`, `datetime` и т.д.) переводы **не создаются** в форме редактирования, поскольку их значения не предполагают ввода произвольного текста.

<a name="edit-field-types"></a>
### 5.6. Особенности типов полей в форме

Каждый тип поля генерирует разный HTML-код, поэтому при ручной вёрстке формы нужно учитывать, как именно выглядит поле.

- **`input`** — простое текстовое поле `<input type="text" class="form-control" ...>`.
- **`textarea`** — многострочное поле `<textarea class="form-control" ...></textarea>`.
- **`datetime`** — набор селектов (день, месяц, год, час, минуты) внутри блока `<div class="row g-2">`.
- **`select`** — выпадающий список `<select class="form-select">...`.
- **`checkbox`** — флажок `<input type="checkbox" ...>`.
- **`radio`** — группа радиокнопок, каждая с подписью.
- **`file`** — поле загрузки файла с чекбоксом удаления.
- **`country`** — выпадающий список стран.
- **`range`** — выпадающий список чисел (реализован через `select`).
- **`checklistbox`** — несколько чекбоксов для множественного выбора.

Поскольку HTML-разметка уже определена в настройках экстраполей, в шаблоне обычно достаточно обернуть готовый тег в контейнер с `label`.

<a name="edit-example-code"></a>
### 5.7. Пример готового кода для `market.edit.tpl`

Ниже приведён полный фрагмент, который можно вставить в шаблон `market.edit.tpl` (обычно внутри формы после основных полей и до кнопок отправки).

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->

<!-- Индивидуальные поля (перечислены все демо-поля) -->
<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_NAME} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_NAME_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_START} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_START_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_START}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_TICKETPRICE} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_TICKETPRICE_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_TICKETPRICE}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_SESON} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_SESON_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_SESON}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_INT} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_INT_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_INT}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_DOUBLE} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_DOUBLE_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_DOUBLE}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_SELECT} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_SELECT_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_SELECT}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_CHECKBOX} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_CHECKBOX_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_CHECKBOX}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_RADIO} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_RADIO_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_RADIO}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_DATETIME} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_DATETIME_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_DATETIME}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_FILE} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_FILE_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_FILE}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_COUNTRY} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_COUNTRY_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_COUNTRY}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_RANGE} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_RANGE_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_RANGE}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_DEMO_CHECKLISTBOX} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_DEMO_CHECKLISTBOX_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_DEMO_CHECKLISTBOX}
</div>
<!-- ENDIF -->

<!-- Мультиязычные поля (для input/textarea) -->
<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_NAME_EN} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_NAME_EN_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME_EN}
</div>
<!-- ENDIF -->

<!-- IF {MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION_EN} -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION_EN_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_DESCRIPTION_EN}
</div>
<!-- ENDIF -->

<!-- ENDIF -->
```

Или можно использовать групповой блок вместо индивидуальных:

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="mb-3">
    <label class="form-label">{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EXTRAFLD}
</div>
<!-- END: XTRA_EXTRAFLD -->
<!-- ENDIF -->
```

---

<a name="hook-market-tags"></a>
## 6. Хук `market.tags` — страница просмотра товара

<a name="view-when-where"></a>
### 6.1. Когда вызывается и где используется

Файл `xtradbrowmarket.market.tags.php` подключается к хуку `market.tags`. Этот хук срабатывает на **странице просмотра отдельного товара** (шаблон `market.tpl`).

Здесь значения экстраполей выводятся в **форматированном виде**, то есть с применением функций отображения (`cot_build_extrafields_data`), что обеспечивает корректный вывод дат, локализованных значений списков и т.д.

<a name="view-available-tags"></a>
### 6.2. Доступные теги

На странице товара для каждого поля доступны следующие индивидуальные теги:

- `{MARKET_XTRA_<ИМЯ_ПОЛЯ>}` — отформатированное значение для вывода.
- `{MARKET_XTRA_<ИМЯ_ПОЛЯ>_TITLE}` — заголовок поля.
- `{MARKET_XTRA_<ИМЯ_ПОЛЯ>_VALUE}` — **сырое** (неформатированное) значение, полезное для проверок в условиях.
- Если поле имеет тип `country`, дополнительно доступен тег `{MARKET_XTRA_<ИМЯ_ПОЛЯ>_NAME}` — локализованное название страны.

Кроме того, есть групповые теги для блока `XTRA_EXTRAFLD`:

- `{MARKET_XTRA_EXTRAFIELD_TITLE}`
- `{MARKET_XTRA_EXTRAFIELD_VALUE}`
- `{MARKET_XTRA_EXTRAFIELD_NAME}` (имя поля)

<a name="view-individual-tags"></a>
### 6.3. Индивидуальные теги каждого поля

**Таблица тегов для демо-полей на странице товара:**

| Поле | Значение | Заголовок | Сырое значение |
|------|----------|-----------|----------------|
| `event_name` | `{MARKET_XTRA_EVENT_NAME}` | `{MARKET_XTRA_EVENT_NAME_TITLE}` | `{MARKET_XTRA_EVENT_NAME_VALUE}` |
| `event_description` | `{MARKET_XTRA_EVENT_DESCRIPTION}` | `{MARKET_XTRA_EVENT_DESCRIPTION_TITLE}` | `{MARKET_XTRA_EVENT_DESCRIPTION_VALUE}` |
| `event_start` | `{MARKET_XTRA_EVENT_START}` | `{MARKET_XTRA_EVENT_START_TITLE}` | `{MARKET_XTRA_EVENT_START_VALUE}` |
| `event_ticketprice` | `{MARKET_XTRA_EVENT_TICKETPRICE}` | `{MARKET_XTRA_EVENT_TICKETPRICE_TITLE}` | `{MARKET_XTRA_EVENT_TICKETPRICE_VALUE}` |
| `event_seson` | `{MARKET_XTRA_EVENT_SESON}` | `{MARKET_XTRA_EVENT_SESON_TITLE}` | `{MARKET_XTRA_EVENT_SESON_VALUE}` |
| `demo_int` | `{MARKET_XTRA_DEMO_INT}` | `{MARKET_XTRA_DEMO_INT_TITLE}` | `{MARKET_XTRA_DEMO_INT_VALUE}` |
| `demo_double` | `{MARKET_XTRA_DEMO_DOUBLE}` | `{MARKET_XTRA_DEMO_DOUBLE_TITLE}` | `{MARKET_XTRA_DEMO_DOUBLE_VALUE}` |
| `demo_select` | `{MARKET_XTRA_DEMO_SELECT}` | `{MARKET_XTRA_DEMO_SELECT_TITLE}` | `{MARKET_XTRA_DEMO_SELECT_VALUE}` |
| `demo_checkbox` | `{MARKET_XTRA_DEMO_CHECKBOX}` | `{MARKET_XTRA_DEMO_CHECKBOX_TITLE}` | `{MARKET_XTRA_DEMO_CHECKBOX_VALUE}` |
| `demo_radio` | `{MARKET_XTRA_DEMO_RADIO}` | `{MARKET_XTRA_DEMO_RADIO_TITLE}` | `{MARKET_XTRA_DEMO_RADIO_VALUE}` |
| `demo_datetime` | `{MARKET_XTRA_DEMO_DATETIME}` | `{MARKET_XTRA_DEMO_DATETIME_TITLE}` | `{MARKET_XTRA_DEMO_DATETIME_VALUE}` |
| `demo_file` | `{MARKET_XTRA_DEMO_FILE}` | `{MARKET_XTRA_DEMO_FILE_TITLE}` | `{MARKET_XTRA_DEMO_FILE_VALUE}` |
| `demo_country` | `{MARKET_XTRA_DEMO_COUNTRY}` | `{MARKET_XTRA_DEMO_COUNTRY_TITLE}` | `{MARKET_XTRA_DEMO_COUNTRY_VALUE}` (+ `{MARKET_XTRA_DEMO_COUNTRY_NAME}`) |
| `demo_range` | `{MARKET_XTRA_DEMO_RANGE}` | `{MARKET_XTRA_DEMO_RANGE_TITLE}` | `{MARKET_XTRA_DEMO_RANGE_VALUE}` |
| `demo_checklistbox` | `{MARKET_XTRA_DEMO_CHECKLISTBOX}` | `{MARKET_XTRA_DEMO_CHECKLISTBOX_TITLE}` | `{MARKET_XTRA_DEMO_CHECKLISTBOX_VALUE}` |

<a name="view-group-block"></a>
### 6.4. Групповой блок `XTRA_EXTRAFLD`

На странице товара также можно вывести все поля циклом:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="d-flex mb-3">
    <div class="contact-label">{MARKET_XTRA_EXTRAFIELD_TITLE}</div>
    <div class="contact-value">{MARKET_XTRA_EXTRAFIELD_VALUE}</div>
</div>
<!-- END: XTRA_EXTRAFLD -->
```

Этот блок повторится для каждого поля. Внутри него доступны также `{MARKET_XTRA_EXTRAFIELD_NAME}` — имя поля (например, `event_name`).

<a name="view-country"></a>
### 6.5. Особенности поля «страна»

Для полей типа `country` дополнительно выводится локализованное название страны через тег `_NAME`. Например:

```html
<!-- IF {MARKET_XTRA_DEMO_COUNTRY_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-label">{MARKET_XTRA_DEMO_COUNTRY_TITLE}</div>
    <div class="contact-value">{MARKET_XTRA_DEMO_COUNTRY} {MARKET_XTRA_DEMO_COUNTRY_NAME}</div>
</div>
<!-- ENDIF -->
```

Здесь `{MARKET_XTRA_DEMO_COUNTRY}` выведет двухбуквенный код страны (например, `UA`), а `{MARKET_XTRA_DEMO_COUNTRY_NAME}` — её название на текущем языке пользователя.

<a name="view-i18n"></a>
### 6.6. Мультиязычность на странице товара

Если включена мультиязычность, то для типов полей, **не имеющих встроенной локализации** (`input`, `textarea`, `double`, `inputint`, `datetime`, `range`, `file`, `country`), значение автоматически подменяется переводом из таблицы `cot_xtradbrowmarket_i18n`, если такой перевод существует для текущего языка пользователя.

Для типов `select`, `radio`, `checklistbox` локализация выполняется штатными средствами Cotonti через языковые ключи `$L['имя_поля_значение']`, поэтому переводы в таблице i18n для них не используются.

**Практический вывод:** в шаблоне вы всегда используете одни и те же теги `{MARKET_XTRA_...}`; подмена значения происходит автоматически.

<a name="view-example-code"></a>
### 6.7. Пример готового кода для `market.tpl`

Этот код можно разместить в любом месте шаблона страницы товара, например, после основного текста.

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->

<!-- IF {MARKET_XTRA_EVENT_NAME_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_EVENT_NAME_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_EVENT_NAME}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_EVENT_DESCRIPTION_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_EVENT_DESCRIPTION_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_EVENT_DESCRIPTION}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_EVENT_START_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_EVENT_START_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_EVENT_START}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_EVENT_TICKETPRICE_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_EVENT_TICKETPRICE_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_EVENT_TICKETPRICE}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_EVENT_SESON_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_EVENT_SESON_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_EVENT_SESON}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_INT_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_INT_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_INT}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_DOUBLE_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_DOUBLE_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_DOUBLE}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_SELECT_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_SELECT_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_SELECT}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_CHECKBOX} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_CHECKBOX_TITLE}</div>
        <div class="contact-value"><span class="badge bg-success">{PHP.L.Yes}</span></div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_RADIO_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_RADIO_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_RADIO}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_DATETIME_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_DATETIME_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_DATETIME}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_FILE_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_FILE_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_FILE}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_COUNTRY_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_COUNTRY_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_COUNTRY} {MARKET_XTRA_DEMO_COUNTRY_NAME}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_RANGE_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_RANGE_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_RANGE}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- IF {MARKET_XTRA_DEMO_CHECKLISTBOX_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-icon"><i class="fa-solid fa-circle-info"></i></div>
    <div>
        <div class="contact-label">{MARKET_XTRA_DEMO_CHECKLISTBOX_TITLE}</div>
        <div class="contact-value">{MARKET_XTRA_DEMO_CHECKLISTBOX}</div>
    </div>
</div>
<!-- ENDIF -->

<!-- ENDIF -->
```

---

<a name="hook-markettags-main"></a>
## 7. Хук `markettags.main` — общий массив тегов `cot_generate_markettags`

<a name="main-when"></a>
### 7.1. Когда вызывается

Файл `xtradbrowmarket.markettags.php` подключён к хуку `markettags.main`. Этот хук вызывается внутри системной функции `cot_generate_markettags()`, которая формирует общий массив тегов для товара. Эта функция используется в различных контекстах, **кроме стандартного списка товаров в шаблоне `market.list.tpl`**. Например, она может вызываться в корзине, SEO-плагинах, виджетах и других расширениях, которые получают теги товара через общий массив.

**Важно:** для вывода полей в списке товаров (`market.list.tpl`) плагин использует отдельный хук `market.list.loop` (файл `xtradbrowmarket.market.list.loop.php`). Поэтому в разделе 8 данного руководства описан именно этот хук и соответствующие теги `LIST_ROW_XTRA_*`.

<a name="main-how-tags-work"></a>
### 7.2. Как теги попадают в другие плагины и шаблоны

Внутри функции `cot_generate_markettags()` создаётся локальный массив `$temp_array`. Хук `markettags.main` добавляет в него новые элементы с ключами, начинающимися с префикса **`XTRADBROWMARKET_`**.

После того как функция завершает работу, все элементы массива `$temp_array` преобразуются в теги с добавлением переданного префикса (например, `ITEM_`, `CART_ROW_` и т.д.). Таким образом, если функция была вызвана с префиксом `ITEM_`, то ключ `XTRADBROWMARKET_EVENT_NAME` станет тегом `{ITEM_XTRADBROWMARKET_EVENT_NAME}`.

**Это очень важно:** теги, добавленные в общий массив, доступны в любом шаблоне, который вызывает `cot_generate_markettags()` с определённым префиксом.

<a name="main-prefix"></a>
### 7.3. Префикс `XTRADBROWMARKET_`

Для каждого экстраполя в массив добавляются три ключа:

- `XTRADBROWMARKET_<ИМЯ>` — отформатированное значение.
- `XTRADBROWMARKET_<ИМЯ>_TITLE` — заголовок.
- `XTRADBROWMARKET_<ИМЯ>_VALUE` — сырое значение.

Если поле имеет тип `country`, также добавляется `XTRADBROWMARKET_<ИМЯ>_NAME` — название страны.

**Пример для поля `event_name`:** в массиве появятся ключи `XTRADBROWMARKET_EVENT_NAME`, `XTRADBROWMARKET_EVENT_NAME_TITLE`, `XTRADBROWMARKET_EVENT_NAME_VALUE`.

<a name="main-example"></a>
### 7.4. Пример использования в произвольном шаблоне

Допустим, какой-то плагин или шаблон вызывает `cot_generate_markettags()` с префиксом `ITEM_`. Тогда теги будут выглядеть так:

```html
<!-- IF {ITEM_XTRADBROWMARKET_EVENT_NAME_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-label">{ITEM_XTRADBROWMARKET_EVENT_NAME_TITLE}</div>
    <div class="contact-value">{ITEM_XTRADBROWMARKET_EVENT_NAME}</div>
</div>
<!-- ENDIF -->
```

Аналогично для всех остальных полей. **Конкретный префикс зависит от контекста вызова.** В разных расширениях он может быть `PRODUCT_`, `CART_ROW_`, `ORDER_` и т.д. Общее правило: если в шаблоне вы видите теги вроде `{ITEM_TITLE}`, `{ITEM_ID}`, значит функция была вызвана с префиксом `ITEM_`. Тогда все добавленные хуком теги следует использовать с этим же префиксом: `{ITEM_XTRADBROWMARKET_EVENT_NAME}`.

**Напоминание:** Для стандартного списка товаров (`market.list.tpl`) используйте **раздел 8**, так как там применяется отдельный хук с префиксом `LIST_ROW_XTRA_`.

<a name="main-i18n"></a>
### 7.5. Особенности мультиязычности и типов

Механизм подмены перевода аналогичен тому, что используется в хуке `market.tags`: для типов без встроенной локализации значение заменяется переводом, если он есть. Поэтому в шаблонах не нужно вручную обрабатывать переводы — всё делается автоматически.

---

<a name="hook-market-list-loop"></a>
## 8. Хук `market.list.loop` — вывод в списке товаров

<a name="list-when-where"></a>
### 8.1. Когда вызывается и где используется

Файл `xtradbrowmarket.market.list.loop.php` подключён к хуку `market.list.loop`. Этот хук вызывается **на каждой итерации цикла** вывода списка товаров в шаблоне `market.list.tpl`. Он позволяет добавить дополнительные теги к текущему товару внутри цикла.

<a name="list-available-tags"></a>
### 8.2. Доступные теги

Хук присваивает для каждого поля следующие теги с префиксом `LIST_ROW_`:

- `{LIST_ROW_XTRA_<ИМЯ_ПОЛЯ>}` — отформатированное значение.
- `{LIST_ROW_XTRA_<ИМЯ_ПОЛЯ>_TITLE}` — заголовок.
- `{LIST_ROW_XTRA_<ИМЯ_ПОЛЯ>_VALUE}` — сырое значение.
- Для поля `country` дополнительно: `{LIST_ROW_XTRA_<ИМЯ_ПОЛЯ>_NAME}`.

Также внутри цикла можно использовать групповой блок `XTRA_EXTRAFLD`, который будет повторяться для каждого поля текущего товара.

<a name="list-individual-tags"></a>
### 8.3. Индивидуальные теги каждого поля

**Таблица тегов для списка товаров:**

| Поле | Значение | Заголовок | Сырое значение |
|------|----------|-----------|----------------|
| `event_name` | `{LIST_ROW_XTRA_EVENT_NAME}` | `{LIST_ROW_XTRA_EVENT_NAME_TITLE}` | `{LIST_ROW_XTRA_EVENT_NAME_VALUE}` |
| `event_description` | `{LIST_ROW_XTRA_EVENT_DESCRIPTION}` | `{LIST_ROW_XTRA_EVENT_DESCRIPTION_TITLE}` | `{LIST_ROW_XTRA_EVENT_DESCRIPTION_VALUE}` |
| `event_start` | `{LIST_ROW_XTRA_EVENT_START}` | `{LIST_ROW_XTRA_EVENT_START_TITLE}` | `{LIST_ROW_XTRA_EVENT_START_VALUE}` |
| `event_ticketprice` | `{LIST_ROW_XTRA_EVENT_TICKETPRICE}` | `{LIST_ROW_XTRA_EVENT_TICKETPRICE_TITLE}` | `{LIST_ROW_XTRA_EVENT_TICKETPRICE_VALUE}` |
| `event_seson` | `{LIST_ROW_XTRA_EVENT_SESON}` | `{LIST_ROW_XTRA_EVENT_SESON_TITLE}` | `{LIST_ROW_XTRA_EVENT_SESON_VALUE}` |
| `demo_int` | `{LIST_ROW_XTRA_DEMO_INT}` | `{LIST_ROW_XTRA_DEMO_INT_TITLE}` | `{LIST_ROW_XTRA_DEMO_INT_VALUE}` |
| `demo_double` | `{LIST_ROW_XTRA_DEMO_DOUBLE}` | `{LIST_ROW_XTRA_DEMO_DOUBLE_TITLE}` | `{LIST_ROW_XTRA_DEMO_DOUBLE_VALUE}` |
| `demo_select` | `{LIST_ROW_XTRA_DEMO_SELECT}` | `{LIST_ROW_XTRA_DEMO_SELECT_TITLE}` | `{LIST_ROW_XTRA_DEMO_SELECT_VALUE}` |
| `demo_checkbox` | `{LIST_ROW_XTRA_DEMO_CHECKBOX}` | `{LIST_ROW_XTRA_DEMO_CHECKBOX_TITLE}` | `{LIST_ROW_XTRA_DEMO_CHECKBOX_VALUE}` |
| `demo_radio` | `{LIST_ROW_XTRA_DEMO_RADIO}` | `{LIST_ROW_XTRA_DEMO_RADIO_TITLE}` | `{LIST_ROW_XTRA_DEMO_RADIO_VALUE}` |
| `demo_datetime` | `{LIST_ROW_XTRA_DEMO_DATETIME}` | `{LIST_ROW_XTRA_DEMO_DATETIME_TITLE}` | `{LIST_ROW_XTRA_DEMO_DATETIME_VALUE}` |
| `demo_file` | `{LIST_ROW_XTRA_DEMO_FILE}` | `{LIST_ROW_XTRA_DEMO_FILE_TITLE}` | `{LIST_ROW_XTRA_DEMO_FILE_VALUE}` |
| `demo_country` | `{LIST_ROW_XTRA_DEMO_COUNTRY}` | `{LIST_ROW_XTRA_DEMO_COUNTRY_TITLE}` | `{LIST_ROW_XTRA_DEMO_COUNTRY_VALUE}` (+ `{LIST_ROW_XTRA_DEMO_COUNTRY_NAME}`) |
| `demo_range` | `{LIST_ROW_XTRA_DEMO_RANGE}` | `{LIST_ROW_XTRA_DEMO_RANGE_TITLE}` | `{LIST_ROW_XTRA_DEMO_RANGE_VALUE}` |
| `demo_checklistbox` | `{LIST_ROW_XTRA_DEMO_CHECKLISTBOX}` | `{LIST_ROW_XTRA_DEMO_CHECKLISTBOX_TITLE}` | `{LIST_ROW_XTRA_DEMO_CHECKLISTBOX_VALUE}` |

<a name="list-group-block"></a>
### 8.4. Групповой блок внутри списка

Внутри цикла списка товаров можно вывести все поля товара с помощью блока `XTRA_EXTRAFLD`:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EXTRAFLD_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EXTRAFLD}</div>
</div>
<!-- END: XTRA_EXTRAFLD -->
```

**Примечание:** В файле хука `xtradbrowmarket.market.list.loop.php` для парсинга этого блока используется вызов `$t->parse('MAIN.USERS_ROW.XTRA_EXTRAFLD')`. Это может быть опечаткой, и для корректной работы в шаблоне `market.list.tpl` следует использовать блок `<!-- BEGIN: XTRA_EXTRAFLD -->` внутри блока `LIST_ROW`, а в коде хука при необходимости поправить путь на `MAIN.LIST_ROW.XTRA_EXTRAFLD`. Однако в предоставленном файле хука используется `USERS_ROW`, что указывает на возможную ошибку копирования. В любом случае, вы можете использовать индивидуальные теги, чтобы избежать этой проблемы.

<a name="list-example-code"></a>
### 8.5. Пример готового кода для `market.list.tpl`

Ниже фрагмент, который можно вставить внутри цикла списка товаров (между `<!-- BEGIN: LIST_ROW -->` и `<!-- END: LIST_ROW -->`).

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->

<!-- IF {LIST_ROW_XTRA_EVENT_NAME_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EVENT_NAME_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EVENT_NAME}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_EVENT_DESCRIPTION_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EVENT_DESCRIPTION_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EVENT_DESCRIPTION}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_EVENT_START_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EVENT_START_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EVENT_START}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_EVENT_TICKETPRICE_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EVENT_TICKETPRICE_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EVENT_TICKETPRICE}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_EVENT_SESON_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EVENT_SESON_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EVENT_SESON}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_INT_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_INT_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_INT}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_DOUBLE_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_DOUBLE_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_DOUBLE}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_SELECT_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_SELECT_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_SELECT}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_CHECKBOX} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_CHECKBOX_TITLE}</div>
    <div class="contact-value"><span class="badge bg-success">{PHP.L.Yes}</span></div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_RADIO_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_RADIO_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_RADIO}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_DATETIME_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_DATETIME_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_DATETIME}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_FILE_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_FILE_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_FILE}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_COUNTRY_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_COUNTRY_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_COUNTRY} {LIST_ROW_XTRA_DEMO_COUNTRY_NAME}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_RANGE_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_RANGE_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_RANGE}</div>
</div>
<!-- ENDIF -->

<!-- IF {LIST_ROW_XTRA_DEMO_CHECKLISTBOX_VALUE} -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_DEMO_CHECKLISTBOX_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_DEMO_CHECKLISTBOX}</div>
</div>
<!-- ENDIF -->

<!-- ENDIF -->
```

---

<a name="tables"></a>
## 9. Таблицы соответствий

<a name="table-prefixes"></a>
### 9.1. Таблица 1. Префиксы тегов по файлам хуков

| Файл хука | Хук | Префикс тегов | Пример тега |
|-----------|------|---------------|-------------|
| `xtradbrowmarket.market.edit.tags.php` | `market.edit.tags` | `MARKETEDIT_FORM_XTRA_` | `{MARKETEDIT_FORM_XTRA_EVENT_NAME}` |
| `xtradbrowmarket.market.tags.php` | `market.tags` | `MARKET_XTRA_` | `{MARKET_XTRA_EVENT_NAME}` |
| `xtradbrowmarket.markettags.php` | `markettags.main` | `XTRADBROWMARKET_` (внутри общего префикса) | `{ITEM_XTRADBROWMARKET_EVENT_NAME}` (если префикс `ITEM_`) |
| `xtradbrowmarket.market.list.loop.php` | `market.list.loop` | `LIST_ROW_XTRA_` | `{LIST_ROW_XTRA_EVENT_NAME}` |

<a name="table-field-types"></a>
### 9.2. Таблица 2. Типы полей и их отображение

| Тип поля | Что выводится в `_VALUE` | Что выводится в основном теге | Особенности |
|----------|--------------------------|-------------------------------|-------------|
| `input` | строка | экранированная строка или HTML (зависит от парсера) | Проверка по `_VALUE` |
| `textarea` | строка | обработанный текст | Проверка по `_VALUE` |
| `datetime` | timestamp (int) или 0 | отформатированная дата | **Проверять строго по `_VALUE`, так как 0 может превратиться в "01.01.1970"** |
| `double` | число | число | Проверка по `_VALUE` |
| `select` | выбранное значение (строка) | локализованное значение | Проверка по `_VALUE` |
| `checkbox` | 1 или 0 | 1 или 0 | Лучше использовать `<!-- IF {TAG} -->` и выводить свой текст |
| `radio` | выбранное значение | локализованное значение | Проверка по `_VALUE` |
| `file` | имя файла | имя файла | Проверка по `_VALUE` |
| `country` | код страны | код страны | Дополнительно есть `_NAME` |
| `range` | число | число | Проверка по `_VALUE` |
| `checklistbox` | строка через запятую | локализованная строка через разделитель | Проверка по `_VALUE` |

<a name="table-suffixes"></a>
### 9.3. Таблица 3. Суффиксы `_TITLE`, `_VALUE`, `_NAME`

| Суффикс | Назначение | Пример |
|---------|------------|--------|
| (без суффикса) | Отформатированное значение для вывода | `{MARKET_XTRA_EVENT_NAME}` |
| `_TITLE` | Заголовок поля (локализованное описание) | `{MARKET_XTRA_EVENT_NAME_TITLE}` |
| `_VALUE` | Сырое значение (рекомендуется для проверок) | `{MARKET_XTRA_EVENT_NAME_VALUE}` |
| `_NAME` | Только для `country`: название страны | `{MARKET_XTRA_DEMO_COUNTRY_NAME}` |

---

<a name="empty-values-security"></a>
## 10. Обработка пустых значений и безопасность

- Всегда проверяйте наличие значения перед выводом, чтобы не показывать пустые блоки.
- Для полей типа `datetime` **обязательно** используйте проверку `<!-- IF {TAG_VALUE} -->`, иначе значение `0` будет показано как "01.01.1970".
- Для полей типа `checkbox` используйте проверку `<!-- IF {TAG} -->`, но значение `1` можно заменить на понятный текст.
- Для текстовых полей (`input`, `textarea`) значения могут содержать HTML, если поле настроено с парсером HTML. Убедитесь, что вывод безопасен.
- При выводе в списках учитывайте, что некоторые поля могут быть длинными; используйте CSS для обрезки или адаптации.

---

<a name="conclusion"></a>
## 11. Заключение

В этом руководстве мы подробно рассмотрели, как выводить теги плагина **xtradbrowmarket** в четырёх основных контекстах: форма редактирования, страница товара, общий массив тегов и список товаров. Вы узнали о префиксах, суффиксах и особенностях типов полей.

Используя приведённые примеры и таблицы, вы сможете легко интегрировать дополнительные поля в свои шаблоны и адаптировать их под собственные нужды.
