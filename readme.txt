=== ProjectManager ===
Contributors: Kolja Schleich
Tags: datamanager, CMS, Content Management System
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 1.6

This plugin can be used to manage any number of projects with recurrent datasets (e.g. portrait system, dvd collection)

== Description ==

This plugin is a datamanager for any recurrent datasets. It can be used to manage and list a DVD collection,to to present portraits (e.g. athlets of a team) or anything you can think of. Below is a least of features

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
* coupling of dataset entries to user ID (since Version 1.5).

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

= Couping of dataset entries to user ID =

Since Version 1.5 the dataset entries are linked to the user ID who entered it. Users with capability "Manage Projects" can only add one dataset and also edit their dataset only. Those users with capability "Projectmanager Admin" have full access to ProjectManager. They can add as many datasets they like, edit every dataset, change owner of a dataset, edit FormFields and edit Settings as well as Color Scheme. By default Blog Administrators have this access. Editors only get the "Manage Projects" Permission. Use [Role Manager](http://www.im-web-gefunden.de/wordpress-plugins/role-manager/) Plugin to fine control permissions. Thus ProjectManager can now also be used as extended profile for users.

= Customization =

Since version 1.3 it is possible to customize frontend output via plugin hooks. See functions

* getDatasetList
* getGallery
* getSingleView

in projectmanager.php for details.


== ChangeLog ==

**Version 1.6**, *January-18-2009*

- NEW: implemented Slideshow Widget
- usability enhancements
- code cleaning
- style enhancements

**Version 1.5**, *January-05-2009*

- NEW: coupled datasets to user id who entered them.

**Version 1.4**, *December-19-2008*

- NEW: selection, checkbox list, radio list form field types
- some code cleaning and fixes

**Version 1.3**, *December-01-2008*

- support for multiple categorization
- customization of dataset output via wordpress hooks
- search for category names (comma separated list of cat names)
- set colorschemes of tables

**Version 1.2.3**, *November-24-2008*

- option to add direct link to project in navigation panel
- fixed bug in project creation

**Version 1.2.2**, *November-23-2008*

- fixed upgrade bug

**Version 1.2.1**, *November-23-2008*

- fixed database collation

**Version 1.2**, *November-22-2008*

- Ajax editing of datasets
- display of specific group only
- some other minor new features

**Version 1.1**, *November-21-2008*

- major restructuring of plugin
- full control of display via shortcodes
- added TinyMCE Button for better usability
