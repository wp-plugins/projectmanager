=== ProjectManager ===
Contributors: Kolja Schleich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2329191
Tags: datamanager, CMS, Content Management System
Requires at least: 2.7
Tested up to: 2.7
Stable tag: 1.8.2

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
* hook one project into user profile

See [Usage](http://wordpress.org/extend/plugins/projectmanager/other_notes/) for details on shortcodes and the template system.


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

`[project id=x template=table|gallery cat_id=y]`


Replace x with the respective project ID, the cat_id tag is optional and can be used to only display datasets of that category. If it is missing or empty all datasets of the project are displayed. ProjectManager supports templates, similar to [NextGgen Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/). It comes with two default templates *table* and *gallery* which are located in the *view/table.php* and *view/gallery.php*. *table* displays the datasets in a simple tabular output whereas *gallery* shows them in a gallery only with the images on the main page. You can design your own templates and place them in

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


You can either copy and edit the default templates or also create your own templates. To use the templates you need to put in the template tag the filename of it without extension. If you create derivatives of the gallery template, be sure to name them *gallery-yourtemplate* to activate gallery settings. For example you have created a template

`yourthemedirectory/projectmanager/gallery-custom.php`

you can use this template to display with the following code

`[project id=x template=**gallery-custom** cat_id=y]`


= Hook Project into user profile =
It is possible to hook one project into the user profile to use it as extended profile. Thus it could be used to implement a player registration in combination with my [LeagueManager Plugin](http://wordpress.org/extend/plugins/leaguemanager/). Users need the capability `project_user_profile` to use this feature. By default only Administrators and Editors have this, but you can use [Role Manager](http://www.im-web-gefunden.de/wordpress-plugins/role-manager/) for finetuning.


= Access control =
ProjectManager has three different capabilities: 

*projectmanager_admin
*manage_projects
*project_user_profile*

Users with the capability *projectmanager_admin* can edit settings, edit formfields and import datasets from CSV files. By default only Administrators have this privilege. Users with the capability *manage_projects* can add and edit datasets and those with the capability *project_user_profile* can use the profile hook feature. **Important**: The capabilities are not additive! Thus a user with capability *projectmanager_admin*, but not *manage_projects* will not be able to add and edit datasets.

== Credits ==
The ProjectManager menue icons and TinyMCE Button are taken from the Fugue Icons of http://www.pinvoke.com/.