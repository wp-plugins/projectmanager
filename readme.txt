=== ProjectManager ===
Contributors: Kolja Schleich
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2329191
Tags: datamanager, CMS, Content Management System
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.7

This plugin can be used to manage any number of projects with recurrent datasets (e.g. portrait system, dvd collection)

== Description ==

This plugin is a datamanager for any recurrent datasets. It can be used to manage and list a DVD collection, to to present portraits (e.g. athlets of a team), simple tabular calendar or anything you can think of. Below is a least of features

**Features**

* add as many different projects as you want to
* widget use for any project, controlled via admin panel
* adding of form fields (text, textfield, e-mail, date, url, selection, checkbox and radio list) for each project independently
* simple search of any form field and category names
* usage of Wordpress Category System for grouping
* various output formats (table, list, gallery)
* Ajax enabled editing of datasets
* simple display in frontend over shortcodes
* easy adding of shortcodes via TinyMCE Button
* change colorscheme for output tables
* coupling of dataset entries to user ID
* dataset sorting by form fields
* import and export of datasets from/to CSV file
* hook one project into user profile

After adding a project, check out the settings and form field pages first. The frontend display of datasets is controlled via shortcodes, see the Usage section for details.


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

Use 

`[prjctmngr_search_form=$project_id,$pos]`

to display the search formular. Substitute $project_id with the respective project ID to display. $pos must have one of the three values 'right', 'center', 'left'.

Use

`[prjcgmngr_group_selection=$project_id,$type,$pos]`

to display the group selections. $project_id is again the ID of the project to display. $type can be 'dropdown' or 'list'. $pos has the same values as above.

Use

`[dataset_list=$project_id,$group,$type]`

to display the datasets of the project with ID=$project_id in a simple form. Display types can be 'table', 'ul', 'ol'. $group is either the groupID of a specific group or left empty.

Use

`[dataset_gallery=$project_id,$cols,$group]`

to display the datasets as a gallery, with a picture if one is supplied. $cols is the number of columns. $group is same as above.

Search by category names with comma separated list.

= Coupling of dataset entries to user ID =

Since Version 1.5 the dataset entries are linked to the user ID who entered it. Users with capability "Manage Projects" can only add one dataset and also edit their dataset only. Those users with capability "Projectmanager Admin" have full access to ProjectManager. They can add as many datasets they like, edit every dataset, change owner of a dataset, edit FormFields and edit Settings as well as Color Scheme. By default Blog Administrators have this access. Editors only get the "Manage Projects" Permission. Use [Role Manager](http://www.im-web-gefunden.de/wordpress-plugins/role-manager/) Plugin to fine control permissions. Thus ProjectManager can now also be used as extended profile for users.

= Customization =

Since version 1.3 it is possible to customize frontend output via function hooks. See functions

* getDatasetList
* getGallery
* getSingleView

in projectmanager.php for details.
