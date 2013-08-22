=== Tea Theme Options ===
Contributors: achrafchouk
Donate link: https://github.com/Takeatea/tea_theme_options
Tags: theme, options, pages, custom post types
Requires at least: 3.4.2
Tested up to: 3.6
Stable tag: 1.4.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Tea Theme Options allows you to easily add professional looking theme options panels to your WordPress theme.


== Description ==

The [Tea Theme Options](http://takeatea.github.com/tea_to_wp/) (or **Tea TO**) allows you to easily add professional looking theme options panels to your WordPress theme. The **Tea TO** is built for [Wordpress](http://wordpress.org "CMS Wordpress") v3.x and uses the Wordpress built-in pages.

* [**Options API**](http://codex.wordpress.org/Options_API) - A simple and standardized way of storing data in the database.
* [**Transients API**](http://codex.wordpress.org/Transients_API) - Very similar to the Options API but with the added feature of an expiration time, which simplifies the process of using the wp_options database table to temporarily store cached information.
* **Wordpress Media Manager** - Beautiful interface: A streamlined, all-new experience where you can create galleries faster with drag-and-drop reordering, inline caption editing, and simplified controls.
* **Full of Options** - 4 kinds of options used to display information, store fields values or get data from your Wordpress database. The options are made to build your Wordpress pages easily.
* **Easier for administrators** - The interface is thought to be the most userfriendly. The Tea TO core adds some extra interface customisations to make your life easier.
* **Easier for developers** - Create a new admin panel easily with the new dashboard. The Tea TO core is made to allow non-developer profiles to easily create the settings they need to customise their templates.
* **Custom Post Types** - Create a custom post type from your uniq dashboard easily.


All available fields

**Display fields**:

* Breakline or Horizontal rule - Can be usefull.
* Heading - Display a simple title.
* Paragraphe - A simple text content.
* List items - Show items in an unordered list.
* Features - **Special field** used only to build this documentation page (but you can use it as well).

**Common fields**:

* Basic Text - The most basic of form fields. Basic, but important.
* Email, number and more - The most basic of form fields extended. You can choose between email, password, number, range, search and url.
* Hidden field - A hidden field, if you need to store a special data.
* Textarea - Again basic, but essencial.
* Checkbox - No need to introduce it...
* Radio - Its brother (or sister, as you want).
* Select - Provide a list of possible option values.
* Multiselect - The same list as previous one but with multichoices.

**Special fields**:

* Background - Great for managing a complete background layout with options.
* Color - Need some custom colors? Use the Wordpress color picker.
* Google Fonts - Want to use a custom font provided by Google Web Fonts? It's easy now.
* Include - Offers the possibility to include a php file.
* RTE - Want a full rich editing experience? Use the Wordpress editor.
* Social - Who has never needed social links on his website? You can manage them easily here.
* Wordpress Upload - Upload images (only for now), great for logo or default thumbnail. It uses the [Wordpress Media Manager](http://codex.wordpress.org/Version_3.5#Highlights).

**Wordress fields**:

* Categories - Display a list of Wordpress categories.
* Menus - Display a list of Wordpress menus.
* Pages - Display a list of Wordpress pages.
* Posts - Display a list of Wrdpress posts.
* Post Types - Display a list of Wordpress posttypes.
* Tags - Display a list of Wordpress tags.

**Social Networks fields**:

* Flickr - Make a bridge between your website and your Flickr profile.
* Instagram - Make a bridge between your website and your Instagram profile.
* Twitter - Make a bridge between your website and your Twitter profile due to the new API v1.1.


**Take a Tea**

* http://takeatea.com
* http://twitter.com/takeatea
* http://github.com/takeatea

**Achraf Chouk**

* http://fr.linkedin.com/in/achrafchouk/
* http://twitter.com/crewstyle
* http://github.com/crewstyle

Copyright 2013 [Take a tea](http://takeatea.com "Take a tea")  
Infusé par Take a tea ;)


== Installation ==

**To get started**, checkout or download the https://github.com/takeatea/tea_theme_options package into the `wp-content/plugins/`

    git clone https://github.com/takeatea/tea_theme_options

Check your new `tea_theme_options` folder is created in your plugins directory.


Go to your plugins page `your_wp_website/wp-admin/plugins.php` and enable the Tea Theme Options plugin.

Create your new first page settings (as capability, description and submit button).
Click on the **Tea T.O.** page, select the **Add a custom page** menu and fill the form.
NOTA: all created pages will only appear if contents are defined.

Repeat the process as you want/need :)
You can easily create Custom post types from the **Add a custom post type** menu: as usual, just fill the form.


All you have to do from the **Tea T.O.** page is to click on your wanted page and use the bottom Adding contents form. For each field, just follow the instructions and fill the form simply.
NOTA: the **Tea TO** uses [Transient Wordpress API](http://codex.wordpress.org/Transients_API) to stock options.


Here is the latest step: check quickly your new panel options.

* Go to your `your_wp_website/wp-admin`
* Log in to your admin panel
* **See that you have a new Link in your admin sidebar**

That's all to begin working with **Tea TO**


== Frequently Asked Questions ==

= No question yet =

...


== Screenshots ==

1. Dashboard
2. Empty dashboard
3. Adding a page
4. Editing a page
5. All available fields
6. A social network block
7. An example page


== Changelog ==

= 1.4.4 (August 22, 2013) =
Add new uninstall.php to make uninstall works
Fix uninstall bug

= 1.4.3 (August 21, 2013) =
Better compatibility with Wordpress 3.4.2 and uses of register_uninstall_hook instead of register_desactivation_hook

= 1.4.2 (August 21, 2013) =
Main class contains now only what she is supposed to do: a container for everything

= 1.4.1 (August 21, 2013) =
Fix retrocompatibility with Wordpress 3.4 version

* **Fix:**
  * fix bug on footer layout

= 1.4.0 (August 20, 2013) =
Get real advices from Nicolas A. <https://twitter.com/nicolassing> from Take a Tea :)

* **Edit:**
  * all components are now in different folders
  * each field in a single package
  * checkbox, multiselect, radio and select or now uniq (choice component is dead)
* **New:**
  * Abstract classes are now introduced for fields

= 1.3.2.1 (August 14, 2013) =
* **Fix:**
  * fix bug on small icon

= 1.3.2 (August 14, 2013) =
NEW CUSTOM POST TYPE
You can now manage all your CPT directly from the Tea TO dashboard

= 1.3.1 (August 13, 2013) =
Responsified!

= 1.3.0 (August 10, 2013) =
NEW PLUGIN VERSION
* with new images
* with a Wordpress plugin readme version

= 1.2.12 (July 31, 2013) =
Fix small bug in Twitter template, display update date on network templates, adds descriptions on documentation page

= 1.2.12 (July 31, 2013) =
Make all business code in one single function, add twitter connection and make new Wordpress CRON schedules to update DB

* **New:**
  * new twitter field with API, connection and more
  * new Wordpress CRON schedules to update networks contents in DB and cache
* **Edit:**
  * business code for networks is now in one single function: updateNetwork

= 1.2.11 (July 30, 2013) =
Fix some bugs with Instagram recent medias and FlickR username

* **Fix:**
  * fix Instagram recent medias bug
  * fix FlickR username bug

= 1.2.10 (July 30, 2013) =
New flickr field with API

* **New:**
  * new flickr field with API, connection and more

= 1.2.9 (July 29, 2013) =
Edit header layout without form, new instagram field with API, new _del_option function

* **Edit:**
  * add submit option in header layout: no form without button ;)
* **New:**
  * new instagram field with API, connection and more
  * new _del_option function to delete option from DB and transient

= 1.2.8 (July 26, 2013) =
Detele Date field and add new RTE field.

* **Edit:**
  * delete date field 'cause it useless too...
  * optimize JS scripts
* **New:**
  * new rte field to get all Wordpress powaaa :)

= 1.2.7 (July 26, 2013) =
Update Background field and fix lots of bugs

* **Edit:**
  * update background field to a better experience
  * delete image field 'cause it was... hum... useless
* **Fix:**
  * Enqueue new media script to be compliant with Wordpress 3.5.2

= 1.2.6 (July 23, 2013) =
Update Google font field and optimize script

* **Edit:**
  * delete all switches to let if/else instead
  * update Google font field to display to 18 fonts
* **Fix:**
  * Fix small bug on README.md

= 1.2.5 (July 11, 2013) =
New usefull field and fix bug

* **Fix:**
  * fix multiselect forgotten field
* **New:**
  * new include field to display everything you want

= 1.2.4 (July 11, 2013) =
README.md up to date with default documentation page

* **Edit:**
  * README.md

= 1.2.3 (May 27, 2013) =
Get real advices from Xavier C. <https://twitter.com/xavismeh> :)

* **Edit:**
  * assets are now enabled in all Wordpress admin pages (a big news is coming ;))
  * back to checkbox/select/radio instead of choice (not userfriendly)
  * icons are now defined in the TeaTO and not settable anymore
  * no more "__categories" special name anymore: you can set "__category" for simple or multiple choices
  * public keys are now privates
* **Fix:**
  * fix hidden field which does not need description or title attributes
* **New:**
  * here comes the new default TeaTO Documentation page (appears even if you have no settings)
  * new features and list fields to display contents
  * new way to disable Wordpress scripts/styles on the TeaTO custom pages

= 1.2.2 (May 27, 2013) =
Fix some bugs, adds new fields, adds new default documentation page, better media-views integration, new scripts, and more...
Details on the next commit

= 1.2.1 (May 14, 2013) =
Fix some small bugs

* **Fix:**
  * delete a forgotten enclosure
* **New:**
  * add TeaTO version

= 1.2.0 (May 14, 2013) =
Add some new fields and fix small bugs

* **Edit:**
  * edit all TeaTO definition by setting only pages (no more subpages now)
  * edit category/menu/page/post/posttype/tag fields with some extra options in a WordpressContents function
* **Fix:**
  * fix the empty color value
* **New:**
  * add Background field with all needed options
  * add new page config to hide submit button
  * prepare default documentation page with no options

= 1.1.1 (May 03, 2013) =
Add some new fields and fix small bugs

* **Edit:**
  * edit social field to include label and link data
* **New:**
  * add Wordpress admin bar links
  * add some Defaults values
  * add _set_option() function to the Tea TO package
  * add Paragraph field
  * add rows option to textarea field
  * prepare RTE and Date new fields

= 1.1.0 (April 25, 2013) =
Add some new fields and fix small bugs

* **Edit:**
  * edit br and hr fields
  * edit text field with some extra options instead of number/range/email/password/search/url fields
* **Fix:**
  * fix font field
* **New:**
  * add _get_option() function to the Tea TO package
  * add Choice field with some extra options instead of checkbox/radio/select/multiselect fields

= 1.0.3 (March 31, 2013) =
Some improvments on checkbox fields and new social icons

* **New:**
  * add an "Un/select all checkboxes" on image and social fields
  * add Bloglovin, Hellocoton and Youtube social icons

= 1.0.2 (March 31, 2013) =
Add a small checkbox feature

* **New:**
  * add an "Un/select all checkboxes" on checkbox field

= 1.0.1 (March 26, 2013) =
List now all next todos and add some extra features

* **Fix:**
  * fix title display on breadcrumb
  * fix JS media popin
* **New:**
  * uses now the Wordpress Media Uploader
  * uses now the Wordpress Color field
  * add information in function comments
  * add admin warning messages
  * add Instagram social button

= 1.0.0 (October 30, 2012) =

* **Initial release**


== Upgrade Notice ==

Nothing to do.

