=== Product Levels add-on for ATUM ===

Contributors: stockmanagementlabs, salvamb, japiera, agimeno82, dorquium
Tags: product levels, bom, bill of materials, product parts, raw materials
Requires at least: 4.6
Tested up to: 5.4.1
Requires PHP: 5.6
WC requires at least: 3.0.0
WC tested up to: 4.1.0
Stable tag: 1.4.4
License: ©2020 Stock Management Labs™

== Description ==

One of the most important add-ons we have placed on the list of ATUM's premium features. Bill Of Materials or product structure (BOM) is a list of the raw materials, product parts and the quantities of each needed to manufacture an end product.

In our Premium add-on of ATUM, we are introducing the first two levels of control. ATUM inventory management for WooCommerce lets you control company’s Raw Materials and Product Parts. A small retailer,  distributor, wholesaler, standard manufacturer, or any business that wants to take complete control over their growth will find this feature irreplacable.

= AVAILABLE FEATURES =

* We have created two new post types within WooCommerce product data section. As an addition to simple, variable, grouped and external product, ATUM’s ‘Product Levels Premium’ ads ‘Raw Material’ and ‘Product Part’ post types. Bill of Materials is the main driving force of any business, and our premium version takes complete care of raw materials and product parts. These are the two largest indicators of profitability. Let’s take cards shop as an example. A birthday card is a finished product that ATUM controls by Stock Central. However, the paper and the ink used to make the card we classify as ‘Raw Material’. ‘Product Part’ would be a decoration bow or a voucher the seller adds to the packaging. Even the packaging can be created as a product part if required. The user would create these BOM in WooCommerce and relate them to individual products sold in the shop. We’ll explain how further down on this page.
* Under the General Tab, users can set their regular price, sale price, purchase price and any applicable download files as ATUM needs these figures for the Manufacturing Central and any other future upgrades.
* Set the Product Part or Raw Material SKU and the actual BOM amount available for further production.
* An additional Bill Of Materials tab was created within the product data section of every WooCommerce post type. From the example above user would create a raw material product called 'black ink' for example.
* The user is able to add as many Raw Materials or Product Parts per WooCommerce product as needed. Simple and very user friendly interface allows users to control the full BOM of any product. We have included an automated search for any existing BOM so adding one has never been easier. Users are even able to multiply the added Raw Materials or Product Parts.
* Brand new Manufacturing Central has been added to the side ATUM menu. Similar to Stock Central for product within WooCommerce, Manufacturing Central takes control of all Bill Of Material items within the business. As standard everyting happens on one beautifully design screen. With an additional screen control and help menu, working has never been so easy.


== Changelog ==

---

`1.4.4`

*2020-05-08*

**Features**

* Overall performance improvements.
* Reduced SQL queries complexity.
* Removed duplicated queries.
* Avoid recalculating the BOM trees multiple times.
* Added cache handlers to some helpers to improve performance.


**Changes**

* Make stockables all the BOM products' inventories.
* Prevent accessing order items if order type not supported.
* Added PL variation types to MI compatible children types.
* Added "modify" and "delete" options by inventory to BOMModel.
* Added decimal values (if set) to the calculated stock field.
* Refactoring.

**Fixes**

* Fixed error when there is a NULL parameter being passed to a hook.
* Fixed wrong logic when enabling/disabling the manage stock field.
* Fxed manage stock field was not being saved for new inventories.
* Fixed BOM variable with MI calculating wrong stock in Manufacturing Central.
* Prevent order items' BOM trees from being built multiple times and casuing issues.
* Fixed POs always reducing stock when changing their status.
* Fixed WC Orders with BOM + MI order items couldn't be changed form the backend.
* Fixed BOM order item transient being deleted in non "on-hold" statuses.
* Fixed BOM products not being counted in ATUM Dashboard.
* Minor CSS fixes.

---

`1.4.3`

*2020-04-03*

**Changes**

* Updated ATUM Utils JS component.
* Ensure there are no WPML translations when deleting a linked BOM.
* Refactory.

**Fixes**

