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
        'content' => __('The <a href="http://takeatea.github.com/tea_to_wp/" title="Tea Theme Options" class="openit">Tea Theme Options</a> (or <b>Tea TO</b>) allows you to easily add professional looking theme options panels to your WordPress theme.<br/>The <b>Tea TO</b> is built for <a href="http://wordpress.org" target="_blank">Wordpress</a> v3.x and uses the Wordpress built-in pages.', TTO_I18N)
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
    ),
    array(
        'type' => 'heading',
        'title' => __('Theme Options Field Types.', TTO_I18N)
    ),
    array(
        'type' => 'features',
        'title' => __('Display fields.', TTO_I18N),
        'contents' => array(
            array(
                'title' => __('Breakline or Horizontal rule', TTO_I18N),
                'content' => __('Can be usefull.', TTO_I18N),
                'code' => 'array(
    "type" => "br", //replace by "hr"
);'
            ),
            array(
                'title' => __('Heading', TTO_I18N),
                'content' => __('Display a simple title.', TTO_I18N),
                'code' => 'array(
    "type" => "heading",
    "title" => "My heading title"
);'
            ),
            array(
                'title' => __('Paragraph', TTO_I18N),
                'content' => __('A simple text content.', TTO_I18N),
                'code' => 'array(
    "type" => "p",
    "content" => "My long text content as Lorem Ipsum"
);'
            ),
            array(
                'title' => __('List items', TTO_I18N),
                'content' => __('Show items in an unordered list.', TTO_I18N),
                'code' => 'array(
    "type" => "list",
    "contents" => array(
        "My first content",
        "My second content",
        "My third content",
        "Simply..."
    )
);'
            ),
            array(
                'title' => __('Features', TTO_I18N),
                'content' => __('<b>Special field</b> used only to build this documentation page (but you can use it as well).', TTO_I18N),
                'code' => 'array(
    "type" => "features",
    "title" => "My features title",
    "contents" => array(
        array(
            "title" => "My 1st feature title",
            "content" => "My 1st feature long content",
            "code" => "My 1st feature HTML code to display in popin after clicking on a button"
        )
        //You can repeat this array as much as you want
    )
);'
            )
        )
    ),
    array(
        'type' => 'features',
        'title' => __('Common fields.', TTO_I18N),
        'contents' => array(
            array(
                'title' => __('Basic Text', TTO_I18N),
                'content' => __('The most basic of form fields. Basic, but important.', TTO_I18N),
                'code' => 'array(
    "type" => "text",
    "title" => "My text title",
    "id" => "my_text_field_id",
    "std" => "My default value",
    "placeholder" => "My usefull placeholder",
    "description" => "My field description"
);'
            ),
            array(
                'title' => __('Email, number and more', TTO_I18N),
                'content' => __('The most basic of form fields extended. You can choose between email, password, number, range, search and url.', TTO_I18N),
                'code' => 'array(
    "type" => "text",
    "title" => "My text title",
    "id" => "my_number_field_id",
    "std" => "My default value",
    "placeholder" => "My usefull placeholder",
    "description" => "My field description",
    "options" => array(
        "type" => "number",
        "min" => 10,
        "max" => 100,
        "step" => 5
    )
);'
            ),
            array(
                'title' => __('Hidden field', TTO_I18N),
                'content' => __('A hidden field, if you need to store a special data.', TTO_I18N),
                'code' => 'array(
    "type" => "hidden",
    "id" => "my_hidden_field_id",
    "std" => "My default value"
);'
            ),
            array(
                'title' => __('Textarea', TTO_I18N),
                'content' => __('Again basic, but essencial.', TTO_I18N),
                'code' => 'array(
    "type" => "textarea",
    "title" => "My textarea title",
    "id" => "my_textarea_field_id",
    "rows" => 10
);'
            ),
            array(
                'title' => __('Checkbox', TTO_I18N),
                'content' => __('No need to introduce it...', TTO_I18N),
                'code' => 'array(
    "type" => "checkbox",
    "title" => "My choices title",
    "id" => "my_checkbox_field_id",
    "std" => array("3", "5"),
    "options" => array(
        3 => "Three",
        4 => "Four,
        5 => "Five"
    )
);'
            ),
            array(
                'title' => __('Radio', TTO_I18N),
                'content' => __('Its brother (or sister, as you want).', TTO_I18N),
                'code' => 'array(
    "type" => "radio",
    "title" => "My choice title",
    "id" => "my_radio_field_id",
    "std" => "3",
    "options" => array(
        3 => "Three",
        4 => "Four,
        5 => "Five"
    )
);'
            ),
            array(
                'title' => __('Select', TTO_I18N),
                'content' => __('Provide a list of possible option values.', TTO_I18N),
                'code' => 'array(
    "type" => "select",
    "title" => "My choice title",
    "id" => "my_select_field_id",
    "std" => "3",
    "options" => array(
        3 => "Three",
        4 => "Four,
        5 => "Five"
    )
);'
            ),
            array(
                'title' => __('Multiselect', TTO_I18N),
                'content' => __('The same list as previous one but with multichoices.', TTO_I18N),
                'code' => 'array(
    "type" => "select",
    "title" => "My choices title",
    "id" => "my_multiselect_field_id",
    "std" => array("3", "5"),
    "multiple" => true,
    "options" => array(
        3 => "Three",
        4 => "Four,
        5 => "Five"
    )
);'
            )
        )
    ),
    array(
        'type' => 'features',
        'title' => __('Special fields.', TTO_I18N),
        'contents' => array(
            array(
                'title' => __('Background', TTO_I18N),
                'content' => __('Great for managing a complete background layout with options.', TTO_I18N),
                'code' => 'array(
    "type" => "background",
    "title" => "My background field title",
    "id" => "my_background_field_id",
    "default" => true,
    "std" => array(
        "image" => "my_background_default_url",
        "color" => "#ffffff",
        "repeat" => "no-repeat",
        "position_x" => "left",
        "position_y" => "top"
    ),
    "description" => "My background field description"
);'
            ),
            array(
                'title' => __('Color', TTO_I18N),
                'content' => __('Need some custom colors? Use the Wordpress color picker.', TTO_I18N),
                'code' => 'array(
    "type" => "color",
    "title" => "My color field title",
    "id" => "my_color_field_id",
    "std" => "#ffffff",
    "description" => "My color field description"
);'
            ),
            array(
                'title' => __('Google Fonts', TTO_I18N),
                'content' => __('Want to use a custom font provided by Google Web Fonts? It\'s easy now.', TTO_I18N),
                'code' => 'array(
    "type" => "font",
    "title" => "My font field title",
    "id" => "my_font_field_id",
    "std" => "Lobster",
    "description" => "My font field description",
    "default" => true,
    "options" => array(
        "PT+Sans" => array("PT Sans", "400,700")
    )
);'
            ),
            array(
                'title' => __('Include', TTO_I18N),
                'content' => __('Offers the possibility to include a php file.', TTO_I18N),
                'code' => 'array(
    "type" => "include",
    "title" => "My include field title",
    "file" => "my_path_file"
);'
            ),
            array(
                'title' => __('RTE', TTO_I18N),
                'content' => __('Want a full rich editing experience? Use the Wordpress editor.', TTO_I18N),
                'code' => 'array(
    "type" => "rte",
    "title" => "My RTE field title",
    "id" => "my_rte_field_id",
    "description" => "My RTE field description"
);'
            ),
            array(
                'title' => __('Social', TTO_I18N),
                'content' => __('Who has never needed social links on his website? You can manage them easily here.', TTO_I18N),
                'code' => 'array(
    "type" => "social",
    "title" => "My social field title",
    "id" => "my_social_field_id",
    "description" => "My social field description",
    "default" => array(
        "addthis", "bloglovin", "deviantart", "dribbble", "facebook",
        "flickr", "forrst", "friendfeed", "hellocoton", "googleplus",
        "instagram", "lastfm", "linkedin", "pinterest", "rss",
        "skype", "tumblr", "twitter", "vimeo", "youtube"
    )
);'
            ),
            array(
                'title' => __('Wordpress Upload', TTO_I18N),
                'content' => __('Upload images (only for now), great for logo or default thumbnail. It uses the <a href="http://codex.wordpress.org/Version_3.5#Highlights" class="openit">Wordpress Media Manager</a>', TTO_I18N),
                'code' => 'array(
    "type" => "upload",
    "title" => "My upload field title",
    "id" => "my_upload_field_id",
    "description" => "My upload field description",
    "library" => "image", //Default value, tells to accept only images
    "multiple" => true //If you need to upload a gallery
);'
            )
        )
    ),
    array(
        'type' => 'features',
        'title' => __('Wordpress fields', TTO_I18N),
        'contents' => array(
            array(
                'title' => __('Categories', TTO_I18N),
                'content' => __('Display a list of Wordpress categories.', TTO_I18N),
                'code' => 'array(
    "type" => "categories",
    "id" => "my_categories_field_id"
);'
            ),
            array(
                'title' => __('Menus', TTO_I18N),
                'content' => __('Display a list of Wordpress menus.', TTO_I18N),
                'code' => 'array(
    "type" => "menus",
    "id" => "my_menus_field_id"
);'
            ),
            array(
                'title' => __('Pages', TTO_I18N),
                'content' => __('Display a list of Wordpress pages.', TTO_I18N),
                'code' => 'array(
    "type" => "pages",
    "id" => "my_pages_field_id"
);'
            ),
            array(
                'title' => __('Posts', TTO_I18N),
                'content' => __('Display a list of Wrdpress posts.', TTO_I18N),
                'code' => 'array(
    "type" => "posts",
    "id" => "my_posts_field_id"
);'
            ),
            array(
                'title' => __('Post Types', TTO_I18N),
                'content' => __('Display a list of Wordpress posttypes.', TTO_I18N),
                'code' => 'array(
    "type" => "posttypes",
    "id" => "my_posttypes_field_id"
);'
            ),
            array(
                'title' => __('Tags', TTO_I18N),
                'content' => __('Display a list of Wordpress tags.', TTO_I18N),
                'code' => 'array(
    "type" => "tags",
    "id" => "my_tags_field_id"
);'

            )
        )
    ),
    array(
        'type' => 'features',
        'title' => __('Connection fields', TTO_I18N),
        'contents' => array(
            array(
                'title' => 'FlickR',
                'content' => __('Make a bridge between your website and your FlickR profile.', TTO_I18N),
                'code' => 'array(
    "type" => "flickr",
    "title" => "Welcome to FlickR",
    "description" => "It\'s your medias. Yours, yours and yours."
);'
            ),
            array(
                'title' => 'Instagram',
                'content' => __('Make a bridge between your website and your Instagram profile.', TTO_I18N),
                'code' => 'array(
    "type" => "instagram",
    "title" => "Welcome to Instagram",
    "description" => "It\'s your profile. You, you and you."
);'
            ),
            array(
                'title' => 'Twitter',
                'content' => __('Make a bridge between your website and your Twitter profile.', TTO_I18N),
                'code' => 'array(
    "type" => "twitter",
    "title" => "Welcome to Twitter",
    "description" => "It\'s your tweets. Yours, yours and yours."
);'
            )
        )
    )
);