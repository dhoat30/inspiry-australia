=== Laybuy Payment Extension for WooCommerce  ===
Contributors: laybuy, overdose
Tags: woocommerce, payment-gateway
Requires at least: 4.6
Tested up to: 5.8
Stable tag: 5.3.5
Requires PHP: 5.6.32
License: Apache License
License URI: https://www.apache.org/licenses/LICENSE-2.0.html

Laybuy WooCommerce Gateway Plugin

== Description ==

This extension allows you to integrate your WooCommerce store platform with the https://laybuy.com payment system

= REQUIREMENTS =

* PHP version 5.6 or greater (PHP 7.1+ is recommended)
* MySQL version 5.5 or greater (MySQL 5.6+ is recommended)
* WooCommerce 3.3+ / Requires WordPress 4.5+


== Installation ==

1. Use the github's download feature to download a zip of the plugin (Clone or Download -> Download ZIP) to the `/wp-content/plugins/laybuy-woocommerce` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Browse to Admin -> Wocommerce -> Settings -> Checkout -> Laybuy, here you can set your Laybuy Merchant details and choose to display the product price breakdown. The breakdown is displayed with Woocommerce's Product actions, there is a link in the Description to show you where these will display.

== Changelog ==

= 5.3.5 =
*Release Date: Wed, 15 Dec 2021*

* Redirect from Laybuy wp action

= 5.3.4 =
*Release Date: Fri, 27 Aug 2021*

* Support request button added

= 5.3.3 =
*Release Date: Thu, 12 Aug 2021*

* Compatibility with WordPress 5.8

= 5.3.2 =
*Release Date: Thu, 9 July 2021*

* Support of legacy WooCommerce

= 5.3.1 =
*Release Date: Thu, 4 March 2021*

* Missing phone number bug fix on checkout

= 5.3.0 =
*Release Date: Tue, 2 March 2021*

* Compatibility with WooCommerce 5.0.0
* Small bug fixes

= 5.2.9 =
*Release Date: Mon, 1 March 2021*

* Add billing phone field in advanced settings
* Fix bug on cart on Variable Product

= 5.2.8 =
*Release Date: Thu, 12 Jan 2021*

* Change product price breakdown hook priority
* Add price breakdown snippet on products that are "out of stock" (optional)

= 5.2.7 =
*Release Date: Thu, 3 Dec 2020*

* Small bug fix for product type when the selected list is empty

= 5.2.6 =
*Release Date: Tue, 1 Dec 2020*

* Add advanced product type setting in admin to control which product type to display the laybuy widget for

= 5.2.5 =
*Release Date: Wed, 25 Nov 2020*

* Add support of the US dollar

= 5.2.4 =
*Release Date: Sat, 31 Oct 2020*

* Disable geolocation tracking by default. Add laybuy_geolocation filter for more flexibility
* Added white/dark theme logo selection from the admin
* Fixed some small bugs upon activation

= 5.2.3 =
*Release Date: Tue, 24 Sep 2020*

* Tested with PHP up to 7.3.22 version
* Add currency and order amount in confirmation order request to trigger extra checks

= 5.2.2 =
*Release Date: Mon, 7 Sep 2020*

= 5.2.1 =
*Release Date: Mon, 2 Sep 2020*

* Hide widget from not supported countries

= 5.2.0 =
*Release Date: Mon, 2 Sep 2020*

* Fix currency issue

= 5.1.12 =
*Release Date: Mon, 31 Aug 2020*

* Don't show Laybuy for non supported countries while using Country Based currency plugins.

= 5.1.11 =
*Release Date: Tue, 30 June 2020*

* Add Product Price Breakdown Hook Priority

= 5.1.10 =
*Release Date: Tue, 26 May 2020*

* Compatibility with Woocommerce Composite Products
* Compatibility with Woocommerce Product Bundles

= 5.1.9 =
*Release Date: Mon, 18 May 2020*

* Compatibility with WooCommerce 4.1.0

= 5.1.8 =
*Release Date: Mon, 13 March 2020*

* Fix wording for some cases on cart and checkout pages

= 5.1.7 =
*Release Date: Mon, 9 March 2020*

* Some bug fixes for Laybuy Plus extension

= 5.1.6 =
*Release Date: Mon, 9 March 2020*

* Added support of Laybuy Plus extension

= 5.1.5 =
*Release Date: Thu, 27 Feb 2020*

* Fix compatibility issue for woo plugins using woocommerce_checkout_order_processed hook

= 5.1.4 =
*Release Date: Thu, 20 Feb 2020*

* Small bug fix

= 5.0.6 =
*Release Date: Mon, 14 Oct 2019*

= 5.1.3 =
*Release Date: Thu, 20 Feb 2020*

* Add price calculation fallback on LayBuy print paragraph

= 5.1.2 =
*Release Date: Thu, 20 Feb 2020*

* Extend logging coverage

= 5.1.1 =
*Release Date: Thu, 20 Feb 2020*

* Hide LayBuy price breakdown in admin panel

= 5.1.0 =
*Release Date: Thu, 13 Feb 2020*

* Added Compatibility Mode
* Fix price breakdown for variable products

= 5.0.18 =
*Release Date: Mon, 4 Feb 2020*

* Added over limit payments

= 5.0.17 =
*Release Date: Mon, 3 Feb 2020*

* Small fix of a laybuy paragraph showing for expensive or out of stock products

= 5.0.16 =
*Release Date: Mon, 27 Jan 2020*

* Added support of Woocommerce 3.9.0

= 5.0.15 =
*Release Date: Mon, 29 Nov 2019*

* Turn on/off Laybuy info page (/laybuy) in advanced settings

= 5.0.14 =
*Release Date: Mon, 25 Nov 2019*

* Minor style fixes

= 5.0.13 =
*Release Date: Tue, 19 Nov 2019*

* Minor UI fix on cart page

= 5.0.12 =
*Release Date: Thu, 14 Nov 2019*

* Show shipping without GST in an invoice

= 5.0.11 =
*Release Date: Mon, 12 Nov 2019*

* Add usage of php version_compare function

= 5.0.10 =
*Release Date: Mon, 12 Nov 2019*

* Price breakdown predefined text settings for the clients updating from the old version of the plugin

= 5.0.9 =
*Release Date: Mon, 11 Nov 2019*

* Remove price breakdown table view

= 5.0.8 =
*Release Date: Thursday, 1 Nov 2019*

* Fix product price breakdown displaying issue

= 5.0.7 =
*Release Date: Thursday, 21 Oct 2019*

* Add extra layer of logging
* Add support of Laybuy Global

= 5.0.4 =
*Release Date: Thursday, 24 Sep 2019*

* Compatibility with WooCommerce 3.7.
* Compatibility with WooCommerce Checkout Add-Ons
* Compatibility with Order Delivery Date Pro for WooCommerce
* Removed unnecessary module description in module settings
* Fixed some minor warnings from error logs