* Fixed stock being reduced instead of increased for BOM without MI on Order status changes.
* Fixed checkboxes column not being shown in Manufacturing Central when is AJAX loaded.
* Prevent showing the BOM panels for WPML translations.

---

`1.4.2`

*2020-03-06*

**Fixes**

* Fixed backorders allowed field's wrong show/hide behavior.
* Refactory.
* CSS fixes.

---

`1.4.1`

*2020-02-25*

**Features**

* Added support for inventory expiry days.

**Changes**

* Refactory set_bom_order_item_transient function to prevent non needed queries.
* Added new hook after Manufacturing Central List filters.
* Suspend the products' BOM order stock recalculation when an order inventory is created.
* Suspend the products' BOM order stock recalculation when an order inventory is removed.
* Display the order item's BOM tree collapsed by default.
* Added Cache to the BOM order items transient.
* Added cache to BOMModel order items' methods.
* BOM order item inventories' changes detection when saving orders.

**Fixes**

* Fixed wrong BOM order tree calculated if any inventory had negative stock.
* Fixed wrong available inventory quantity when editing orders with already changed stock.
* Fixed no correct quantities were inserted in the BOM orders' table when editing from the backend.
* Fixed inventory stock not being increased correctly from WC orders.
* Fixed wrong BOM order items quantities when the order status was "pending payment".
* Fixed product with BOM and without MI stock wasn't decreasing correctly.
* Fixed BOM tree order items were messed up when ordering items by column.
* Refactory.

---

`1.4.0`

*2020-02-07*

**Features**

* Added full compatibility with Multi-Inventory.
* Now all the BOM types may have multiple inventories and the MI configuration is applied to them too.
* If the BOM stock control is enabled the Main Inventories will handle the calculated stock.
* The inventories used for the last level BOM can be eidted manually from the order/PO/IL page.
* Added new BOM management popup to be able to edit the used inventories manually.
* The full BOM tree (with used quantities) is now shown on orders/POs/ILs (with or without MI).
* A new icon is shown on order items to easily identify whether the product has linked BOM.
* The correct BOM inventories are being reduced/increased when switching order statuses.
* The sellable BOMs will make use of MI as any other MI-compatible product when sold.
* Added the BOM fields (calculated stock, committed, shortage, free to use) to the Main Inventory.
* Added new helper to find the bottom-level BOM children.
* Added column groups to Manufacturing Central.
* Added new custom hook for BOM tree nodes.
* Added inventories to the BOM report.
* Add the quantity input to the BOM Management popup and disable the order item's one.
* Update BOM tree qtys after changing the order item quantity.
* Exclude all the associated products and the variation siblings from BOM link searches (to avoid cyclical issues).
* Hide the categories that have only non-sellable BOMs within and the "hide_empty" option set to true.

**Changes**

* Upgraded to TypeScript 3.7.3.
* Updated dependency versions.
* Changed the PL icon logic for the tooltip text.
* Disable the WC's manage stock for BOM products when the BOM stock control is enabled.
* Get rid of the original_stock hidden field when a product has calculated stock.
* Disable the Allow Backorders field for the associated products that have children not allowing them.
* Show the allow backorder fields on BOM products when the BOM stock control is enabled.
* Wait untilt WC has completed the product fields visibility adjustments before doing our own.
* Only add hooks for force real stock when necessary.
* Get rid of the "ATUM_PREFIX" constant from db table names to avoid issues.
* Added changed_qty column to BOM Orders table.
* Only check if there is enough stock available if the BOM stock control is disabled.
* Added the premium support link to the plugin details on the plugins page.
* Show Virtual and Downloadable checkboxes on simple BOMs.

**Fixes**

