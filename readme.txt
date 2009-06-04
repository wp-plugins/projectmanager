=== ProjectManager ===
Contributors: Kolja Schleich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2329191
Tags: datamanager, CMS, Content Management System
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 2.5.3

This plugin can be used to manage any number of projects with recurrent datasets (e.g. portrait system, dvd collection)

== Description ==

This plugin is a datamanager for any recurrent datasets. It can be used to manage and list a DVD collection, to to present portraits (e.g. athlets of a team), simple tabular calendar or anything you can think of. Below is a least of features

**Features**

* add as many different projects as you want to
* widget for any project, controlled via admin panel
* adding of form fields (text, textfield, e-mail, date, url, selection, checkbox and radio list) for each project independently
* simple search of any form field and category names
* template system to easily customize frontend display
* Ajax enabled editing of datasets
* easy adding of shortcodes via TinyMCE Button
* change colorscheme for output tables via admin panel
* dataset sorting by any form field
* import and export of datasets from/to CSV file
* hook projects into user profile
* manual drag & drop sorting of datasets

See [Usage](http://wordpress.org/extend/plugins/projectmanager/other_notes/) for details on shortcodes and the template system.

Due to the growing popularity of my plugins I have launched a [website](http://kolja.galerie-neander.de/)!

[ChangeLog](http://svn.wp-plugins.org/projectmanager/trunk/changelog.txt)

== Installation ==

To install the plugin to the following steps

1. Unzip the zip-file and upload the content to your Wordpress Plugin directory.
2. Activiate the plugin via the admin plugin page.


== Screenshots ==
1. Project Overview Page
2. Settings page
3. Add different form fields dynamically
4. Easy adding of new datasets
5. Widget control panel
6. Easy adding of shortcode tags via TinyMCE Button


== Usage ==

= Shortcodes =
You can display all datasets of one project with the following code

`[project id=ID template=table|gallery cat_id=ID orderby=name|id|order|formfields-ID order=ASC|DESC single=true|false selections=true|false]`


The following list gives a short description of each attribute:

* id: the ID of the project to display
* template: the template to load. Default templates are *table* and *gallery* (default: table)
* cat_id: ID of category to display datasets of (optional, default: show datasets of all categories)
* orderby: field to order datasets by. Either *name*, *id*, *order* or *formfields-ID* where ID is the formfield ID (optional, default: name)
* order: direction of dataset ordering, either *ASC* or *DESC* (optional, default: ASC)
* single: switch to add link to single dataset page (optional, default: 'true')
* selections: switch to control display of selections navbar for categories and dataset ordering (optional, default: 'true')

ProjectManager supports templates, similar to [NextGgen Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/). It comes with two default templates *table* and *gallery* which are located in the *view/table.php* and *view/gallery.php*. *table* displays the datasets in a simple tabular output whereas *gallery* shows them in a gallery only with the images on the main page. You can design your own templates and place them in

`yourthemedirectory/projectmanager/`


A more detailed description of the template system can be found below.

Further single datasets can be displayed directly with the following code

`[dataset id=x]`


where x is the dataset ID.

Finally ProjectManager comes with a simple search for specific datasets, also form fields or categories. To display the search form use the following code

`[project_search project_id=x template=compact|extend]`


where x is again the project ID and template can be either *compact* or *extend*.


= Templates =
You can customize the frontend output via templates. Templates shipped with the plugin are located in the *view* subdirectory. You can store your own templates in

`yourthemedirectory/projectmanager/`


You can either copy and edit the default templates or also create your own templates. To use the templates you need to put in the template tag the filename of it without extension. For example you have created a template

`yourthemedirectory/projectmanager/gallery-custom.php`


you can use this template to display with the following code

`[project id=x template=**gallery-custom** cat_id=y]`


= Extend User Profile =

Projects can be linked with the user profile. Any usr with capability *projectmanager_user* can use this feature. If the default role has this capability users get added to each project that has the hook activated and can edit data through their profile. To subsequently add a Wordpress user to a project, use the *Add WP User* Link in the dataset form, located next to the name field. The added dataset will be linked to this user.


= Custom icons for Admin Menu =
If you want to use custom icons for the admin menu put them in

`yourthemedirectory/projectmanager/icons/`

= Access control =

Since Version 2.5 ProjectManager has fine grained access control: 

* edit_projects: add and edit projects
* delete_projects:  delete projects
* projectmanager_settings: grants access to global settings page
* edit_formfields: grant editing of formfields
* edit_projects_settings: grant access to settings page of individual projects
* import_datasets: grant import/export of datasets
* edit_datasets: add datasets and edit own datasets
* edit_other_datasets: add and edit any datasets (needs also edit_datasets)
* delete_datasets: delete own datasets
* delete_other_datasets: delete any dataset (needs also delete_datasets)
* view_projects: grant read access to projects and dataset overview
* projectmanager_user: grants permission to use profile hook feature


= Notes on Formfields =

Currently there are two formfields that require a short statement: *numeric* and *currency*. The display of these two types varies among different countries. If the PHP Internationalization Functions (http://us3.php.net/manual/en//book.intl.php), requires at least PHP 5.2 (recommended is 5.2.4+), are installed you are all set. Otherwise there are two filters that can be used to manually control the format

* projectmanager_numeric
* projectmanager_currency

Note that these filters are for some reasons not applied if you use the AJAX edit. After reloading the page the display is correct.


== ChangeLog ==
See [changelog.txt](http://svn.wp-plugins.org/projectmanager/trunk/changelog.txt).

== Credits ==
The ProjectManager menue icons and TinyMCE Button are taken from the Fugue Icons of http://www.pinvoke.com/.
