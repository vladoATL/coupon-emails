=== Coupon Emails ===
Contributors: perties2
Tags: coupon, Name Day, meniny, Birthday, Slovak, Czech, email, review
Requires at least: 5.8
Tested up to: 6.3.1
Stable tag: 5.8
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin automatically generates emails with unique coupons for customers' birthdays, their name days, after makeing an order and after reviewed a poriduct with many customization options.

== Description ==
This plugin is an excellent tool when marketing with discount coupons. It generates unique coupons for each customer according to configurable filters. The coupon email can be sent on the customer's birthday, their name day, a certain number of days after the order, to customers who haven't ordered anything for a long time or to one-time filtered customers. You can set up a review thank you email with coupon as well.

The date of birth field is automatically added to the checkout and user profile page. Name days are currently available for Slovakia, Czech Republic, Austria and Hungary. For Czech names, it is possible to set a Czech salutation.

### Installation 

1. Upload `coupon-email` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to plugin setting page and configure plugin : Marketing -> Coupon Emails
4. Always make sure the Run in test mode checkbox is checked first. Cancel test mode only after you have thoroughly tested the setup.

== Frequently Asked Questions ==

= Where can I find the plugin settings page?

The reload page can be found in the menu Marketing -> Coupon Emails

= How to prevent sending unwanted emails?

Keep the 'Run in test mode' checkbox checked when setting the filters and the email body text. All email messages will be then sent to the admin or to the email address set in BCC.


== Screenshots ==


= Third Party Plugin Support =
Coupon Emails plugin is officially compatible with the following plugins:
* [WooCommerce]
* [Advanced Coupons](https://wordpress.org/plugins/advanced-coupons-for-woocommerce-free/)
* [Site Reviews](https://wordpress.org/plugins/site-reviews/)

== Changelog ==
= 1.0.1 =
- Release version

= 0.3.4 =
- Compatibility with Site Reviews plugin

= 0.3.3 =
- Add after reviewed email with coupon

= 0.3.2 =
- Add Polish calendar
- Add Croatian calendar
- Add Spanish calendar

= 0.3.1 =
- Add statistics
- Czech translation file

= 0.3.0 =
* Add after order  email with coupon
* Generate SQL refactoring
* Enable SQL log
* Plugin name change

= 0.2.7 =
* Add one time email with coupon

= 0.2.6 =
* Add Reorder email with coupon

= 0.2.5 =
* Send Birthday coupon only once a year

= 0.2.4 =
* Add Birthday coupon

= 0.2.3 =
* Code refactoring

= 0.2.2 =
* Setup for My Day

= 0.2.1 =
* Setup for My Day implementation

= 0.1.5 =
* Cron not to run when functionality disabled

= 0.1.4 =
* Add product categories include and exclude
* Add coupon individual use only
* Add exclude sale items
* Add min and max amount to spend

= 0.1.3 =
* Download current calendar in .csv format
* Display first name with capital first letter regardless the data
* Add Discount type
* Add Coupon Description
* Add Free Shipping

= 0.1.2 =
* Length of coupon can by set
* Delete unused expired coupons can by set
* No need to enter cron time in UTC time anymore
* Added Austrian and Hungarian calendar
* Fix sending to CC email

= 0.1.1 =
* This is the initial code