* Removed the BOM fields from non-main inventories.
* Fixed stock increase/decrease for items with linked BOMs.
* Fixed available to purchase option.
* Fixed inventories not being set as onbackorder when should.
* Fixed unending loop issue affecting to variable BOMs.
* Fixed the ATUM data removal when a product is deleted.
* Fixed allow back orders field not being disabled on variations.
* Fixed associated products not showing the right stock status when disabling the OOST.
* Fixed non numeric value error when getting the calculated stock.
* Recalculate the BOM tree stock after changing the minimum threshold from the product page.
* Recalculate the synced purchase price when doing changes to the parent product's BOMs.
* Prevent adding BOM order rows with qty 0.
* Show the PL fields correctly on BOM variations when the BOM stock control is disabled.
* Fixed "Used for variations" checkbox not being shown when creating variable BOMs.
* Fixed BOM search query.
* Avoid locking the manage stock checkbox on non-main inventories.
* CSS fixes.
* Refactory.

---

`1.3.7.1`

*2019-12-05*

**Changes**

* Check if an associated product is really a product before adding it to the list.

**Fixes**

* Fixed variable BOM sellable saved before saving changes.
* Fixed ATUM Product Data saved twice when creating if BOM stock control was enabled.

---

`1.3.7`

*2019-11-14*

**Features**

* Set aliases for the BOM product types' classes.
* Added filtering to Products' API endpoint using PL fields.

**Changes**

* Prepare the BOM products for database in API requests.
* CSS changes for accessibility (following WordPress 5.3 new styling).

**Fixes**

* Fixed wrong arguments' order in Products' API endpoint extender.
* Register some PL admin hooks when a REST API request is being performed.

---

`1.3.6.2`

*2019-11-04*

**Fixes**

* Fixed calculated stock being updated even if BOM stock control was disabled.

---

`1.3.6`

*2019-10-31*

**Features**

* Added Product Levels extension for the new ATUM REST API.
* Added BOM order items to the WC Orders, Purchase Orders and Inventory Logs endpoints.
* Added linked BOMs to the Products and Variation Products endpoints.
* Added BOM Stock control data to the Products endpoint.
* Added Product Levels tools to the ATUM Tools endpoint.
* Added sync real stock for products with calculated stock.
* Added sync BOM calculated stocks tool.

**Changes**

* Exclude BOMs from query from the list of IDs to subquery.
* Sync all the WC stock with calculated stock automatically when updating to this version.

**Fixes**

* Fixed wrong bom_sellable value being saved for Variable BOMs.
* Fixed "get_all_related_bom_products" legacy method that wasn't retieving variations.
* Fixed no BOMs in Manufacturing Central when accessing the "all stock" view.
* Fixed BOM sellable field being saved in non-BOM products.
* Fixed wrong language text domains.
* CSS fixes.
* Refactory.

---

`1.3.5.1`

*2019-10-11*

**Fixes**

* Fixed calculated stock wasn't set from Manufacturing Central if the new BOM stock was 0.
* Refactory.

---

`1.3.5`

*2019-09-20*

**Changes**

* Adapted gulpfile code to work with Gulp 4.
* Check that a product is really a product before confirming if it's a BOM.
* Extra checking to avoid issues with products that not exist anymore.
* Count the sellable BOMs for the "Sales Last Days" column in Manufacturing Central.
* Set the variable product's sellable status depending on its children statuses.

**Fixes**

* Fixed purchase price sync when a product is formed by more than one unit of any BOM.
* CSS fixes.
* Refactory.

---

`1.3.4.2`

*2019-09-05*

**Changes**

* Updated JS dependencies.
* Updated gulpfile.
* Added a new hook to alert other plugins that Product Levels has just activated.

**Fixes**

* Select2 CSS fix.

---

`1.3.4.1`

*2019-08-16*

**Changes**

* Moved ATUM product data fields related to PL from ATUM in the Helper duplicate a product function.

**Fixes**

* Buf fix wrog stock status in backordered products when  bom stock control enabled.
* Buf fix BOM associates were not shown.
* CSS fixes.
* Refactory.

---

`1.3.4`

*2019-07-31*

**Features**

* Adapted to the new ATUM colors feature.

**Fixes**

* Fixed stock indicator and editable stocks in Manufacturing Central.
* Fixed language file not being loaded.
* Prevent adding "calculated stock" tooltip to MI parents.
* Fixed tsconfig.json to support TypeScript 3.5.3.
* CSS fixes.
* Refactory.

---

`1.3.3.6`

