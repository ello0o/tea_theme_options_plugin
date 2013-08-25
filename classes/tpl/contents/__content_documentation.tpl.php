<?php
//Build data
$titles = array(
    'title' => __('Documentation', TTO_I18N),
    'name' => __('Documentation', TTO_I18N),
    'slug' => '_documentation',
    'submit' => false
);
$details = array(
    array(
        'type' => 'heading',
        'title' => __('Simple, easy to use and fully integrated Theme Options for Wordpress.', TTO_I18N)
    ),
    array(
        'type' => 'p',
        'content' => __('The <a href="https://github.com/Takeatea/tea_theme_options" title="Tea Theme Options" class="openit">Tea Theme Options</a> (or <b>Tea TO</b>) allows you to easily add professional looking theme options panels to your WordPress theme.<br/>The <b>Tea TO</b> is built for <a href="http://wordpress.org" target="_blank">Wordpress</a> v3.x and uses the Wordpress built-in pages.', TTO_I18N)
    ),
    array(
        'type' => 'list',
        'contents' => array(
            __('<b>Option API</b> - A simple and standardized way of storing data in the database.', TTO_I18N),
            __('<b>Transient API</b> - Very similar to the Options API but with the added feature of an expiration time, which simplifies the process of using the wp_options database table to temporarily store cached information.', TTO_I18N),
            __('<b>Wordpress Media Manager</b> - Beautiful interface: A streamlined, all-new experience where you can create galleries faster with drag-and-drop reordering, inline caption editing, and simplified controls.', TTO_I18N),
            __('<b>Full of Options</b> - 3 kind of options used to display information, store fields values or get data from your Wordpress database. The options are made to build your Wordpress pages easily.', TTO_I18N),
            __('<b>Easier for administrators</b> - The interface is thought to be the most userfriendly. The Tea TO core adds some extra interface customisations to make your life easier.', TTO_I18N),
            __('<b>Easier for developers</b> - Create a new admin panel easily with the new dashboard. The Tea TO core is made to allow non-developer profiles to easily create the settings they need to customise their templates.', TTO_I18N)
        )
    )
);