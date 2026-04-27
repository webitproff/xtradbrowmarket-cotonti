## Extrafields Market Custom — Expansion for Market PRO

**General plugin information**

- **Code:** xtradbrowmarket
- **Purpose:** adds extra fields for the [**"Market PRO v.5"**](https://abuyfile.com/ru/market/cotonti/plugs/marketpro) module into its own database table
- **Version:** 2.7.9
- **Date:** April 26, 2026
- **Author:** webitproff
- **Copyright:** © 2026 webitproff
- **Notes:** Beginners are advised to study the [**forum section about the ExtraFields API**](https://abuyfile.com/ru/forums/cotonti/original/extrafields). [**(code in this file)**](https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php). After installing the plugin, immediately open its extra fields management.
- **Dependencies:** [**Market PRO v.5+**](https://github.com/webitproff/marketpro-cotonti) by webitproff

<img width="1536" height="1024" alt="Плагин “Extrafields Market-Pro Custom” для Cotonti" src="https://github.com/user-attachments/assets/a5aeb16f-fe2b-496f-b904-ee3c756ccf56" />

### [**permanent link to the current plugin source code on GitHub**](https://github.com/webitproff/xtradbrowmarket-cotonti)

## Plugin structure (hooks)

\# Part File Hooks 

1 extrafields xtradbrowmarket.extrafields.php admin.extrafields.first 

2 header.tags xtradbrowmarket.header.tags.php header.tags 

3 market.edit.delete.done xtradbrowmarket.market.edit.delete.done.php market.edit.delete.done 

4 market.edit.tags xtradbrowmarket.market.edit.tags.php market.edit.tags 

5 market.edit.update.done xtradbrowmarket.market.edit.update.done.php market.edit.update.done 

6 market.tags xtradbrowmarket.market.tags.php market.tags 

7 markettags xtradbrowmarket.markettags.php markettags.main

## Step‑by‑step installation and usage

1. **Download the plugin**
2. Upload the `xtradbrowmarket` folder into the plugins directory so that the file `xtradbrowmarket.setup.php` is located at:
   
   ```markdown
   /plugins/xtradbrowmarket/xtradbrowmarket.setup.php
   ```
   
   After installation, if there were no errors, the note will immediately contain a link
   
   ```markdown
   After installing the plugin, open the extra fields of the Custom Extrafields Market plugin.
   ```
   
   Or navigate via:
   
   ```markdown
   Administration → Extras → Extra fields → Table cot_xtradbrowmarket - Custom Extrafields Market
   ```
   
   browser link
   
   ```ini
   https://cotonti.local/admin/extrafields?n=cot_xtradbrowmarket
   ```
   
   This is the very heart of your plugin — the 15 pre‑installed demonstration extra fields will give you a complete picture of which extra field type suits which scenario and application.
3. **Open the product edit template** — it is `market.edit.tpl`. Its correct "geolocation":
   
   ```php
   /themes/index36/modules/market/market.edit.tpl
   ```
   
   and before the "Yes"/"No" radio buttons for product deletion insert the following code:
   
   ```php
   <!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
   <div class="card mb-4">
       <div class="card-header">
           <h4>{PHP.L.xtradbrowmarket_edittpl_dynamic_title}</h4>
       </div>
       <div class="card-body">
           <!-- BEGIN: XTRA_EXTRAFLD -->
           <div class="form-group mb-3">
               <label>{MARKETEDIT_FORM_XTRA_EXTRAFLD_TITLE}</label>
               {MARKETEDIT_FORM_XTRA_EXTRAFLD}
           </div>
           <!-- END: XTRA_EXTRAFLD -->
       </div>
   </div>
   <!-- ENDIF -->
   ```
   
   Use the Tab key to indent it properly, then save the file. Then open any product for editing, for example
   
   ```php
   https://cotonti.local/market/1165?m=edit
   ```
   
   As mentioned earlier, you should now see all fields before the "Yes"/"No" delete buttons (and right after installation there are 15 of them). Now fill in all fields with some random data — don't be afraid to trust your intuition. Save the product and immediately return to check what was saved in your fields. If everything is "ok", proceed to edit the product detail template.
4. **Template: market.tpl**  
   Open the product detail template (its full detail page) — it is `market.tpl`. Its correct "geolocation":
   
   ```php
   /themes/index36/modules/market/market.tpl
   /themes/index36/modules/market/market.category-name.tpl
   ```
   
   Find the product title:
   
   ```html
   <h1 class="h4 mb-3">
       <!-- IF {PHP.item.fieldmrkt_product_status} == 'instock' -->
       <span class="px-2 fw-bold bg-success text-white rounded-2">{MARKET_PRODUCT_STATUS}</span>
       <!-- ENDIF -->
       <!-- IF {PHP.item.fieldmrkt_product_status} == 'onorder' -->
       <span class="fw-bold text-warning-hot">{MARKET_PRODUCT_STATUS}</span>
       <!-- ENDIF -->
   {MARKET_TITLE}
   </h1>
   ```
   
   and immediately after it, or wherever you like, insert the following code:
   
   ```html
   <!-- IF {PHP.usr.maingrp} == 5 -->
   <!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
   <div class="card mb-4">
   	<div class="card-header">
   		<h4 class="mb-3">{PHP.L.xtradbrowmarket_pagetpl_custom_title}</h4>
   		<small class="mb-3">{PHP.L.xtradbrowmarket_pagetpl_custom_desc}</small>
   	</div>
   	<div class="card-body">
   		
   		<!-- event_name (input) -->
   		<!-- IF {MARKET_XTRA_EVENT_NAME} -->
   		<div class="mb-3">
   			<i class="fa-regular fa-calendar-check me-2 text-primary"></i>
   			<strong>{MARKET_XTRA_EVENT_NAME_TITLE}:</strong>
   			<span class="fw-semibold">{MARKET_XTRA_EVENT_NAME}</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- event_description (textarea) with tag stripping and truncation -->
   		<!-- IF {MARKET_XTRA_EVENT_DESCRIPTION} -->
   		<div class="mb-3 p-3 bg-light rounded">
   			<h6 class="fw-bold"><i class="fa-solid fa-align-left me-1"></i> {MARKET_XTRA_EVENT_DESCRIPTION_TITLE}</h6>
   			<p class="mb-0">
   				{MARKET_XTRA_EVENT_DESCRIPTION_VALUE|strip_tags($this)|mb_substr($this, 0, 150, 'UTF-8')}...
   			</p>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- event_start (datetime) – formatting via cot_date -->
   		<!-- IF {MARKET_XTRA_EVENT_START} -->
   		<div class="mb-3">
   			<i class="fa-regular fa-clock me-2 text-warning"></i>
   			<strong>{MARKET_XTRA_EVENT_START_TITLE}:</strong>
   			<span class="badge bg-warning text-dark">
   				{MARKET_XTRA_EVENT_START_VALUE|cot_date('d.m.Y H:i', $this)}
   			</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- event_ticketprice (double) -->
   		<!-- IF {MARKET_XTRA_EVENT_TICKETPRICE} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-dollar-sign me-2 text-success"></i>
   			<strong>{MARKET_XTRA_EVENT_TICKETPRICE_TITLE}:</strong>
   			<!-- IF {MARKET_XTRA_EVENT_TICKETPRICE_VALUE} == '0' -->
   			<span class="badge bg-success">Free</span>
   			<!-- ELSE -->
   			<span class="fs-3 fw-bold">
   				{MARKET_XTRA_EVENT_TICKETPRICE_VALUE} $
   			</span>
   			<!-- ENDIF -->
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- event_seson (select) – nested IFs instead of ELSEIF -->
   		<!-- IF {MARKET_XTRA_EVENT_SESON} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-cloud-sun me-2 text-success"></i>
   			<strong>{MARKET_XTRA_EVENT_SESON_TITLE}:</strong>
   			<!-- IF {MARKET_XTRA_EVENT_SESON_VALUE} == 'winter' -->
   			<span>❄️ Winter</span>
   			<!-- ELSE -->
   			<!-- IF {MARKET_XTRA_EVENT_SESON_VALUE} == 'spring' -->
   			<span>🌱 Spring</span>
   			<!-- ELSE -->
   			<!-- IF {MARKET_XTRA_EVENT_SESON_VALUE} == 'summer' -->
   			<span>☀️ Summer</span>
   			<!-- ELSE -->
   			<!-- IF {MARKET_XTRA_EVENT_SESON_VALUE} == 'autumn' -->
   			<span>🍂 Autumn</span>
   			<!-- ELSE -->
   			<span class="text-capitalize">{MARKET_XTRA_EVENT_SESON}</span>
   			<!-- ENDIF -->
   			<!-- ENDIF -->
   			<!-- ENDIF -->
   			<!-- ENDIF -->
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_int (inputint) -->
   		<!-- IF {MARKET_XTRA_DEMO_INT} -->
   		<div class="mb-3">
   			<span class="fa-stack fa-sm me-2">
   				<i class="fa-solid fa-circle fa-stack-2x text-secondary"></i>
   				<i class="fa-solid fa-hashtag fa-stack-1x fa-inverse"></i>
   			</span>
   			<strong>{MARKET_XTRA_DEMO_INT_TITLE}:</strong>
   			<span class="badge bg-secondary">{MARKET_XTRA_DEMO_INT}</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_double (double) -->
   		<!-- IF {MARKET_XTRA_DEMO_DOUBLE} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-dollar-sign me-2 text-success"></i>
   			<strong>{MARKET_XTRA_DEMO_DOUBLE_TITLE}:</strong>
   			<!-- IF {MARKET_XTRA_DEMO_DOUBLE_VALUE} == '0.00' -->
   			<span class="text-muted">not specified</span>
   			<!-- ELSE -->
   			{MARKET_XTRA_DEMO_DOUBLE_VALUE}
   			<!-- ENDIF -->
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_select (select) -->
   		<!-- IF {MARKET_XTRA_DEMO_SELECT} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-list me-2 text-info"></i>
   			<strong>{MARKET_XTRA_DEMO_SELECT_TITLE}:</strong>
   			<span class="badge bg-info text-dark">{MARKET_XTRA_DEMO_SELECT}</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_radio (radio) – value check inside IF -->
   		<!-- IF {MARKET_XTRA_DEMO_RADIO} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-circle-dot me-2 text-secondary"></i>
   			<strong>{MARKET_XTRA_DEMO_RADIO_TITLE}:</strong>
   			<!-- IF {MARKET_XTRA_DEMO_RADIO_VALUE} == 'Yes' -->
   			<span class="text-success fw-bold">Yes</span>
   			<!-- ELSE -->
   			<span class="text-danger fw-bold">No</span>
   			<!-- ENDIF -->
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_datetime (datetime) -->
   		<!-- IF {MARKET_XTRA_DEMO_DATETIME} -->
   		<div class="mb-3">
   			<i class="fa-regular fa-calendar me-2 text-danger"></i>
   			<strong>{MARKET_XTRA_DEMO_DATETIME_TITLE}:</strong>
   			<span class="text-muted">
   				{MARKET_XTRA_DEMO_DATETIME_VALUE|cot_date('d.m.Y H:i', $this)}
   			</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_file (file)  -->
   		<!-- IF {MARKET_XTRA_DEMO_FILE} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-paperclip me-2"></i>
   			<strong>{MARKET_XTRA_DEMO_FILE_TITLE}:</strong>
   			<a href="{PHP.cfg.mainurl}/datas/exflds/xtradbrowmarket/{MARKET_XTRA_DEMO_FILE}" target="_blank">
   				{MARKET_XTRA_DEMO_FILE}
   			</a>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_country (country) -->
   		<!-- IF {MARKET_XTRA_DEMO_COUNTRY} -->
   		<div class="mb-3">
   			<img src="images/flags/{MARKET_XTRA_DEMO_COUNTRY_VALUE}.svg"
   			style="width:24px;height:auto;" class="me-2" alt="">
   			<strong>{MARKET_XTRA_DEMO_COUNTRY_TITLE}:</strong>
   			<span>{MARKET_XTRA_DEMO_COUNTRY}</span>    <span>{MARKET_XTRA_DEMO_COUNTRY_NAME}</span>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_range (range) – progress bar with real value -->
   		<!-- IF {MARKET_XTRA_DEMO_RANGE} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-sliders me-2" style="color:#6f42c1;"></i>
   			<strong>{MARKET_XTRA_DEMO_RANGE_TITLE}:</strong>
   			<div class="progress mt-1" style="height:20px;">
   				<div class="progress-bar bg-info" role="progressbar"
   				style="width:{MARKET_XTRA_DEMO_RANGE_VALUE}%;"
   				aria-valuenow="{MARKET_XTRA_DEMO_RANGE_VALUE}" aria-valuemin="0" aria-valuemax="100">
   					{MARKET_XTRA_DEMO_RANGE_VALUE}%
   				</div>
   			</div>
   		</div>
   		<!-- ENDIF -->
   		
   		<!-- demo_checklistbox (checklistbox) -->
   		<!-- IF {MARKET_XTRA_DEMO_CHECKLISTBOX} -->
   		<div class="mb-3">
   			<i class="fa-solid fa-check-double me-2 text-primary"></i>
   			<strong>{MARKET_XTRA_DEMO_CHECKLISTBOX_TITLE}:</strong>
   			<span class="text-muted">{MARKET_XTRA_DEMO_CHECKLISTBOX}</span>
   		</div>
   		<!-- ENDIF -->
   		
   	</div>
   </div>
   <!-- ENDIF -->
   <!-- ENDIF -->
   ```
   
   Please note, here I wrap the plugin activity check in a conditional "frame":
   
   ```php
   <!-- IF {PHP.usr.maingrp} == 5 -->
   <!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
   <div class="card mb-4">
       ... bla-bla-bla ....
   </div>
   <!-- ENDIF -->
   <!-- ENDIF -->
   ```
   
   – it will be shown only to the main administrator, and only if the plugin is active. This is mainly useful while you are pushing something to production (a live site). Once you have "fine‑tuned" that log for yourself, you can calmly remove the frame:  
   `<!-- IF {PHP.usr.maingrp} == 5 -->` – remove this line, which declares the condition (show to admin), leaving all the inner code intact  
   `<!-- ENDIF -->` – remove this line, which closes the condition (show to admin).
5. **Template: market.list.tpl**  
   Open the product list template in a category, categories, or without any at all — it is `market.list.tpl`. Its correct "geolocation":
   
   ```php
   /themes/index36/modules/market/market.list.tpl
   /themes/index36/modules/market/market.list.category-name.tpl
   ```
   
   Scroll to the product loop block
   
   ```php
   <div class="row row-cols-1 row-cols-xl-3 row-cols-lg-2 row-cols-md-1 g-3 g-lg-4" id="market-items-container">
       <!-- BEGIN: LIST_ROW -->
       <div class="col">
        ...
       </div>
       <!-- END: LIST_ROW -->
   </div>
   ```
   
   And, for instance, immediately after the link to the product detail
   
   ```php
   <h5 class="card-title mb-2">
       <a href="{LIST_ROW_URL}" class="text-decoration-none">{LIST_ROW_TITLE}</a>
   </h5>
   ```
   
   add the following code:
   
   ```php
   <!-- IF {PHP|cot_plugin_active('xtradbrowmarket')} -->
   
   <!-- just as an example – country of origin -->
   <!-- IF {LIST_ROW_XTRA_DEMO_COUNTRY} -->
   <div class="mb-3">
       <img src="images/flags/{LIST_ROW_XTRA_DEMO_COUNTRY_VALUE}.svg"
            style="width:24px;height:auto;" class="me-2" alt="">
       <strong>{LIST_ROW_XTRA_DEMO_COUNTRY_TITLE}:</strong>
       <span>{LIST_ROW_XTRA_DEMO_COUNTRY}</span>    <span>{LIST_ROW_XTRA_DEMO_COUNTRY_NAME}</span>
   </div>
   <!-- ENDIF -->
   
   <!-- just as an example – date of market release -->
   <!-- IF {LIST_ROW_XTRA_EVENT_START} -->
   <div class="mb-3">
       <strong>{LIST_ROW_XTRA_EVENT_START_TITLE}:</strong>
       <span class="text-primary"><i class="fa-regular fa-alarm-clock fa-xl"></i></span>
       <span class="ms-2 fw-bold text-danger">{LIST_ROW_XTRA_EVENT_START}</span>
   </div>
   <!-- ENDIF -->
   
   <!-- ENDIF -->
   ```
   
   Save the file, then go to the product list and see what comes out (check the screenshots).

## Displaying custom extra fields in header.tpl

Its correct "geolocation":

```php
/themes/index36/header.tpl
```

The goals of such an implementation can be very diverse; the main thing is that we have a flexible and sufficiently simple way to solve specific tasks — for example, to tell search engines something individual about each product on our site.

Ideally, of course, you would want to have a dedicated "site header" for products — [**a custom template**](https://abuyfile.com/ru/cotonti/authorial-plugins/pagemycatheader) `header.market.tpl` or even one for each desired category — `header.market.notebook.tpl`, `header.market.mobile-phones.tpl`, etc. In Cotonti you can actually have something like this:

```php
/themes/index36/header.tpl – common header
/themes/index36/header.list.tpl – header for the article list
/themes/index36/header.page.news.tpl – header for the full page of an article from the "news" category
/themes/index36/header.market.tpl – common header for the "market" module
/themes/index36/header.market.notebook.tpl – header for the product card in the "Notebooks" category
```

Of course, this is configured separately and is a topic for another discussion, but if you ask, "... and why bother so much? the simpler the better!"

And you will be quite right, but very "short‑sightedly"! For a "local" perspective — 100% correct, but for a regional or national one — not worth a pint of even the worst beer! Why?

### A lyrical digression about differentiation

On an operational level, I certainly agree — the simpler we make our `header.market.notebook.tpl` tailored to that specific product category, the better. However, you will never manage to make a simple and universal `header.tpl` that fits all tasks. Someone already tried — I'm talking about Henry Ford with his famous mass‑market Model T. That automobile was simple, reliable, and undeniably dominating, but not for long and only in the segment of affordable cars. The Model T was intended only for the masses, something very quantitative and highly standardized. But a successful businessman, an enterprising gangster, or a powerful politician obsessed with his own importance would never want to associate himself — a "potent, powerful" individual — with the masses and something standard, ordinary! Such a successful person did not need a car merely as a means of transportation; he needed a car that would emphasize his achievements, status, and success. And here, owning a simple and cheap Model T actually damaged his image, lowering the owner’s perceived success, or it simply did not meet already standard technical requirements — off‑road capability, suspension, etc. Therefore, with the arrival of the "Chevrolet Superior", "Cadillac Type 51", "Cadillac V‑63", many people started selling their Model Ts and buying "what truly matched them". That is exactly how Ford's dominance in the automobile market collapsed.

The year 1926. Three models, three prices:  
Ford Model T (Runabout): $260  
Chevrolet Superior Series V (Roadster): $510  
Cadillac V‑63 (Touring): $3085

Even before the concept of "Positioning" appeared, the market was already shouting the well‑known words of Jack Trout, uttered in 1992 — "Differentiate or Die". You cannot take a simple, universally streamlined management model that works for one successful city and apply it to every city or town in a region, let alone to every city in a nation. We don’t build a cosmodrome, a genetics institute, a cowshed, and a confectionery factory in every city, do we? That is why simplification should happen at the local, municipal level and within the framework of an overall strategy.

There, I got carried away.  
Let’s return to our stubborn folks who are allergic to anything colorful. I think I have demonstrated clearly enough why a single super‑duper‑simple `header.tpl` for the whole site will never solve the small tasks required to achieve strategic goals, and of course I very much hope that if you are a site owner, you definitely have such goals, for instance:

- in a year, re‑orient the content to a specific audience within a physically tangible geography;
- in six months, clean up the articles in the "blog" section from markup garbage that I, through inexperience and stupidity, copied from other people's sites;
- alongside the product showcase, organize and launch in a forced mode a forum with support for buyers and users who have already purchased or might purchase products in my store in the future, etc.

This is where one of the highlights of Cotonti becomes necessary — the ability to differentiate `header.tpl` for different "regions" and "cities".

By the way, this is another example that Cotonti is not some OpenCart or WordPress, which are as simple as the Model T — you put it in the garage and forget about it, as long as there is a car )))). But if you are an active developer and orders keep pouring in, you will often have in mind: "\*\*\*b\*chy jalopy" — you need to tune it or upgrade it, and on top of that with the client's whims — here you "nudge" something, there something "falls off", and when it comes to the estimate — the Client is in frank horror — "but it's free, isn't it?"...