*2019-06-28*

**Fixes**

* Fixed undefined variable notice.
* Check that a product still exists before trying to display it on List Tables.
* Remove linked BOM when a product is deleted.
* Avoid memory leaks when deleting cache groups.
* Refactory.

---

`1.3.3.5`

*2019-06-21*

**Fixes**

* Fixed undefined index error in Manufacturing Central.
* Fixed stock indicator not showing for variable BOM in MC.
* Refactory.

---

`1.3.3.4`

*2019-06-03*

**Fixes**

* Handle weird cases when trying to get the calculated stock from something that is not a product.
* Prevent reducing BOM stock twice with WooCommerce versions < 3.5.0.

---

`1.3.3.3`

*2019-05-24*

**Fixes**

* Fixed calculated stock recalculation for all the BOM associates on every purchase.
* Fixed editables variations with calculated stock quantity on Stock Central.
* Refactory.

---

`1.3.3.2`

*2019-05-18*

**Changes**

* Added exclude path to TypeScript config.

**Fixes**

* Avoid conflicts with jQuery UI's datepicker.
* CSS fixes.

---

`1.3.3.1`

*2019-05-08*

**Fixes**

* Refactory.
* CSS recompilation.

---

`1.3.3`

*2019-04-30*

**Features**

* Performance improvement: reduced number of db queries performed in Manufacturing Central to the half.
* Performance improvement: added a calculated stok quantity column to the ATUM product data table to reduce calculations on every page load.
* Performance improvement: recalculate the calculated stock quantity column when needed.
* Recalculate the calculated stock quantity for the whole BOM tree after MC changes.
* Recalculate the calculated stock quantity every time the a product stock is increased/reduced.

**Changes**

* Updated to the latest TypeScript version.
* Disable the ATUM cache when forcing the calculated stock quantity.

**Fixes**

* Undefined index fix.
* Refactory.
* Added compatibility with WC 3.6+ (items are now discounting stock when added to orders manually).
* Fixed checking BOM stock in cart was checking non BOM products.
* Fixed blank cells showing on Sales Last Days column.
* Fixed stock reduced twice for calculated products in Orders.
* Center numeric columns.
* Fixed alert shown in MC when trying to change a BOM stock.
* Only change stock when saving items if already changed some stock.
* Fixed Refund not restocking calculated products.
* Refactory.

---

`1.3.2.3`

*2019-03-29*

**Changes**

* Show a 404 error page when accessing to non sellable BOM products directly.

**Fixes**

* Fixed Purchase Order note quantities not showning correctly.

---

`1.3.2.2`

*2019-03-22*

**Fixes**

* Fixed Double stock reduced when the site is working with legacy payment gateways.
* Fixed Subscription fields shown in BOM variables products.
* Fixed order bom table was not created in multisite networks.

---

`1.3.2.1`

*2019-03-13*

**Changes**

* Delete all the Product Levels data when unistalling if the option in ATUM settings is enabled.

**Fixes**

* Fixed Manufacturing Central reports not being printed correclty in some cases.


---

`1.3.2`

*2019-03-08*

**Features**

* Performance improvements: reduced the number of db queries using cache.
* Refactory JS to TypeScript.

**Fixes**

* Fixed filter by supplier in Manufacturing Central had variable products included although no children available.
* Avoid CSS conflicts with other plugins using Select2.
* Fix: Manufacturing Central uncontrolled was not using the right trait.
* Fixed wrong total in wareahuse in BOM list item when bom_stock_control was activated.
* Fixed Manufacturing Central export errors.
* Fixed Stock Indicator not properly shown for stock calculated products.
* Fixed PHP notices on Manufacturing Central reports.

---

`1.3.1`

*2019-03-01*

**Features**

* Improved performance with cache.
* Added compatibility for order refunds.

**Changes**

* Add on-hold BOMs to calculated stock.
* Check if there are enough BOM for fullfill the order.
* Remove BOM stock control props for the products that have no linked BOM(s).
* Cache refactoring.
* Do not allow the stock to be edited from List Tables when is being calculated.
* Hide the Out of Stock Threshold field on non sellable BOMs.
* Add uncolored rows to the BOM builder by default.

