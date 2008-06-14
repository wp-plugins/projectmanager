=== ProjectManager ===
Contributors: Kolja Schleich
Tags: Sidebar, visitor counter
Requires at least: 2.2
Tested up to: 2.5
Stable tag: 1.0

This plugin can be used to manage any number of projects with recurrent datasets (e.g. portrait system, dvd collection)

== Description ==

Manage any project consisting of recurrent datasets. This could be portrait systems (linke author profiles, athlete portraits), dvd collection, link collections or anything you can think of. To display projects in the frontend you need to create a template and put it in the template subdirectory. The plugin comes with a bunch of standard templates which can be adopted to your needs.

*Features*

- add as many different projects as you want to
- widget use for any project, controlled via checkbox
- adding of form fields (text, textfield, e-mail, date, url) for each project independently
- simple search of any form field
- templates to display projects in frontend
- output of datasets as table, list or description list
- batch deletion of projects and datasets

*Possible Applications*

- user profile system (e.g. author profile, athlet portraits)
- dvd collection, link collection
- architect projects

You can also put additional code into a 'my-hacks.php' file in the base directory of the plugin to tweak the plugin.

**Usage**

- Add New Project in *Settings -> Projectmanager*. Then go to *Manage -> Projects*
- If there is only one project in the database the Manage Link will point directly to this project
- Select a project and go to *Settings*. There a template for frontend output and form fields can be set
- You are ready to add datasets :)


To display projects in the frontend you need to add the following tag into a post or page

`[print_projects id=$project_id grp_id=$grp_id]`

Substitute $project_id with the respective project ID to display. The `grp_id` tag is optional to display only the datasets of a specific group of the project.

*Note: I tested the plugin as best as I can, but there might be some bugs to fix. Please report bugs or feature requests to: kolja.schleich@googlemail.com*


== Installation ==

To install the plugin to the following steps

1. Unzip the zip-file and upload the content to your Wordpress Plugin directory.
2. Activiate the plugin via the admin plugin page.