### A practical example for header.market.notebook.tpl

I digressed again. Let's open:

```php
/themes/index36/header.market.notebook.tpl
```

here it is:

```php
<!--
    /********************************************************************************
    * File: header.tpl
    * Extension: Core'
    * Description: HTML template for header.tpl.
    * Compatibility: CMF/CMS Cotonti Siena v0.9.26[](https://github.com/Cotonti/Cotonti)
    * Dependencies:
    *        Bootstrap 5.3.+[](https://getbootstrap.com/);
    *        Font Awesome Free 7.1[](https://fontawesome.com/)
    * Theme: Index36
    * Version: 1.0.2
    * Created: 01 Feb 2026
    * Updated: 22 Apr 2026
    * Copyright (c) 2026 webitproff | https://github.com/webitproff
    * Source: https://github.com/webitproff/index36-cotonti-theme
    * Demo : https://freelance-script.abuyfile.com/
    * Help and support: https://abuyfile.com/ru/forums/cotonti/original/skins/index36
    * License: BSD (Free distribution with saving Copyright (c) 2026 webitproff)
    ********************************************************************************/
-->
<!-- BEGIN: HEADER -->
<!DOCTYPE html>
    <!-- IF {HTML_LANG} -->
    <html lang="{HTML_LANG}" data-bs-theme="light">
    <!-- ELSE -->
    <html lang="{PHP.usr.lang}" data-bs-theme="light">
    <!-- ENDIF -->
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- IF {I18N_HEADER_META_TITLE} -->
        <title>{I18N_HEADER_META_TITLE}</title>
        <!-- ELSE -->
        <title>{HEADER_TITLE}</title>
        <!-- ENDIF -->
    <!-- IF {I18N_HEADER_META_DESCRIPTION} -->
        <meta name="description" content="{I18N_HEADER_META_DESCRIPTION}" />
    <!-- ELSE -->
        <!-- IF {HEADER_META_DESCRIPTION} -->
        <meta name="description" content="{HEADER_META_DESCRIPTION}" />
        <!-- ENDIF -->
    <!-- ENDIF -->
        <!-- IF {HEADER_BASEHREF} -->
        {HEADER_BASEHREF}
        <!-- ENDIF -->
        <!-- IF {HEADER_CANONICAL_URL} -->
        <link rel="canonical" href="{HEADER_CANONICAL_URL}" />
        <!-- ENDIF -->
        <!-- IF {ALTERNATE_TAGS} -->
        {ALTERNATE_TAGS}
        <!-- ENDIF -->
        <link rel="shortcut icon" href="favicon.ico" />
        <link rel="icon" href="{PHP.cfg.themes_dir}/{PHP.theme}/img/icon.webp" type="image/svg+xml">
        <link rel="apple-touch-icon" href="apple-touch-icon.png" />
        <!-- IF {PHP.out.meta} -->
        {PHP.out.meta}
        <!-- ENDIF -->
        <script>
            (function () {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const defaultTheme = storedTheme || (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-bs-theme', defaultTheme);
            })();
        </script>
        {HEADER_HEAD}
    </head>
    <body class="sidebar-closed">
        <header class="navbar navbar-expand-lg shadow-sm fixed-top" style="background-color: var(--bs-header-bg);" data-bs-theme="inherit">
               ....
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <!-- BEGIN: I18N_LANG -->
                    <div class="btn-group">
                        <button type="button" class="dropdown-toggle btn nav-link d-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-language me-2"></i>
                            <!-- IF {PHP.i18n_locale} == 'ru' -->RU<!-- ENDIF -->
                            <!-- IF {PHP.i18n_locale} == 'cn' -->CN<!-- ENDIF -->
                            <!-- IF {PHP.i18n_locale} == 'en' -->EN<!-- ENDIF -->
                            <!-- IF {PHP.i18n_locale} == 'ua' -->UA<!-- ENDIF -->
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- BEGIN: I18N_LANG_ROW -->
                            <li><a class="dropdown-item" href="{I18N_LANG_ROW_URL}">{I18N_LANG_ROW_TITLE}</a></li>
                            <!-- END: I18N_LANG_ROW -->
                        </ul>
                    </div>
                    <!-- END: I18N_LANG -->

            ...
        </header>

        <aside class="main-sidebar">
            ...
        </aside>

        <div class="expanded-panels ps-container">
        ...
        </div>
        <main>
<!-- END: HEADER -->
```