**Fixes**

* Fixed WC Orders not showing the real stock changes.
* Fixed BOM with no stock not being used to calculate the stock.
* Fixed stock quantity field showing in variations when BOM stock control is enabled.
* Fixed minimum threshold not working properly in some cases.
* Fixed back orders calculation in BOM builder.
* Fixed thumb column class in BOM builder template.
* CSS fixes.

---

`1.3.0`

*2019-02-22*

**Features**

* New BOM Stock control feature. You can now control the stock of all your products by their children BOM's stock.
* Added option to Settings to enable/disable the BOM stock control functionality globally.
* New BOM associates tab added to BOMs when the BOM stock control is enabled.
* New BOM stock control fields: "Calculated stock quantity", "Minimum threshold", "Selling Priority" and "Available to purchase".
* Added the BOM stock control fields as columns to Stock Central and Manufacturing Central.
* Make the BOM stock control columns sortable.
* Added compatibility between Product Levels and WC Product Bundles.
* Allow setting any priority as the last with a click.
* Do not show the BOM stock control fields in non-sellable BOMs.
* Rearrange selling priorities after changing one.
* Control the minimum threshold for BOM associates according to the selling priority.
* Adjust the BOM controlled products' stocks to the available to purchase amount.
* New BOM builder UI following ATUM style guides.
* Added link to BOM builder names.
* Added thumbnail and toggle icon columns to BOM builder.
* Added full BOM multi-tree for Manufacturing Central.
* Added backorders column to Manufacturing Central.
* Added tooltips to unmanaged BOMs in Manufacturing Central.
* Javascript Modularization and code upgraded to ES6 syntax (work in progress).
* Javascript code refactorized (work in progress).

**Changes**

* Changed product types svg to atum font icons.
* Performance improvements using cache.
* Use ATUM thumb placeholder for BOM products without thumb.
* Recalculate BOM item data after quantity input changes.
* Removed non-sellable BOM's from json_search_products.
* Removed some columns from MC when BOM stock control is enabled.
* Force manage stock in BOM tree products.
* Changed section titles styles.
* Change comitted, free to use and shortage calculations.
* Hide stock status for unmanaged variable BOMs.
* Show the MC's BOM hierarchy icon on all the BOMs that are being used.
* Select the current item within the full BOM tree in a different color.


**Fixes**

* Fixed Uncontrolled list not showing for Manufacturing Central.
* Prevent Upgrade from running several times.
* Fixed increase and reduce stock in Inventory Logs.
* Fixed purchase price sync.
* Fixed BOM builder rows marked in shortage when shouldn't.
* Fixed BOM Hierarchy Tree in BOM Associates.

---

`1.2.12.5`

*2019-01-04*

**Changes**

* Create the right Product Levels tables from the start to avoid issues.
* Switched the Manufacturing Central product type icons from SVG to ATUM font icons.

**Fixes**

* Do not search by column if no column is selected in Manufacturing Central.
* Check that all the product levels terms are created and create them if don't exist.
* CSS fixes.
* Refactory.

---

`1.2.12.4`

*2018-12-20*

**Changes**

* Do not remove variations when changing a variable product to a BOM variable and vice versa.

**Fixes**

* Fixed sellable variations tool.
* Fixed bug in Manufacturing Central's low stock counters.
* Fixed bug in Manufacturing Central's legacy low stock counters.
* Fixed upgrade version task.
* Refactory: code style.

---

`1.2.12.3`

*2018-12-17*

**Changes**

* Using autoprefixer when compiling SCSS to CSS.

**Fixes**

* Text typo change.
* Re-added search by product name to Manufacturing Central.
* CSS fixes.

---

`1.2.12.2`

*2018-12-14*

**Changes**

* Added order type field in BOM orders table.

**Fixes**

* Fixed CSS class names.
* Fixed minimum versions checks.
* Fixed icons.

---

`1.2.12.1`

*2018-12-12*

**Fixes**

* Fixed all products showing at Manufacturing Central.

---

`1.2.12`

