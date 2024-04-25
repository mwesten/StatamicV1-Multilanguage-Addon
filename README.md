> [!CAUTION]
> **This repository is archived**  
> The plugin is witten for Statamic v1; way back and is not maintained and not supported anymore.

Statamic v1 multilanguage plugin v0.1
=====================================

# Introduction
**This version is only tested with Statamic 1.11.2**

Statamic v1 wasn't built to support Localisation and Internationalisation. To build a multilanguage site with it, you could use some tricks, like the very useful blogpost by [Katrin Kerber](http://katrinkerber.com): [Building a Multilingual Site with Statamic](http://katrinkerber.com/notes/building-a-multilingual-site-with-statamic).

Some of these principles were put in this addon that can be called troughout your templates.

For basic plugin use, read the following chapters; for the complete Multilanguage setup, read the chapter __"Multilanguage Setup"__ further below.


# Multilanguage Addon

## Installation
### Clone or Copy the files to their destination
Clone this project on your system:

```bash
cd webfolder/_add-ons
git clone git://github.com/mwesten/Plugin-Multilanguage.git multilanguage
```

Or download the project and add the contents of archive to the `_add-ons/multilanguage` folder.


### Usage

#### TAG: `{{ multilanguage:lang }}` 
Returns the 2 letter language-code based on current language

Checks if the segment_1 contains one of the accepted language parts.

* If it does, it returns that part of the url.
* If it doesn't, it returns the default language part

This can be used to dynamically include partials for the current language. This way your layouts/templates are mostly uniform (one for all languages) and all localised content is included with different partials.

**Example:**
Include a localized breadcrumb on each page.
In the page-template, add `{{ theme:partial src="{ multilanguage:lang }/footer-breadcrumb" }}` and create a language folder for each language-code in `_theme\yourtheme\partials\` then add a file `footer-breadcrumb.html` in each folder and add the localized content there.

The corresponding localized content will be shown according to the language you are viewing.

#### TAG: `{{ multilanguage:url }}` 

Same as `{{ multilanguage:lang }}`, but with a slash in front of the language, so it can be used in URL's immediately.

**Example:**
When emptying the BuiltWithBison cart
`{{ bison:empty_cart return="{ multilanguage:url }/cart" }}`


#### TAG: `{{ multilanguage:switcher }}` 
Inserts a language switcher.
Can switch to a corresponding page in another language, an entry in another language, a BuiltWithBison product in another language, or just to the other language homepage.

I make the assumption that the setup used is implemented as explained below in the chapter "Multilanguage Setup"; especially the fieldsets in use to identify:
* the current page/entry in another language: `alternative_url_[+ 2 char language code]` ie: **alternative\_url\_en**
* the current product in another language: `alternative_prod_url_[+ 2 char language code]` ie: **alternative\_prod\_url\_en**

If you are on a page that contains the `{{ multilanguage:switcher }}` and no other special items like the aforementioned fieldsets, the switcher switches to the homepage of the other languages.

If the field `alternative_url_de` and `alternative_url_fr` are available, the language switcher switches to the page specified in the corresponding field.

If the field `alternative_prod_url_de` and `alternative_prod_url_fr` are available, the language switcher switches to the product-page specified in the corresponding field.

**Example:**

I include the partial `lang_switch` in my main navigation. The partial includes the following code:

```html
{{ multilanguage:switcher }}
  <ul>
    {{ languages }}
        {{ if is_current }}
        
        {{ elseif produrl }}
        <li>
            <a href="{{ produrl }}">{{ text }}</a>
        </li>
        {{ elseif alturl }}
        <li>
            <a href="{{ alturl }}">{{ text }}</a>
        </li>
        {{ else }}
        <li>
            <a href="{{ url }}">{{ text }}</a>
        </li>
        {{ endif }}
    {{ /languages }}
  </ul>
{{ /multilanguage:switcher }}
```

It now displays the language-switcher for the languages that are defined in `ml_languages`. The text that is shown is defined in `ml_switch_text`.


#### TAG: `{{ multilanguage:get_locale }}`
Inserts the ISO locale string, based on the language the current page is in. The locale returned can be specified in the config-file under the setting `ml_locales`. This could be used in the metatags for example.

For Belgium, one could use the following:

``` yaml
ml_locales:
  en: en_GB
  nl: nl_BE # Dutch spoken in Belgium (Flemish)
  fr: fr_BE # French spoken in Belgium