I'm showing a working example specifically of field output, not the semantic load they carry in the examples, so you can combine them as you like. Find:

```php
<title>{HEADER_TITLE}</title>
```

replace with

```php
<title>{HEADER_TITLE} <!-- IF {MARKET_HEADER_XTRA_DEMO_COUNTRY} -->. {MARKET_HEADER_XTRA_DEMO_COUNTRY_NAME}<!-- ENDIF --></title>
```

Instead of

```php
<title>Super awesome product</title>
```

the browser will display

```php
<title>Super awesome product. China</title>
```

then, find

```php
<!-- IF {HEADER_META_DESCRIPTION} --><meta name="description" content="{HEADER_META_DESCRIPTION}<!-- ENDIF -->" />
<!-- ENDIF -->
```

replace with

```php
<!-- IF {HEADER_META_DESCRIPTION} --><meta name="description" content="{HEADER_META_DESCRIPTION}<!-- IF {MARKET_HEADER_XTRA_EVENT_START} -->  • {MARKET_HEADER_XTRA_EVENT_START_TITLE} {MARKET_HEADER_XTRA_EVENT_START}<!-- ENDIF -->" />
<!-- ENDIF -->
```

and now instead of

```php
<meta name="description" content="Buy the product with the coolest AI-generated description" />
```

we will get

```php
<meta name="description" content="Buy the product with the coolest AI-generated description • Promotional sale only with us from 27.05.2026 10:10" />
```

 

Don't forget how the market punished Ford for refusing to change.

You cannot build a cosmodrome in every city — a management model must adapt to local conditions. From the general to the specific. The same applies to a website: each category or specific task needs its own "tuned" tool or template, while maintaining the overall strategy. Cotonti provides this flexibility, which distinguishes it favorably from simple "boxed" CMSs.

Extrafields Market Custom is a small toolbox that lets you place exactly what you need and where you need it — integrate metadata or any content into the system through truly simple output of extra fields.


 **[ReadMeMore](https://abuyfile.com/ru/market/cotonti/plugs/extrafields-market-custom)**
 
 **[Support](https://abuyfile.com/ru/forums/cotonti/original/extrafields)**
 
 **[API Extrafields](https://github.com/Cotonti/Cotonti/blob/master/system/extrafields.php)**