*2018-12-11*

**Features**

* Changed Manufacturing Central styles to fit the new ATUM designs.
* Updated readme format to be compatible with SML website.
* Performance improvements.

**Changes**

* Added minimum versions warnings.
* Adapted Product Levels data models to new ATUM data models.
* Added Bom Sellable column to ATUM product data.
* Replaced all the icons to the new ATUM icons.

**Fixes**

* Fixed WPML integration issues.
* Added variable BOM to product type dropdown on Manufacturing Central.
* Minor bug fixes.

---

`1.2.11`

*2018-10-26*

**Changes**

* CSS changes.

**Fixes**

* Fixed WPML error when WooCommerce WPML is active while WPML doesn't.
* Fixed hook name.
* Fixed variable BOM not showing on Manufacturin Central's PDF reports.
* Refactory: code style.
* Sanitization fixes.

---

`1.2.10.1`

*2018-10-4*

**Features**

* New Manufacturing List Table columns sorting.

**Fixes**

* Fixed extra fields hide/show logic not working.
* Removed MultiInventory Integration class.

---

`1.2.10`

*2018-09-27*

**Fixes**

* Fixed "Sellable variations not shown in frontend".
* Refactory.

---

`1.2.9`

*2018-09-20*

**Fixes**

* Fixed "Selling of BOM" feature when the global setting is enabled.
* Moved Multi-Inventory integration code to its own class.
* Refactory.

---

`1.2.8`

*2018-09-07*

**Features**

* Added WC Bookings add-on compatibility.

**Fixes**

* Refactory (code style).
* Minor bug fixes.

---

`1.2.7.6`

*2018-08-13*

**Features**

* Added PL variables to the WC loop.

**Fixes**

* Fixed loop PL variable products not displaying.

---

`1.2.7.5`

*2018-08-08*

**Changes**

* Changed shortage display behavior, now shortage only has a red background when its value < 0.

**Fixes**

* Fixed BOM variable products not appeared in the WC loop.
* Fixed BOM variables add to cart didn't add variations.

---

`1.2.7.4`

*2018-08-08*

**Features**

* Added initial purchase price product calc from BOM materials when enabling "Sync Purchase Price" switch.

**Fixes**

* Fixed Product Part total BOM cost set to Raw Materials Total if not Product Parts assigned when loading.

---

`1.2.7.3`

*2018-08-06*

**Fixes**

* Fixed error calculating product's BOM totals when "BOM item cost calculation" setting was disabled.

---

`1.2.7.2`

*2018-08-03*

**Fixes**

* Fixed version 1.2.7.1 causes product’s linked BOMs are inserted every time the product is saved.

---

`1.2.7.1`

*2018-07-26*

**Fixes**

* Fixed shipping tab not showing on WC's product data meta box when the "Selling of BOM" was enabled globally.
* Refactory.

---

`1.2.7`

*2018-07-26*

**Features**

* Added compatibility for Multi-Inventory add-on.

**Fixes**

* Fixed issue when multiple add-ons are active and one of them does not match the min ATUM version.
* CSS changes.
* Refactory.

---

`1.2.6.1`

*2018-07-12*

**Features**

* Added BOM fields to BOM variations.

**Changes**

* Hide "make sellable" field from regular variable products.

**Fixes**

* Refactory

---

`1.2.6`

*2018-06-29*

**Features**

* Allow to sell BOM variations.
* Added tool to variable products for setting the "Make Sellable" option for all the variations at once.

**Fixes**

* Fixed popover titles in Manufacturing Central.
* Get purchase_field meta key name from ATUM Globals.
* Fixed language text domains.
* Allow float values when inserting items to the BOM order items table.
* Fixed issue with Purchase Price Sync.
* Refactory.
* Fixed PHPCS code smells.

---

`1.2.5`

*2018-06-22*

**Features**

* Added "Sold Last Days" column to Manufacturing Central.
* Show empty product types on Manufacturing Central's filter to allow private products' filtering.

**Fixes**

* Refactory.
* Minor bug fixes.

---

`1.2.4`

*2018-06-15*

**Features**