```

While in The Netherlands they could use the following:

``` yaml
ml_locales:
  en: en_GB # English spoken in Great Brittain
  nl: nl_NL # Dutch spoken in The Netherlands
  fr: fr_FR # French spoken in France
```


#### TAG: `{{ multilanguage:getcurrentwithlang }}`
This tag can be used to redirect a site-url that doesn't have the language identifier in the URL, to the same URL with a language identifier.
This is useful when using external functionality that redirects to a predefined URL, for example after a successful login, you'll be redirected to the url `sitename.com/member`. 

**Example:**

If you are using the members module, you could add the following routes to your `_config\routes.yaml`to redirect all the member pages to the `member/redirect` template.

``` yaml
routes:
  /member : member/redirect
  /member/login : member/redirect
  /member/update : member/redirect
  /member/register : member/redirect
  /member/dashboard :  member/redirect
  /member/orderhistory :  member/redirect
  /member/forgot-password :  member/redirect
  /member/reset-password :  member/redirect
  /member/email-sent :  member/redirect

  /en/member :  member/en/dashboard
  /en/member/login : member/en/login
  /en/member/update: member/en/update
  /en/member/register : member/en/register
  /en/member/dashboard :  member/en/dashboard
  /en/member/orderhistory :  member/en/orderhistory
  /en/member/forgot-password :  member/en/forgot_password
  /en/member/reset-password :  member/en/reset_password
  /en/member/email-sent :  member/en/email_sent

  /de/member :  member/de/dashboard
  /de/member/login : member/de/login
  /de/member/update: member/de/update
  /de/member/register : member/de/register
  /de/member/dashboard :  member/de/dashboard
  /de/member/orderhistory :  member/de/orderhistory
  /de/member/forgot-password :  member/de/forgot_password
  /de/member/reset-password :  member/de/reset_password
  /de/member/email-sent :  member/de/email_sent

```

Then in `_theme\yourtheme\templates\member\redirect.html` add the following lines

```html
<!-- REDIRECT CURRENT PAGE TO THE SAME PAGE WITH LANGUAGE ADDED -->
{{ multilanguage:getcurrentwithlang }}
```
When a German user logs in, he gets redirected to the `yoursite.com/member` page wich in turn gets 
redirected to `yoursite.com/de/member`. It then uses the template `_theme\yourtheme\templates\member\de\dashboard`.


#### TAG: `{{ multilanguage:autoselect }}` 
This tag detects the preferred language specified by your browser and checks if that language 
is available on the site. If it's available, you'll be redirected to that language folder, else it
defaults to the language specified in `ml_default_language`.

**Example:**
Create a `_content/page.md` with the following content:

```markdown
---
title: Home
_template: languageredirect
---
Redirects the root homepage to the language homepage.
```

Then create a `_theme\yourtheme\templates\languageredirect.html` with the following content:

```markdown
<!-- REDIRECT ROOT PAGE TO LANGUAGE-PAGE -->
{{ multilanguage:autoselect }}
```

When calling `yoursite.com`, you now get redirected to the language set in your browser, 
or to the default-language `ml_default_language` set in the config file.

# Multilanguage Setup

## Basic setup
Create the following `_content` structure if we want to use the languages English, German and French:

```
_content
  01-en
    01-about
    02-blog
    03-products
    page.md /* English homepage */
  02-de
    01-ueber
    02-blog
    03-producte
    page.md /* German homepage */
  03-nl
    01-over
    02-blog
    03-producten
    page.md /* Dutch homepage */
  page.md /* Calls the browser-detected language, or defaults to the predefined default language */

