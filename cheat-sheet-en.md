# Complete Guide to Outputting Tags of the xtradbrowmarket Plugin in Cotonti Templates

**Table of Contents**

1. [Introduction](#introduction)  
2. [Basics of Cotonti Templates](#cotonti-templates-basics)  
   2.1. [How Cotonti Processes Templates](#template-engine)  
   2.2. [Syntax of Tags and Blocks](#template-syntax)  
   2.3. [Global Variables and Conditions](#template-conditions)  
3. [Overview of the xtradbrowmarket Plugin](#plugin-overview)  
   3.1. [Purpose of the Plugin](#plugin-purpose)  
   3.2. [Demonstration Fields](#demo-fields)  
   3.3. [Database Tables](#database-tables)  
4. [Hook Files Responsible for Tag Output](#hook-files-overview)  
5. [Hook `market.edit.tags` — Product Edit Form](#hook-market-edit-tags)  
   5.1. [When It Is Called and Where It Is Used](#edit-when-where)  
   5.2. [Available Tags](#edit-available-tags)  
   5.3. [Individual Tags for Each Field](#edit-individual-tags)  
   5.4. [Group Block `XTRA_EXTRAFLD`](#edit-group-block)  
   5.5. [Multilingual Translation Tags](#edit-i18n-tags)  
   5.6. [Peculiarities of Field Types in the Form](#edit-field-types)  
   5.7. [Ready-Made Code Example for `market.edit.tpl`](#edit-example-code)  
6. [Hook `market.tags` — Product View Page](#hook-market-tags)  
   6.1. [When It Is Called and Where It Is Used](#view-when-where)  
   6.2. [Available Tags](#view-available-tags)  
   6.3. [Individual Tags for Each Field](#view-individual-tags)  
   6.4. [Group Block `XTRA_EXTRAFLD`](#view-group-block)  
   6.5. [Peculiarities of the “Country” Field](#view-country)  
   6.6. [Multilingual Support on the Product Page](#view-i18n)  
   6.7. [Ready-Made Code Example for `market.tpl`](#view-example-code)  
7. [Hook `markettags.main` — Common Tag Array of `cot_generate_markettags`](#hook-markettags-main)  
   7.1. [When It Is Called](#main-when)  
   7.2. [How Tags Reach Other Plugins and Templates](#main-how-tags-work)  
   7.3. [Prefix `XTRADBROWMARKET_`](#main-prefix)  
   7.4. [Example of Use in an Arbitrary Template](#main-example)  
   7.5. [Peculiarities of Multilingual Support and Types](#main-i18n)  
8. [Hook `market.list.loop` — Output in the Product List](#hook-market-list-loop)  
   8.1. [When It Is Called and Where It Is Used](#list-when-where)  
   8.2. [Available Tags](#list-available-tags)  
   8.3. [Individual Tags for Each Field](#list-individual-tags)  
   8.4. [Group Block Inside the List](#list-group-block)  
   8.5. [Ready-Made Code Example for `market.list.tpl`](#list-example-code)  
9. [Correspondence Tables](#tables)  
   9.1. [Table 1. Tag Prefixes by Hook Files](#table-prefixes)  
   9.2. [Table 2. Field Types and Their Display](#table-field-types)  
   9.3. [Table 3. Suffixes `_TITLE`, `_VALUE`, `_NAME`](#table-suffixes)  
10. [Handling Empty Values and Security](#empty-values-security)  
11. [Conclusion](#conclusion)

---

<a name="introduction"></a>
## 1. Introduction

The **xtradbrowmarket** plugin is intended to add extra fields (extrafields) to products of the **Market PRO** module in the **Cotonti CMF** system. The plugin creates its own database table, registers a set of demonstration fields in it, and provides convenient hooks for outputting these fields in templates.

This guide explains **how to correctly output the tags** of these extra fields in various parts of the site: the product edit form, the product view page, the product list, and any other templates that use the common tag array.

The guide is intended for **beginner developers** who already have a basic understanding of the Cotonti system and its template engine. If you are new to Cotonti, we recommend first reading the official documentation.

All examples in this guide are based on the **demonstration fields** that are installed by default. You can adapt them to your own fields.

---

<a name="cotonti-templates-basics"></a>
## 2. Basics of Cotonti Templates

Before moving on to the specific plugin tags, it is necessary to understand how Cotonti processes templates and how the tag mechanism works.

<a name="template-engine"></a>
### 2.1. How Cotonti Processes Templates

Cotonti uses its own template engine called **XTemplate**, which works with **blocks** and **tags**. Templates are usually located in the following folders:

- `themes/your_theme/modules/market/` — Market module templates;
- `themes/your_theme/plug/xtradbrowmarket/` — plugin templates (if any).

The main Market module template is called `market.tpl`, the edit form is `market.edit.tpl`, and the product list is `market.list.tpl`.

When Cotonti processes a request, it loads the corresponding template, compiles it into PHP objects, and then substitutes tag values.

<a name="template-syntax"></a>
### 2.2. Syntax of Tags and Blocks

- A **tag** is written in curly braces: `{TAG_NAME}`.
- A **block** is a repeating section delimited by the comments `<!-- BEGIN: BLOCK_NAME -->` and `<!-- END: BLOCK_NAME -->`.
- A **condition** is written as `<!-- IF expression --> ... <!-- ENDIF -->` (or with `<!-- ELSE -->`).
- Inside conditions, you can use comparison operators: `==`, `!=`, `>`, `<`, `>=`, `<=`, `AND`, `OR`, `!`.

Example:

```html
<!-- IF {MY_VARIABLE} -->
    The variable exists and is not empty.
<!-- ELSE -->
    The variable is empty or undefined.
<!-- ENDIF -->
```

To check a tag value, you can simply use `<!-- IF {MY_TAG} -->` — this returns `true` if the value is not `false`, `null`, `0`, or an empty string `''`.

However, it is important: after processing by some functions (for example, date formatting), an empty value can turn into a non-empty string, so for such fields it is recommended to check the **raw value** with the `_VALUE` suffix.

<a name="template-conditions"></a>
### 2.3. Global Variables and Conditions

Global variables are available in Cotonti templates through the `PHP.` prefix:

- `{PHP.L.Key}` — language string.
- `{PHP.cfg.Key}` — configuration value.
- `{PHP.usr.Field}` — data of the current user.
- `{PHP|cot_plugin_active('name')}` — check whether a plugin is active (returns `true` or `false`).

Example of checking whether the plugin is active:

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
    ... output fields ...
<!-- ENDIF -->
```

---

<a name="plugin-overview"></a>
## 3. Overview of the xtradbrowmarket Plugin

<a name="plugin-purpose"></a>
### 3.1. Purpose of the Plugin

The plugin adds a set of **extra fields** (extrafields) to products of the Market module. These fields are stored in a separate table `cot_xtradbrowmarket`, linked to the products table through the foreign key `itempagid`. The main goal is to demonstrate working with the Cotonti extrafields API and to provide ready-made functionality with multilingual support.

<a name="demo-fields"></a>
### 3.2. Demonstration Fields

The following 15 fields are created when the plugin is installed:

| Database field name | Type | Description |
|---------------------|------|-------------|
| `event_name` | `input` | Event name |
| `event_description` | `textarea` | Event description |
| `event_start` | `datetime` | Event start |
| `event_ticketprice` | `double` | Ticket price |
| `event_seson` | `select` | Season |
| `demo_int` | `inputint` | Example of an integer |
| `demo_double` | `double` | Example of a floating-point number |
| `demo_select` | `select` | Example of a dropdown list |
| `demo_checkbox` | `checkbox` | Example of a checkbox |
| `demo_radio` | `radio` | Example of radio buttons |
| `demo_datetime` | `datetime` | Example of date and time |
| `demo_file` | `file` | Example of file upload |
| `demo_country` | `country` | Example of country selection |
| `demo_range` | `range` | Example of a numeric range |
| `demo_checklistbox` | `checklistbox` | Example of checkboxes with multiple selection |

These names are used to form tags: they are converted to **uppercase** and supplemented with prefixes and suffixes.

<a name="database-tables"></a>
### 3.3. Database Tables

- `cot_xtradbrowmarket` — the main table of field values. The primary key `itempagid` corresponds to `fieldmrkt_id` from the `cot_market` table.
- `cot_xtradbrowmarket_i18n` — the table of translations of values for multilingual fields (usually only `input` and `textarea`).

---

<a name="hook-files-overview"></a>
## 4. Hook Files Responsible for Tag Output

The plugin uses several files connected to certain system hooks. Each file is responsible for its own output context:

| Hook file | Hook in Cotonti | Purpose |
|-----------|-----------------|---------|
| `xtradbrowmarket.market.edit.tags.php` | `market.edit.tags` | Output of fields in the **product edit/add** form (template `market.edit.tpl`) |
| `xtradbrowmarket.market.tags.php` | `market.tags` | Output of fields on the **product view page** (template `market.tpl`) |
| `xtradbrowmarket.markettags.php` | `markettags.main` | Adding tags to the **common array** of the `cot_generate_markettags()` function (used in many plugins, widgets, cart, SEO, etc.) |
| `xtradbrowmarket.market.list.loop.php` | `market.list.loop` | Output of fields in the **product list** (template `market.list.tpl`) inside the loop |

Each of these files generates a set of tags with a certain prefix. Let us consider them in detail.

---

<a name="hook-market-edit-tags"></a>
## 5. Hook `market.edit.tags` — Product Edit Form

<a name="edit-when-where"></a>
### 5.1. When It Is Called and Where It Is Used

The file `xtradbrowmarket.market.edit.tags.php` is connected to the `market.edit.tags` hook. This hook fires during the construction of the **product add/edit** form in the Market module (template `market.edit.tpl`).

All extrafield values that are already saved for the edited product are inserted into the form fields automatically.

<a name="edit-available-tags"></a>
### 5.2. Available Tags

Two types of tags are available in the edit form:

1. **Individual tags** for each field — they can be used anywhere in the template.
2. **Group block** `XTRA_EXTRAFLD` — allows you to output all fields in a loop without manually listing each name.

In addition, if multilingual support is enabled, translation tags are available for text fields (`input` and `textarea`).

<a name="edit-individual-tags"></a>
### 5.3. Individual Tags for Each Field

For each registered extrafield, the following tags are available (field name in uppercase):

- `{MARKETEDIT_FORM_XTRA_<FIELD_NAME>}` — the HTML code of the field (for example, `<input type="text" ...>` or `<textarea>...`).
- `{MARKETEDIT_FORM_XTRA_<FIELD_NAME>_TITLE}` — the field title (localized description).

**Example for the `event_name` field:**

```html
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EVENT_NAME_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME}
</div>
```

**List of all individual tags for the demo fields:**

| Field | Field tag | Title tag |
|-------|-----------|-----------|
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
### 5.4. Group Block `XTRA_EXTRAFLD`

To avoid manually listing all 15 fields, you can use a group loop. On each iteration, the hook file assigns two universal tags:

- `{MARKETEDIT_FORM_XTRA_EXTRAFLD}` — the HTML code of the current field;
- `{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}` — the title of the current field.

In the template, this loop is designed as follows:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EXTRAFLD}
</div>
<!-- END: XTRA_EXTRAFLD -->
```

The block will repeat as many times as there are registered fields, and its own values will be substituted for each field.

<a name="edit-i18n-tags"></a>
### 5.5. Multilingual Translation Tags

If multilingual support is enabled in the plugin settings (`xtradbrowmarket_i18n_use = 1`) and additional languages are active, additional translation tags are generated **only for fields of types `input` and `textarea`**.

For each active additional language (for example, `en`, `ua`, `pl`), the following tags are created:

- `{MARKETEDIT_FORM_XTRA_<FIELD_NAME>_<LANGUAGE_CODE_IN_UPPERCASE>}` — the translation input field;
- `{MARKETEDIT_FORM_XTRA_<FIELD_NAME>_<LANGUAGE_CODE_IN_UPPERCASE>_TITLE}` — the title indicating the language.

**Example for the `event_name` field and the `en` language:**

```html
<div class="mb-3">
    <label>{MARKETEDIT_FORM_XTRA_EVENT_NAME_EN_TITLE}</label>
    {MARKETEDIT_FORM_XTRA_EVENT_NAME_EN}
</div>
```

For the `event_description` field (textarea), similar tags will also be created, but the HTML field will be multiline.

**Important:** for all other field types (`select`, `checkbox`, `datetime`, etc.), translations are **not created** in the edit form, because their values do not imply arbitrary text input.

<a name="edit-field-types"></a>
### 5.6. Peculiarities of Field Types in the Form

Each field type generates different HTML code, so when manually laying out the form, you need to consider what the field looks like.

- **`input`** — a simple text field `<input type="text" class="form-control" ...>`.
- **`textarea`** — a multiline field `<textarea class="form-control" ...></textarea>`.
- **`datetime`** — a set of selects (day, month, year, hour, minutes) inside a `<div class="row g-2">` block.
- **`select`** — a dropdown list `<select class="form-select">...`.
- **`checkbox`** — a checkbox `<input type="checkbox" ...>`.
- **`radio`** — a group of radio buttons, each with a label.
- **`file`** — a file upload field with a delete checkbox.
- **`country`** — a dropdown list of countries.
- **`range`** — a dropdown list of numbers (implemented via `select`).
- **`checklistbox`** — several checkboxes for multiple selection.

Since the HTML markup is already defined in the extrafield settings, it is usually sufficient to wrap the ready-made tag in a container with a `label`.

<a name="edit-example-code"></a>
### 5.7. Ready-Made Code Example for `market.edit.tpl`

Below is a complete fragment that can be inserted into the `market.edit.tpl` template (usually inside the form after the main fields and before the submit buttons).

```html
<!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->

<!-- Individual fields (all demo fields listed) -->
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

<!-- Multilingual fields (for input/textarea) -->
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

Or you can use the group block instead of individual ones:

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
## 6. Hook `market.tags` — Product View Page

<a name="view-when-where"></a>
### 6.1. When It Is Called and Where It Is Used

The file `xtradbrowmarket.market.tags.php` is connected to the `market.tags` hook. This hook fires on the **product view page** (template `market.tpl`).

Here, the extrafield values are output in a **formatted form**, that is, using the display functions (`cot_build_extrafields_data`), which ensures correct output of dates, localized list values, etc.

<a name="view-available-tags"></a>
### 6.2. Available Tags

On the product page, the following individual tags are available for each field:

- `{MARKET_XTRA_<FIELD_NAME>}` — the formatted value for output.
- `{MARKET_XTRA_<FIELD_NAME>_TITLE}` — the field title.
- `{MARKET_XTRA_<FIELD_NAME>_VALUE}` — the **raw** (unformatted) value, useful for checks in conditions.
- If the field is of type `country`, an additional tag `{MARKET_XTRA_<FIELD_NAME>_NAME}` is available — the localized country name.

In addition, there are group tags for the `XTRA_EXTRAFLD` block:

- `{MARKET_XTRA_EXTRAFIELD_TITLE}`
- `{MARKET_XTRA_EXTRAFIELD_VALUE}`
- `{MARKET_XTRA_EXTRAFIELD_NAME}` (field name)

<a name="view-individual-tags"></a>
### 6.3. Individual Tags for Each Field

**Table of tags for the demo fields on the product page:**

| Field | Value | Title | Raw value |
|-------|-------|-------|-----------|
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
### 6.4. Group Block `XTRA_EXTRAFLD`

On the product page, you can also output all fields in a loop:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="d-flex mb-3">
    <div class="contact-label">{MARKET_XTRA_EXTRAFIELD_TITLE}</div>
    <div class="contact-value">{MARKET_XTRA_EXTRAFIELD_VALUE}</div>
</div>
<!-- END: XTRA_EXTRAFLD -->
```

This block will repeat for each field. Inside it, `{MARKET_XTRA_EXTRAFIELD_NAME}` is also available — the field name (for example, `event_name`).

<a name="view-country"></a>
### 6.5. Peculiarities of the “Country” Field

For fields of type `country`, the localized country name is additionally output via the `_NAME` tag. For example:

```html
<!-- IF {MARKET_XTRA_DEMO_COUNTRY_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-label">{MARKET_XTRA_DEMO_COUNTRY_TITLE}</div>
    <div class="contact-value">{MARKET_XTRA_DEMO_COUNTRY} {MARKET_XTRA_DEMO_COUNTRY_NAME}</div>
</div>
<!-- ENDIF -->
```

Here `{MARKET_XTRA_DEMO_COUNTRY}` will output the two-letter country code (for example, `UA`), and `{MARKET_XTRA_DEMO_COUNTRY_NAME}` — its name in the current user language.

<a name="view-i18n"></a>
### 6.6. Multilingual Support on the Product Page

If multilingual support is enabled, then for field types that **do not have built-in localization** (`input`, `textarea`, `double`, `inputint`, `datetime`, `range`, `file`, `country`), the value is automatically replaced with a translation from the `cot_xtradbrowmarket_i18n` table if such a translation exists for the current user language.

For the `select`, `radio`, and `checklistbox` types, localization is performed by standard Cotonti means through the language keys `$L['field_name_value']`, so translations in the i18n table are not used for them.

**Practical conclusion:** in the template, you always use the same `{MARKET_XTRA_...}` tags; the value replacement happens automatically.

<a name="view-example-code"></a>
### 6.7. Ready-Made Code Example for `market.tpl`

This code can be placed anywhere in the product page template, for example, after the main text.

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
## 7. Hook `markettags.main` — Common Tag Array of `cot_generate_markettags`

<a name="main-when"></a>
### 7.1. When It Is Called

The file `xtradbrowmarket.markettags.php` is connected to the `markettags.main` hook. This hook is called inside the system function `cot_generate_markettags()`, which forms a common tag array for a product. This function is used in various contexts, **except for the standard product list in the `market.list.tpl` template**. For example, it can be called in the cart, SEO plugins, widgets, and other extensions that receive product tags through the common array.

**Important:** for outputting fields in the product list (`market.list.tpl`), the plugin uses a separate hook `market.list.loop` (file `xtradbrowmarket.market.list.loop.php`). Therefore, Section 8 of this guide describes exactly that hook and the corresponding `LIST_ROW_XTRA_*` tags.

<a name="main-how-tags-work"></a>
### 7.2. How Tags Reach Other Plugins and Templates

Inside the `cot_generate_markettags()` function, a local array `$temp_array` is created. The `markettags.main` hook adds new elements to it with keys starting with the prefix **`XTRADBROWMARKET_`**.

After the function finishes, all elements of the `$temp_array` array are converted into tags with the addition of the passed prefix (for example, `ITEM_`, `CART_ROW_`, etc.). Thus, if the function was called with the `ITEM_` prefix, the key `XTRADBROWMARKET_EVENT_NAME` will become the tag `{ITEM_XTRADBROWMARKET_EVENT_NAME}`.

**This is very important:** tags added to the common array are available in any template that calls `cot_generate_markettags()` with a specific prefix.

<a name="main-prefix"></a>
### 7.3. Prefix `XTRADBROWMARKET_`

For each extrafield, three keys are added to the array:

- `XTRADBROWMARKET_<NAME>` — the formatted value.
- `XTRADBROWMARKET_<NAME>_TITLE` — the title.
- `XTRADBROWMARKET_<NAME>_VALUE` — the raw value.

If the field is of type `country`, an additional key `XTRADBROWMARKET_<NAME>_NAME` is added — the country name.

**Example for the `event_name` field:** the keys `XTRADBROWMARKET_EVENT_NAME`, `XTRADBROWMARKET_EVENT_NAME_TITLE`, and `XTRADBROWMARKET_EVENT_NAME_VALUE` will appear in the array.

<a name="main-example"></a>
### 7.4. Example of Use in an Arbitrary Template

Suppose some plugin or template calls `cot_generate_markettags()` with the `ITEM_` prefix. Then the tags will look like this:

```html
<!-- IF {ITEM_XTRADBROWMARKET_EVENT_NAME_VALUE} -->
<div class="d-flex mb-3">
    <div class="contact-label">{ITEM_XTRADBROWMARKET_EVENT_NAME_TITLE}</div>
    <div class="contact-value">{ITEM_XTRADBROWMARKET_EVENT_NAME}</div>
</div>
<!-- ENDIF -->
```

Similarly for all other fields. **The specific prefix depends on the call context.** In different extensions it may be `PRODUCT_`, `CART_ROW_`, `ORDER_`, etc. The general rule is: if in a template you see tags like `{ITEM_TITLE}`, `{ITEM_ID}`, then the function was called with the `ITEM_` prefix. Then all tags added by the hook should be used with the same prefix: `{ITEM_XTRADBROWMARKET_EVENT_NAME}`.

**Reminder:** For the standard product list (`market.list.tpl`), use **Section 8**, because a separate hook with the `LIST_ROW_XTRA_` prefix is used there.

<a name="main-i18n"></a>
### 7.5. Peculiarities of Multilingual Support and Types

The translation replacement mechanism is similar to that used in the `market.tags` hook: for types without built-in localization, the value is replaced with a translation if one exists. Therefore, you do not need to manually process translations in templates — everything is done automatically.

---

<a name="hook-market-list-loop"></a>
## 8. Hook `market.list.loop` — Output in the Product List

<a name="list-when-where"></a>
### 8.1. When It Is Called and Where It Is Used

The file `xtradbrowmarket.market.list.loop.php` is connected to the `market.list.loop` hook. This hook is called **on each iteration of the loop** that outputs the product list in the `market.list.tpl` template. It allows you to add additional tags to the current product inside the loop.

<a name="list-available-tags"></a>
### 8.2. Available Tags

For each field, the hook assigns the following tags with the `LIST_ROW_` prefix:

- `{LIST_ROW_XTRA_<FIELD_NAME>}` — the formatted value.
- `{LIST_ROW_XTRA_<FIELD_NAME>_TITLE}` — the title.
- `{LIST_ROW_XTRA_<FIELD_NAME>_VALUE}` — the raw value.
- For a `country` field, additionally: `{LIST_ROW_XTRA_<FIELD_NAME>_NAME}`.

Also inside the loop, you can use the group block `XTRA_EXTRAFLD`, which will repeat for each field of the current product.

<a name="list-individual-tags"></a>
### 8.3. Individual Tags for Each Field

**Table of tags for the product list:**

| Field | Value | Title | Raw value |
|-------|-------|-------|-----------|
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
### 8.4. Group Block Inside the List

Inside the product list loop, you can output all fields of the product using the `XTRA_EXTRAFLD` block:

```html
<!-- BEGIN: XTRA_EXTRAFLD -->
<div class="d-flex mb-2">
    <div class="contact-label">{LIST_ROW_XTRA_EXTRAFLD_TITLE}</div>
    <div class="contact-value">{LIST_ROW_XTRA_EXTRAFLD}</div>
</div>
<!-- END: XTRA_EXTRAFLD -->
```

**Note:** In the hook file `xtradbrowmarket.market.list.loop.php`, the call `$t->parse('MAIN.USERS_ROW.XTRA_EXTRAFLD')` is used to parse this block. This may be a typo, and for correct operation in the `market.list.tpl` template, you should use the block `<!-- BEGIN: XTRA_EXTRAFLD -->` inside the `LIST_ROW` block, and if necessary, fix the path in the hook code to `MAIN.LIST_ROW.XTRA_EXTRAFLD`. However, in the provided hook file, `USERS_ROW` is used, which indicates a possible copy-paste error. In any case, you can use individual tags to avoid this problem.

<a name="list-example-code"></a>
### 8.5. Ready-Made Code Example for `market.list.tpl`

Below is a fragment that can be inserted inside the product list loop (between `<!-- BEGIN: LIST_ROW -->` and `<!-- END: LIST_ROW -->`).

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
## 9. Correspondence Tables

<a name="table-prefixes"></a>
### 9.1. Table 1. Tag Prefixes by Hook Files

| Hook file | Hook | Tag prefix | Example tag |
|-----------|------|------------|-------------|
| `xtradbrowmarket.market.edit.tags.php` | `market.edit.tags` | `MARKETEDIT_FORM_XTRA_` | `{MARKETEDIT_FORM_XTRA_EVENT_NAME}` |
| `xtradbrowmarket.market.tags.php` | `market.tags` | `MARKET_XTRA_` | `{MARKET_XTRA_EVENT_NAME}` |
| `xtradbrowmarket.markettags.php` | `markettags.main` | `XTRADBROWMARKET_` (inside a common prefix) | `{ITEM_XTRADBROWMARKET_EVENT_NAME}` (if prefix `ITEM_`) |
| `xtradbrowmarket.market.list.loop.php` | `market.list.loop` | `LIST_ROW_XTRA_` | `{LIST_ROW_XTRA_EVENT_NAME}` |

<a name="table-field-types"></a>
### 9.2. Table 2. Field Types and Their Display

| Field type | What is output in `_VALUE` | What is output in the main tag | Peculiarities |
|------------|-----------------------------|--------------------------------|---------------|
| `input` | string | escaped string or HTML (depending on parser) | Check by `_VALUE` |
| `textarea` | string | processed text | Check by `_VALUE` |
| `datetime` | timestamp (int) or 0 | formatted date | **Strictly check by `_VALUE`, because 0 can turn into "01.01.1970"** |
| `double` | number | number | Check by `_VALUE` |
| `select` | selected value (string) | localized value | Check by `_VALUE` |
| `checkbox` | 1 or 0 | 1 or 0 | It is better to use `<!-- IF {TAG} -->` and output your own text |
| `radio` | selected value | localized value | Check by `_VALUE` |
| `file` | file name | file name | Check by `_VALUE` |
| `country` | country code | country code | Additionally has `_NAME` |
| `range` | number | number | Check by `_VALUE` |
| `checklistbox` | comma-separated string | localized string with separator | Check by `_VALUE` |

<a name="table-suffixes"></a>
### 9.3. Table 3. Suffixes `_TITLE`, `_VALUE`, `_NAME`

| Suffix | Purpose | Example |
|--------|---------|---------|
| (no suffix) | Formatted value for output | `{MARKET_XTRA_EVENT_NAME}` |
| `_TITLE` | Field title (localized description) | `{MARKET_XTRA_EVENT_NAME_TITLE}` |
| `_VALUE` | Raw value (recommended for checks) | `{MARKET_XTRA_EVENT_NAME_VALUE}` |
| `_NAME` | Only for `country`: country name | `{MARKET_XTRA_DEMO_COUNTRY_NAME}` |

---

<a name="empty-values-security"></a>
## 10. Handling Empty Values and Security

- Always check for the presence of a value before outputting, so as not to show empty blocks.
- For `datetime` fields, **always** use the check `<!-- IF {TAG_VALUE} -->`, otherwise the value `0` will be shown as "01.01.1970".
- For `checkbox` fields, use the check `<!-- IF {TAG} -->`, but the value `1` can be replaced with a clear text.
- For text fields (`input`, `textarea`), values may contain HTML if the field is configured with an HTML parser. Ensure that the output is safe.
- When outputting in lists, keep in mind that some fields may be long; use CSS for truncation or adaptation.

---

<a name="conclusion"></a>
## 11. Conclusion

In this guide, we have examined in detail how to output the tags of the **xtradbrowmarket** plugin in four main contexts: the edit form, the product page, the common tag array, and the product list. You have learned about prefixes, suffixes, and the peculiarities of field types.

Using the examples and tables provided, you can easily integrate the extra fields into your templates and adapt them to your own needs.



