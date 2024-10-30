=== Master Currency WP ===
Contributors: wibergsweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=93LWBQF7LY6SA
Tags: currency, currencies, convert, converter, conversion, form, list, post, page, ecb, european central bank, currencyconversion, ajax
Requires at least: 3.0.1
Tested up to: 5.6.2
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert currencies in form(s), list or single conversion(s) in a page/post. No programming skills required! Fetches rates from European Central Bank.

== Description ==

Master Currency WP makes it very easy to create currency converter forms, list and single conversions with shortcodes. No programming skills are required to create these forms, lists and single
conversions.  Focus has been that the user easily could add just any conversion of currencie(s) to his/her page without entering a single line of code, but at the same time offer real 
good flexibility with a very well thought html-structure so any developer could easily apply his/her layout. The plugin is of course created with an objectoriented approach 
so it's fairly easy to extend functionality. If you do miss some functionality, please tell in the support forum!

If you like the plugin, please consider donating.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin folder master-currency-wp to the `/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Put shortcode(s) on the Wordpress post or page you want to display it on and add css to change layout for those.

= Shortcodes =
* [mcwp_updateddate] - shows the last updated date for the rates at the ECB (European Central Bank)
* [mcwp_currencyconverterform] - shows a converter form with amount, from currency (select from different currencies) and to currency (select from different currencies).
* [mcwp_currencylist] - shows a list of currency-pairs (eg. 1USD=x GBP, 1EUR = x USD etc)
* [mcwp_updateddate] has no attributes
* [mcwp_currencyconvert] shows single conversion(s) in post and/or page

= [mcwp_currencyconverterform] attributes =
* html_id - set id of this form
* input_type - text or number(number type = html5)
* default_amount - default amount to show when form is loaded. If there are several forms on the page/post, the last shortcode on the page/post sets the current default amount.
* amount_title - label that shows at the amount
* from_title -label that shows at the from currency-select-list
* to_title - Label that shows at the to currency-select-list
* calculatebutton_title - label of the button (that is used for calcuting the rate)
* show_currencydescription - if yes, description of the currency would be shown in the select-list (EUR - euro). If no then only the currency would be shown in the select-lists (EUR)
* currencies - if given, include list of currencies given (USD,EUR,GSP) in select-lists
* default_fromcurrency - if given use this currency as default from currency when loading post/page first time
* default_tocurrency - if given use this currency as default to currency when loading post/page first time
* use_ajax - if set to yes, display result without reloading post/page
* result_decimals -Set how many decimals to be displayed in the result of the conversion
* result_sanitize - Sanitize user input
* order_by - Order by currency or by currency description. (order_by = "currency" or order_by = "description")

= Default values =
* [mcwp_currencyconverterform html_id="{none}" input_type="text" default_amount="100" amount_title="Amount" from_title="From" to_title="To" result_title="Result" calculatebutton_title="Calculate" show_currencydescription="yes" currencies="{all currencies from ecb}" default_fromcurrency="EUR" default_tocurrency="USD" result_decimals="4" use_ajax="no" result_sanitize="yes" order_by="{no sorting}"]

= [mcwp_currencylist] attributes =
* html_id - create a (div)wrapper for the list and apply this id on the wrapper
* amount - shows what amount to convert between currencies (1EUR = x USD, can be changed to 15EUR = x USD)
* currencies - pair of what currencies that should be converted and displayed
* result_decimals - Set how many decimals to be displayed in the result of the conversion (for all listed currency pairs)
* separator - Include separator between each pair of currencies (eg. 1EUR= x USD<br />1EUR = x SEK) etc
* order_by - Order by first currency, second currency or by result (order_by = "first_currency", order_by = "second_currency" or order_by = "result")

= Default values =
* [mcwp_currencylist html_id="{none}" amount="1" currencies="EUR-USD,USD-EUR,EUR-SEK,SEK-EUR,EUR-GBP,GBP-EUR" result_decimals="4" separator="{none}" order_by="{no sorting}"]

= [mcwp_currencyconvert] = 
* html_id - apply id to the span-element
* amount - which amount to use for the conversion
* from - which currency to convert from (EUR, SEK etc)
* to - which currency to convert to (SEK, EUR etc)
* result_decimals -Set how many decimals to be displayed in the result of the conversion
* display_amount - yes/no (if no, just return the value of the conversion)
* display_type -  Display type 1: eg. 300EUR=232.7850GBP or 2: eg. 300EUR(232.7850GBP)

= Default values =
* [mcwp_currencyconvert html_id="{none}" amount="1" from="EUR" to="USD" result_decimals="4" display_amount="yes" display_type="1"]


= Example css =
* .mcwp-convert-ecb {display:block;}
* .mcwp-convertequal {padding:0 4px;}
* .mcwp-result .mcwp-title {font-weight:bold;}
* .mcwp-result .mcwp-tocurrency {color:#5e92c4;font-weight:bold;}
* .mcwp-selectamount input, .mcwp-selectfromto select {display:block;padding:7px;}
* .mcwp-selectamount input {width:196px;}
* .mcwp-selectfromto {padding:1em 0;color:#5e92c4;}
* .mcwp-submit {padding:7px;width:212px;}
* .mcwp-submit:hover {cursor:pointer;background:#eeeeee;}


== Frequently Asked Questions ==

= Why don't you include any css for the plugin? =

The goal is to make the plugin work as fast as possible as expected even with the design. By not supplying any css the developer has full control over
what's happening all the way. If you want to some css to start with, just copy the css from the usage example (in the "Other notes"-tab) and put it in your theme (prefererably in your child's theme).

= Does the plugin work on a multilanguage site? =

Yes. It supports label changes directly in the shortcode or through given language files in the lang/ folder of the plugin. Text domain is mastercurrency-wp so language files should look like this:
mastercurrency-wp-sv_SE.mo, mastercurrency-wp-sv_SE.po. (if swedish: sv_SE = swedish)


= Does the plugin support any other external source to fetch current rates? =

No. Not at the moment, but if you are a developer it would easily managed by creating a new object and extending the 
current mastercurrencywp object and change the source that should be used to fetching the rates. If there would be a
high demand of fetching rates from another source it would be considered to add that source, but not there are no such
high demand for any other source then ECB at the moment


= Do you have any pro version? =

No, everything is free!


== Screenshots ==

1. Screenshot with the usage example.

== Changelog ==

= 1.1.61 =
* Bugfix: Only sorting were applied correctly in conveterforms when using custom currencies before. Now it works even if no custom currencies are defined.

= 1.1.60 =
* New attribute separator in currencylist to make it possible to separate each currency pair easily
* New attribute order_by to sort currencylist by first currency, second currency or by result of conversion
* New attribute order_by to sort by currency/description in converter form 
* New attribute input_type. Set this to number if you want the input-field of amount to displayed as number input (html5)
* New class added so possible to style odd/even rows easily ("pyjamas rows")
* Possible to set id's for converter form(s) directly in shortcode. The id would be applied to the form
* Possible to set id's for currency list(s) directly in shortcode. This will create a wrapper-div around the list with the id given
* Possible to set id's for single currency convert directly in shortcode. The id would be applied to the span element

= 1.1.50 =
* New attribute in shortcode to set default amount in currency converter form(s)

= 1.1.43 =
* Translation(s) recognized by the wordpress.org translation tool

= 1.1.3 =
* Translation(s) supported (internationalization of plugin)
* Swedish translation added
* Possibility to show only description (eg. Japanese yen) in currency form select lists
* Sanizities user input amount in currency form (if user puts in 4,323 this would be treated as 4.323. If user puts 4 233.43  it will be treated as 4233.43 etc)
* Include nr of decimals for result of conversion (in all shortcodes)

= 1.0.8 =
* New attributes for converter form shortcode. Select default from and to currencies within shortcode (and use ajax or not)
* New shortcode to display a single currency conversion in a post/page ([mcwp_currencyconvert])
* Several converter forms can now be viewed on same post/page and would still have valid html
* Option to return result direct without having to reload post/page (ajax). 
* Calculates correctly when using lowercase currencies (eg. eur instead of EUR)

= 1.0.3 =
* It's now possible to customize which currencies that are available in the currency converter form

= 1.0.2 =
* When remote currency rates cannot be fetched (because of server down or similar), the plugin uses last (successful) fetched rates.

= 1.0.1 =
* Minor bugfix in converter form. Now shows the amount user entered in the result and shows the latest entered value in the input field.

= 1.0 =
* Plugin released


== Upgrade notice ==
Please tell me if you're missing something (in the support form) ! I will do my best to add the feature.

== Example of usage ==

= shortcodes in post(s)/page(s) =
* [mcwp_updateddate]
* [mcwp_currencyconverterform amount_title="Enter amount" from_title="From currency" to_title="To currency" result_title="Conversion result" calculatebutton_title="Calcutate now!" show_currencydescription="no"]
* [mcwp_currencylist amount="1" currencies="EUR-USD,USD-EUR"]
(See result in screenshot from above shortcodes)

= Other examples of usage =
== Converter form(s) ==
* [mcwp_currencyconverterform html_id="whatever" amount_title="Amount" from_title="From currency" to_title="To currency" calculatebutton_title="Calculate currency" result_title="Result" currencies="SEK,EUR,GBP,USD" default_fromcurrency="EUR" default_tocurrency="GBP" use_ajax="yes"]
* [mcwp_currencyconverterform default_fromcurrency="gbp" default_tocurrency="sek" use_ajax="yes"]
* [mcwp_currencyconverterform currencies="SEK,EUR,GBP,USD" default_fromcurrency="gbp" default_tocurrency="sek" use_ajax="yes" show_currencydescription="only" result_decimals="0"]
* [mcwp_currencyconverterform currencies="SEK,EUR,GBP,USD" default_fromcurrency="gbp" default_tocurrency="sek" use_ajax="yes" show_currencydescription="only" result_decimals="2" result_sanitize="no"] 
* [mcwp_currencyconverterform currencies="SEK,EUR,GBP,USD" default_fromcurrency="SEK" default_tocurrency="USD" use_ajax="no" show_currencydescription="no"] 

== Currency list(s) ==
* [mcwp_currencylist html_id="givemeabreak" amount="10" currencies="EUR-USD,USD-EUR,SEk-CAD" result_decimals="2" separator="<br>"]
* [mcwp_currencylist amount="10" currencies="EUR-USD,USD-EUR,SEk-CAD" result_decimals="2" separator=", "]
* [mcwp_currencylist amount="10" currencies="EUR-USD,USD-EUR,SEk-CAD" result_decimals="2" separator="<br>" order_by="first_currency"]
* [mcwp_currencylist amount="10" currencies="EUR-USD,USD-EUR,SEk-CAD" result_decimals="0" separator="<br>" order_by="second_currency"]
* [mcwp_currencylist amount="10" currencies="EUR-USD,USD-EUR,SEk-CAD" result_decimals="0" separator="<br>" order_by="result"]

== Single conversion(s) ==
* [mcwp_currencyconvert amount="300" from="EUR" to="GBP" display_amount="yes" display_type="1"]
* [mcwp_currencyconvert html_id="eurtogp" amount="300" from="EUR" to="GBP" display_amount="yes" display_type="2"]
* [mcwp_currencyconvert amount="300" from="EUR" to="GBP" display_amount="no" result_decimals="0"]

You can find demos at: <http://wibergsweb.se/plugins/mastercurrencywp>

== Example css ==
* .mcwp-convert-ecb {display:block;}
* .mcwp-convertequal {padding:0 4px;}
* .mcwp-result .mcwp-title {font-weight:bold;}
* .mcwp-result .mcwp-tocurrency {color:#5e92c4;font-weight:bold;}
* .mcwp-selectamount input, .mcwp-selectfromto select {display:block;padding:7px;}
* .mcwp-selectamount input {width:196px;}
* .mcwp-selectfromto {padding:1em 0;color:#5e92c4;}
* .mcwp-submit {padding:7px;width:212px;}
* .mcwp-submit:hover {cursor:pointer;background:#eeeeee;}