* Added "Out of Stock Threshold" column to Manufacturing Central.

**Changes**

* Order BOM variations by menu_order if exists.
* Use the "Days to reorder" setting specified for Manufacturing Central.

---

`1.2.3`

*2018-06-07*

**Features**

* Added "Attributes" and "Advanced" tabs to BOM products.

**Fixes**

* Added ATUM 1.4.9 compatibility.
* Fixed issue that was showing BOM products in Manufacturing Central when filtering by supplier.
* CSS fixes.

---

`1.2.2`

*2018-05-30*

**Features**

* Added sticky table headers to Manufacturing Central.
* Added "Search in Column" feature to Manufacturing Central.

---

`1.2.1.3`

*2018-05-18*

**Fixes**

* Fixed version number issue.

---

`1.2.1.2`

*2018-05-17*

**Fixes**

* Fixed WooCommerce 3.0.0 compatibility issue.
* Clean up ajax product search results before returning them.

---

`1.2.1.1`

*2018-05-17*

**Fixes**

* Fixed issue when linking BOM to a product for the first time.

---

`1.2.1`

*2018-05-16*

**Features**

* Added Unmanaged Products counters.
* Added uninstall tasks.
* Allow linking BOM variations to products.
* Added "weight" column to Manufacturing Central.

**Fixes**

* Fixed bug when creating BOM type terms.
* Fixed "Is Purchasable" button group.
* Refactory.
* CSS fixes.

---

`1.2.0.1`

*2018-05-03*

**Fixes**

* Added compatibility with ATUM 1.4.5.
* Refactory.

---

`1.2.0`

*2018-04-25*

**Features**

* Added Variable Product Parts and Variable Raw Materials.
* Added compatibility for BOM variations.
* Added filter to Stock Central to show only BOM related products.
* Show "stock status" field when the WC's manage stock is disabled in BOM products.
* Added item cost for each item in the BOM list.
* Added Purchase Price Sync of the main product from all BOM attached.
* Added icons for variable BOMs.
* Added new option to Settings to choose between real or unitary BOM cost in BOM line items.

**Fixes**

* Fixed issue when cutting strings with non standard characters.
* Fix to prevent integer conversion of stock quantity in some classes.
* Fixed some filters that were not applied in Front End.
* Fixed BOM fields within variations.
* CSS fixes.
* Refactory.

---

`1.1.9`

*2018-04-05*

**Features**

* Added Totals row to Manufacturing Central.
* Added ATUM Locations tree column to Manufacturing Central.
* Added the new columns to the Manufacturing Central's help tab.

**Changes**

* Changed support links.
* Applied new style for ATUM custom fields.

**Fixes**

* Fixed ATUM 1.4.2 compatibility.
* Fixed JS for variation item quantity.
* Minor bug fixes.
* Refactory.

---

`1.1.8.1`

*2018-03-26*

**Fixes**

* Fixed old WPML access to Helpers ATUM class.
* Fixed SQL error on Manufacturing Central when accessing products in low stock status.

---

`1.1.8`

*2018-03-22*

**Changes**

* Adapted new ATUM stock management system to Product Levels.
* Updated Manufacturing Central's help tab content.
* Added WPML compatibility.

**Fixes**

* Fixed Bom Tree popup’s spinner animation.
* Minor bug fixes.
* Refactory.

---

`1.1.7`

*2018-03-05*

**Fixes**

* Fixed BOM tree button on Stock Central page.
* Added compatibility with ATUM 1.4.0.
* Fixed BOM hierarchy column name.
* Screen ID parameter management for BOM data export.

---

`1.1.6.1`

*2018-02-19*

**Fixes**

* Bypass error when a linked BOM product no longer exists.

---

`1.1.6`

*2018-01-12*

**Features**

* Added Supplier SKU column to Manufacturing Central.
* Allow searching BOM products by Supplier SKU.
* Added suppliers info to the Manufacturing Central’s help tab.
* Added hook to be able to customise the title length in MC list.
* Show a notice if ATUM is not installed or enabled.
* Check whether ATUM is installed and active before loading.