```

Right now you can call the corresponding language URL and get the corresponding language pages by calling one of the urls:

```plain
http://mysite.com/en  
http://mysite.com/de  
http://mysite.com/nl  
```

## Link pages
Link equivalent pages in different languages together. The language-switcher will switch to that page immediately instead of going to the other language-homepage.
This linking can be done with suggest-fieldsets

``` yaml
fields:
  alternative_url_en:
    display: Alternative English page/URL
    type: suggest
    max_items: 1
    required: false
    allow_blank: true
    content:
      folder: en*
      type: pages
      label: url
      value: url
      show_hidden: true
      show_drafts: true
  alternative_url_de:
    display: Alternative German page/URL
    type: suggest
    max_items: 1
    required: false
    allow_blank: true
    content:
      folder: de*
      type: pages
      label: url
      value: url
      show_hidden: true
      show_drafts: true
  alternative_url_nl:
    display: Alternative Dutch page/URL
    type: suggest
    max_items: 1
    required: false
    allow_blank: true
    content:
      folder: nl*
      type: pages
      label: url
      value: url
      show_hidden: true
      show_drafts: true
```


## Setting the document language 
Add th document language to the `html` tag.

```html
<html lang="{{ multilanguage:lang }}">
```

## Define alternative URLs
Add alternate links in the head section, so search-engines and othr applications know what resuts they should show you.

```html
{{ multilanguage:switcher }}
  {{ languages }}
    {{ if is_current }}
    <link rel="alternate" hreflang="{{ code }}" href="{{ current_url }}">
    {{ elseif produrl }}
    <link rel="alternate" hreflang="{{ code }}" href="{{ produrl }}">
    {{ elseif alturl }}
    <link rel="alternate" hreflang="{{ code }}" href="{{ alturl }}">
    {{ else }}
    <link rel="alternate" hreflang="{{ code }}" href="{{ url }}">
    {{ endif }}
  {{ /languages }}
{{ /multilanguage:switcher }}
```

## Sitemap
To help search engines index all pages on your site, you use a sitemap.
My sitemap looks like this:

The file `_content\sitemap.md`:

```yaml
---
title: Sitemap
_type: xml
_layout: sitemap
_template: sitemap
_admin:
  hide: true
---
```

The file `_theme\yourtheme\layouts\sitemap.html`:

```html
{{ _xml_header }}
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"  xmlns:xhtml="http://www.w3.org/1999/xhtml">
    {{ layout_content }}
  </urlset>
```
`_theme\yourtheme\partials\`

The file `_theme\yourtheme\templates\sitemap.html`:

```html
{{# Home pages #}}
{{ get_content from="/en" }}
<url>
  <loc>{{ _site_url }}</loc>
  <xhtml:link rel="alternate" hreflang="en" href="{{ _site_url }}/en" />
  <xhtml:link rel="alternate" hreflang="nl" href="{{ _site_url }}/nl" />
  <lastmod>{{ last_modified format="Y-m-d" }}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.7</priority>
</url>
{{ /get_content }}

{{# Subpages #}}
{{ nav from="/en" max_depth="5" include_content="true" folders_only="false" }}
<url>
  <loc>{{ _site_url }}{{ url }}</loc>
  <xhtml:link rel="alternate" hreflang="en" href="{{ _site_url }}{{ url }}" />
  {{ if alternative_url_nl }}
  <xhtml:link rel="alternate" hreflang="nl" href="{{ _site_url }}{{ alternative_url_nl }}" />
  {{ endif }}
  <lastmod>{{ last_modified format="Y-m-d" }}</lastmod>
  <changefreq>monthly</changefreq>
  <priority>1.0</priority>
</url>
{{ if children }}
  {{ *recursive children* }}
{{ endif }}
{{ /nav }}
{{# Products #}}
{{ entries:listing folder="/products/en/pendants" sort_by="number" }}
<url>
  <loc>{{ _site_url }}{{ url }}</loc>
  <xhtml:link rel="alternate" hreflang="en" href="{{ _site_url }}/en/pendants/{{ alternative_prod_url_en }}" />
  {{ if alternative_prod_url_nl }}
  <xhtml:link rel="alternate" hreflang="nl" href="{{ _site_url }}/nl/hangers/{{ alternative_prod_url_nl }}" />
  {{ endif }}
  <lastmod>{{ last_modified format="Y-m-d" }}</lastmod>
  <changefreq>weekly</changefreq>
  <priority>1.0</priority>
</url>
{{ /entries:listing }}
```

# Disclaimer
I've 'written' this plugin for my own use. It comes **without any guarantee**, so your mileage 
may vary in using it. If you find bugs or have great additions you'd like to share, use github 
to fork the project and share your improvements by initiating pull requests.


