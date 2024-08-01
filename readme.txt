=== Stock Triggers for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, karzin, omardabbas, kousikmukherjeeli
Tags: woocommerce, stock, woo commerce
Requires at least: 4.4
Tested up to: 6.6
Stable tag: 1.7.3
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Automatic product stock increase/decrease actions for WooCommerce.

== Description ==

**Stock Triggers for WooCommerce** plugin lets you control which actions automatically decrease or increase your product stock.

### &#9989; Stock Triggers ###

* Order status: Cancelled
* Order status: Completed
* Order status: Failed
* Order status: On hold
* Order status: Pending payment
* Order status: Processing
* Order status: Refunded
* Payment complete
* Checkout order processed

In addition, there are a number of **admin** options, like running decrease or increase actions manually via the **"Bulk actions"** dropdown in admin orders list, or setting stock actions to be performed on **new order by admin**.

### &#127942; Premium Version ###

[Stock Triggers for WooCommerce Pro](https://wpfactory.com/item/stock-triggers-for-woocommerce/) plugin version will add **custom order statuses** to the triggers list. And in addition, it will allow you to set **custom triggers** directly, by typing action names.

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/stock-triggers-for-woocommerce/).

### &#8505; More ###

* The plugin is **"High-Performance Order Storage (HPOS)"** compatible.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Stock Triggers".

== Changelog ==

= 1.7.3 - 01/08/2024 =
* Dev - Triggers - "Checkout update order meta" trigger added.

= 1.7.2 - 31/07/2024 =
* WC tested up to: 9.1.
* Tested up to: 6.6.

= 1.7.1 - 30/06/2024 =
* Dev - Admin - Admin new order - "Maybe increase" and "Maybe decrease" options added.

= 1.7.0 - 20/06/2024 =
* Dev - "High-Performance Order Storage (HPOS)" compatibility.
* Dev - PHP 8.2 compatibility - "Creation of dynamic property is deprecated" notice fixed.
* WC tested up to: 9.0.
* Tested up to: 6.5.
* WooCommerce added to the "Requires Plugins" (plugin header).

= 1.6.7 - 27/09/2023 =
* WC tested up to: 8.1.
* Tested up to: 6.3.
* Plugin icon, banner updated.

= 1.6.6 - 11/07/2023 =
* Fix - Admin settings - Possible PHP warning fixed.

= 1.6.5 - 18/06/2023 =
* WC tested up to: 7.8.

= 1.6.4 - 22/05/2023 =
* Plugin author updated.

= 1.6.3 - 18/05/2023 =
* Dev - Developers - `alg_wc_stock_triggers_function_decrease` and `alg_wc_stock_triggers_function_increase` filters added.
* WC tested up to: 7.7.
* Tested up to: 6.2.

= 1.6.2 - 13/08/2022 =
* Dev - Remove standard triggers - "B2BKing Pro" compatibility added.
* Deploy script added.
* WC tested up to: 6.8.
* Tested up to: 6.0.

= 1.6.1 - 21/04/2022 =
* Admin settings - "Admin" section added: "Admin Order Options" and "Advanced Options" moved from the "General" settings section.
* WC tested up to: 6.4.
* Tested up to: 5.9.

= 1.6.0 - 25/11/2021 =
* Dev - "Require products" and "Require product categories" options added.
* Dev - "Remove standard triggers" options added.
* Dev - Code refactoring.
* WC tested up to: 5.9.

= 1.5.3 - 07/09/2021 =
* Dev - "Bulk actions" option added.
* WC tested up to: 5.6.

= 1.5.2 - 17/08/2021 =
* Dev - Localisation - Chinese (`zh_CN`) translation added (by @tangzhehao).
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 1.5.1 - 12/07/2021 =
* Dev - Settings - Descriptions updated.

= 1.5.0 - 12/07/2021 =
* Fix - "Undefined property: ..." notice fixed in admin settings (when "Enable plugin" option is disabled).
* Dev - Admin Order Options - "Adjust line item product stock" options added.
* Dev - Settings - "General Options" subsection renamed to "Admin Order Options".

= 1.4.0 - 26/06/2021 =
* Dev - "Force order stock update" options added.
* Dev - Debug - Log file name updated (from `alg-wc-stock-triggers` to `stock-triggers`).
* Dev - Main init priority increased to `PHP_INT_MAX`.
* Dev - Plugin is initialized on `plugins_loaded` now.
* Dev - Code refactoring.
* WC tested up to: 5.4.

= 1.3.1 - 31/03/2021 =
* Dev - Advanced - Debug - More info added to the log.
* Dev - Settings - Order status names are now translated (text domain changed from `stock-triggers-for-woocommerce` to `woocommerce`).
* Dev - Settings - Descriptions updated.
* WC tested up to: 5.1.
* Tested up to: 5.7.

= 1.3.0 - 20/01/2021 =
* Dev - Localisation - `load_plugin_textdomain()` function moved to the `init` action.
* Dev - Admin settings restyled.
* Free plugin version released.
* WC tested up to: 4.9.

= 1.2.0 - 23/12/2020 =
* Dev - General - "Restore stock on admin new order" option renamed to "Admin new order", converted to a select box (i.e., vs checkbox), and new "Decrease" option added.
* Dev - Triggers - "Checkout order processed" trigger added.
* Dev - Advanced - "Debug" option added.
* WC tested up to: 4.8.
* Tested up to: 5.6.

= 1.1.1 - 07/10/2020 =
* Dev - "Restore stock on admin new order" option added.
* Dev - Code refactoring.
* WC tested up to: 4.5.
* Tested up to: 5.5.

= 1.1.0 - 27/03/2020 =
* Fix - Removing actions with `0` priority now.
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.3.

= 1.0.0 - 03/08/2019 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