---

`1.1.5.5`

*2018-01-11*

**Fixes**

* Fixed issue in BOM product’s searches on non-standard databases.

---

`1.1.5.4`

*2018-01-02*

**Fixes**

* Bug fix on Manufacturing Central page.

---

`1.1.5.3`

*2017-12-28*

**Fixes**

* Added compatibility with ATUM 1.3.6.

---

`1.1.5.2`

*2017-12-28*

**Fixes**

* Fixed issue when installing Product Levels for the first time.

---

`1.1.5.1`

*2017-12-15*

**Features**

* Improved performance for sites with big amount of orders.

---

`1.1.5`

*2017-12-07*

**Features**

* Take advantage of the new ATUM feature for decimals in stock quantities.

---

`1.1.4.2`

*2017-12-04*

**Changes**

* Record nested BOM products once a WC order is processed.

**Fixes**

* Fixed the insufficient BOM popup showing in Stock Central page.
* Fixed the insufficient BOM popup running on purchase price changes in Manufacturing Central.

---

`1.1.4.1`

*2017-12-01*

**Fixes**

* Fixed bug when discounting BOM products' stock on purchases.

---

`1.1.4`

*2017-11-30*

**Features**

* Added data export feature to Manufacturing Central.
* Added BOM meta box to BOM products to allow nested BOMs.
* Added hierarchy column to Manufacturing Central to see the BOM's hierarchy tree.

---

`1.1.3.2`

*2017-11-22*

**Changes**

* Excluded non-sellable BOM products from WC queries.

---

`1.1.3.1`

*2017-11-15*

**Fixes**

* Avoid AJAX errors when a non-existing BOM is still linked to any product.

---

`1.1.3`

*2017-11-14*

**Features**

* Added sellable BOM feature.
* Allow setting all the products as sellable at once or individually.

---

`1.1.2`

*2017-11-02*

**Features**

* Improved compatibility with ATUM 1.3.0.
* Added "Inbound Stock" column to "Manufacturing Central".
* Added the "Purchase Price" column to "Manufacturing Central".

---

`1.1.1.1`

*2017-10-19*

**Fixes**

* Minor fixes.

---

`1.1.1`

*2017-09-15*

**Features**

* Ability to sort by "Total in Warehouse" column in Manufacturing Central table.

**Fixes**

* Fixed list table columns' sorting.

---

`1.1.0.3`

*2017-09-11*

**Fixes**

* Fixed compatibility with ATUM 1.2.7.

---

`1.1.0.2`

*2017-09-06*

**Fixes**

* Fixed issue removing materials from variations.

---

`1.1.0.1`

*2017-09-05*

**Fixes**

* Fixed issue with Low Stock indicator in materials that were part of a variation product.
* Fixed issue with materials' stock not being reduced.
* Other minor fixes.

---

`1.0.9.2`

*2017-08-31*

**Changes**

* Manufacturing Central column name changes.
* Updated the Manufacturing Central help section.

**Fixes**

* Fixed Manufacturing Central's stock calculations.

---

`1.0.9.1 =

*2017-08-25*

**Fixes**

* Fixed issue that was causing linked materials dissapearing after saving variation products.
* Fixed issue with floating numbers on linked materials' quantity box.

---

`1.0.9`

*2017-08-07*

**Fixes**

* Fixed compatibility with ATUM 1.2.5 and higher.
* Minor fixes.

---

`1.0.8`

*2017-06-05*

**Fixes**

* Adapted raw materials' stock change to new WooCommerce 3.x hooks

---

`1.0.7`

*2017-05-12*

**Fixes**

* Check the installed ATUM version before registering the add-on.
* Fixed issue with empty ATUM List Tables.

---

`1.0.6`

*2017-04-21*

**Fixes**

* Fixed issue with Processing orders not being counted.
* Fixed compatibility issues with WooCommerce 3.0.5.

---

`1.0.4`

*2017-04-07*

**Fixes**

* WooCommerce 3.0.4 compatibility fixes.

---

`1.0.3`

*2017-03-10*

**Features**

* The first public release of Product Levels add-on.