<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Theme Options
 * @since Tea Theme Options 1.4.0
 *
 * Plugin Name: Tea Theme Options
 * Version: 1.4.0
 * Plugin URI: https://github.com/Takeatea/tea_to_wp
 * Description: The Tea Theme Options (or "Tea TO") allows you to easily add professional looking theme options panels to your WordPress theme.
 * Author: Achraf Chouk
 * Author URI: http://takeatea.com/
 * License: GPL v3
 *
 * Tea Theme Options Plugin
 * Copyright (C) 2013, Achraf Chouk - ach@takeatea.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}

/*if(class_exists($field_class) && method_exists($field_class, 'enqueue')){
    $enqueue = new $field_class('','',$this);
    $enqueue->enqueue();
}*/
//---------------------------------------------------------------------------------------------------------//

//Usefull definitions for the Tea Theme Options
defined('TTO_VERSION')      or define('TTO_VERSION', '1.4.0');
defined('TTO_I18N')         or define('TTO_I18N', 'teathemeoptions');
defined('TTO_DURATION')     or define('TTO_DURATION', 86400);
defined('TTO_INSTAGRAM')    or define('TTO_INSTAGRAM', 'http://takeatea.com/instagram.php');
defined('TTO_TWITTER')      or define('TTO_TWITTER', 'http://takeatea.com/twitter.php');
defined('TTO_URI')          or define('TTO_URI', plugins_url().'/'.basename(dirname(__FILE__)).'/');
defined('TTO_PATH')         or define('TTO_PATH', plugin_dir_path(__FILE__));
defined('TTO_BASENAME')     or define('TTO_BASENAME', plugin_basename(__FILE__));
defined('TTO_ACTION')       or define('TTO_ACTION', 'tea_json_options');
defined('TTO_NONCE')        or define('TTO_NONCE', 'tea-ajax-nonce');


//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Theme Option page.
 *
 * To get its own settings
 *
 * @since Tea Theme Options 1.4.0
 * @todo Special field:     Typeahead, Date, Geolocalisation
 * @todo Shortcodes panel:  Youtube, Vimeo, Dailymotion, Google Maps, Google Adsense,
 *                          Related posts, Private content, RSS Feed, Embed PDF,
 *                          Price table, Carousel, Icons
 * @todo Custom Post Types: Project, Carousel
 */
class Tea_Theme_Options
{
    //Define protected vars
    protected $adminmessage;
    protected $breadcrumb = array();
    protected $capability = 'edit_pages';
    protected $categories = array();
    protected $can_upload = false;
    protected $current = '';
    protected $directory = array();
    protected $duration = 86400;
    protected $icon_small = '/img/teato/icn-small.png';
    protected $icon_big = '/img/teato/icn-big.png';
    protected $identifier;
    protected $includes = array();
    protected $index = null;
    protected $is_admin;
    protected $pages = array();
    protected $wp_contents = array();

    /**
     * Constructor.
     *
     * @uses add_filter()
     * @uses current_user_can()
     * @uses load_plugin_textdomain()
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses wp_next_scheduled()
     * @uses wp_schedule_event()
     * @param string $identifier Define the plugin main slug
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __construct($identifier = 'tea_theme_options')
    {
        //Check if we are in admin panel
        $this->setIsAdmin();

        //Admin panel
        if ($this->getIsAdmin())
        {
            //i18n
            load_plugin_textdomain(TTO_I18N, false, dirname(TTO_BASENAME));

            //Registration hooks
            register_activation_hook(__FILE__, array(&$this, '__adminInstall'));
            register_deactivation_hook(__FILE__, array(&$this, '__adminUninstall'));

            //Check identifier
            if (empty($identifier))
            {
                $this->adminmessage = __('Something went wrong in your parameters definition. You need at least an identifier.', TTO_I18N);
                return false;
            }

            //Define parameters
            $this->can_upload = true; //current_user_can('upload_files');
            $this->identifier = $identifier;

            //Set default duration and directories
            $this->setDuration();
            $this->setDirectory();

            //Get current page
            $this->current = isset($_GET['page']) ? $_GET['page'] : '';

            //Update options...
            if (isset($_REQUEST['tea_to_settings']))
            {
                $this->updateOptions($_REQUEST, $_FILES);
            }
            //...Or add page or custom post type...
            else if (isset($_REQUEST['tea_to_dashboard']))
            {
                $this->updateContents($_REQUEST);
            }
            //...Or update network data
            else if (isset($_REQUEST['tea_to_callback']) || isset($_REQUEST['tea_to_network']))
            {
                $this->updateNetworks($_REQUEST);
            }

            //Build page menus
            $this->buildMenus();
        }

        //Define custom schedule
        if (!wp_next_scheduled('tea_task_schedule'))
        {
            wp_schedule_event(time(), 'hourly', 'tea_task_schedule');
        }

        //Register custom schedule filter
        add_filter('tea_task_schedule', array(&$this, '__cronSchedules'));

        //Build CPT
        $this->buildCustomPostTypes();
    }


    //--------------------------------------------------------------------------//

    /**
     * MAIN FUNCTIONS
     **/

    /**
     * Register custom post types.
     *
     * @uses add_action()
     *
     * @since Tea Theme Options 1.3.2
     */
    public function buildCustomPostTypes()
    {
        //Register global action hook
        add_action('init', array(&$this, '__buildMenuCustomPostType'));

        //Register custom supports action hook
        /*if ($this->getIsAdmin())
        {
            add_action('save_post', array(&$this, '__save'));

            if (!empty($this->customs))
            {
                add_action('admin_init', array(&$this, '__customs'));
            }

            //Register icons action hook
            if (!empty($this->images))
            {
                add_action('admin_head', array(&$this, '__icons'));
            }

            //Register columns action hook
            if (!empty($this->columns))
            {
                add_action('manage_edit-' . $this->posttype . '_columns', array(&$this, '__columns'));
            }

            //Register dashboard action hook
            if ($this->dashboard)
            {
                add_action('wp_dashboard_setup', array(&$this, '__dashboard'));
            }
        }*/
    }

    /**
     * Register menus.
     *
     * @uses add_action()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function buildMenus()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Build Dashboard contents
        $this->buildDefaults();

        //Check if no master page is defined
        if (empty($this->pages))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition: no master page found. You can simply do that by using the addPage public function.', TTO_I18N);
            return false;
        }

        //Initialize the current index page
        $this->index = null;

        //Get all registered pages
        $pages = $this->getOption('tea_config_pages', array());

        //For all page WITH contents, add it to the page listing
        foreach ($pages as $key => $page)
        {
            //If the page contents are not defined, so continue to the next iteration
            if (!isset($page['contents']) || empty($page['contents']))
            {
                continue;
            }

            //Get page
            $titles = array(
                'title' => $page['title'],
                'name' => $page['name'],
                'slug' => $page['slug'],
                'submit' => $page['submit']
            );
            //Get contents
            $details = $page['contents'];

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);
        }

        //Register admin bar action hook
        add_action('wp_before_admin_bar_render', array(&$this, '__buildAdminBar'));

        //Register admin page action hook
        add_action('admin_menu', array(&$this, '__buildMenuPage'));

        //Register admin message action hook
        add_action('admin_notices', array(&$this, '__showAdminMessage'));

        //Register admin ajax action hook
        add_action('wp_ajax_' . TTO_ACTION, array(&$this, '__buildJSONOptions'));

        //Build Documentation and Network connection contents
        $this->buildDefaults(2);
    }

    /**
     * WORDPRESS USED HOOKS
     **/

    /**
     * Hook install plugin.
     *
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __adminInstall()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Nothing to do...
    }

    /**
     * Hook uninstall plugin.
     *
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __adminUninstall()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Delete configs
        _del_option('tea_config_pages');
        _del_option('tea_config_cpts');

        //Delete FlickR
        _del_option('tea_flickr_user_info');
        _del_option('tea_flickr_user_details');
        _del_option('tea_flickr_user_recent');
        _del_option('tea_flickr_connection_update');

        //Delete Instagram
        _del_option('tea_instagram_access_token');
        _del_option('tea_instagram_user_info');
        _del_option('tea_instagram_user_recent');
        _del_option('tea_instagram_connection_update');

        //Delete Twitter
        _del_option('tea_twitter_access_token');
        _del_option('tea_twitter_user_info');
        _del_option('tea_twitter_user_recent');
        _del_option('tea_twitter_connection_update');
    }

    /**
     * Hook building scripts.
     *
     * @uses wp_enqueue_media()
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __assetScripts()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get directory
        $directory = $this->getDirectory();

        //Enqueue usefull scripts
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('accordion');
        wp_enqueue_script('tea-modal', $directory . '/js/teamodal.js', array('jquery'));
        wp_enqueue_script('tea-to', $directory . '/js/teato.js', array('jquery', 'tea-modal'));
    }

    /**
     * Hook building styles.
     *
     * @uses wp_enqueue_style()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __assetStyles()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get directory
        $directory = $this->getDirectory();

        //Enqueue usefull styles
        wp_enqueue_style('media-views');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('tea-to', $directory . '/css/teato.css');
    }

    /**
     * Hook unload scripts.
     *
     * @uses wp_deregister_script()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __assetUnloaded()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //wp_deregister_script('media-models');
    }

    /**
     * Hook building admin bar.
     *
     * @uses add_menu()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __buildAdminBar()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Check if there is no problems on page definitions
        if (!isset($this->pages[$this->identifier]) || empty($this->pages))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition: no master page defined!', TTO_I18N);
            return false;
        }

        //Get the Wordpress globals
        global $wp_admin_bar;

        //Add submenu pages
        foreach ($this->pages as $page)
        {
            //Check the main page
            if ($this->identifier == $page['slug'])
            {
                //Build WP menu in admin bar
                $wp_admin_bar->add_menu(array(
                    'id' => $this->identifier,
                    'title' => $page['name'],
                    'href' => admin_url('admin.php?page=' . $this->identifier)
                ));
            }
            else
            {
                //Build the subpages
                $wp_admin_bar->add_menu(array(
                    'parent' => $this->identifier,
                    'id' => $this->getSlug($page['slug']),
                    'href' => admin_url('admin.php?page=' . $page['slug']),
                    'title' => $page['title'],
                    'meta' => false
                ));
            }
        }
    }

    /**
     * Get a content type in JSON format.
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __buildJSONOptions()
    {
        //Set headers
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: text/html');

        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            //Set code 500
            header('HTTP/1.1 403 Forbidden');
            echo __('You are NOT able to make this AJAX call.', TTO_I18N);
            die;
        }

        //Get request
        $request = $_REQUEST;

        //Check if the submitted nonce matches with the generated one
        if (!isset($request['nonce']) || !wp_verify_nonce($request['nonce'], TTO_NONCE))
        {
            //Set code 500
            header('HTTP/1.1 500 Internal Server Error');
            echo __('It seems your session is out-dated. Please, refresh your page and try again.', TTO_I18N);
            die;
        }

        //Get lists
        $types = $this->getFields();
        $type = $request['content'];

        //Check if the submitted content is unknown
        if (!isset($type) || !in_array($type, $types))
        {
            //Set code 500
            header('HTTP/1.1 500 Internal Server Error');
            echo __('The specified content type is unknown. Please, try again.', TTO_I18N);
            die;
        }

        //Set code 200
        header('HTTP/1.1 200 OK');

        //Set types in special case
        if(in_array($type, array('categories', 'menus', 'pages', 'posts', 'posttypes', 'tags', 'wordpress')))
        {
            $type = 'wordpress';
        }

        //Get wanted contents
        $class = 'Tea_Fields_' . ucfirst($type);
        require_once(TTO_PATH . 'classes/fields/' . $type . '/class-tea-fields-' . $type . '.php');

        //Make the magic
        $field = new $class();
        $field->templateDashboard();
        die;
    }

    /**
     * Hook building menus for CPTS.
     *
     * @uses register_post_type()
     *
     * @since Tea Theme Options 1.3.2.1
     */
    public function __buildMenuCustomPostType()
    {
        //Get all registered pages
        $cpts = $this->getOption('tea_config_cpts', array());

        //Check if we have some CPTS to initialize
        if (empty($cpts))
        {
            return false;
        }

        //Iterate on each cpt
        foreach ($cpts as $key => $cpt)
        {
            //Check if no master page is defined
            if (!isset($cpt['title']) || empty($cpt['title']))
            {
                $this->adminmessage = __('Something went wrong in your parameters definition: no title defined for you custom post type. Please, try again by filling the form properly.', TTO_I18N);
                return false;
            }

            //Special case: define a post as title to edit default post component
            if ('post' == strtolower($cpt['title']))
            {
                continue;
            }

            //Treat arrays
            $caps = isset($cpt['capability_type']) && !empty($cpt['capability_type']) ? array_keys($cpt['capability_type']) : 'post';
            $caps = is_array($caps) && 1 == count($caps) && 'post' == $caps[0] ? 'post' : $caps;
            $sups = isset($cpt['supports']) && !empty($cpt['supports']) ? array_keys($cpt['supports']) : array('title', 'editor', 'thumbnail');
            $taxs = isset($cpt['taxonomies']) && !empty($cpt['taxonomies']) ? array_keys($cpt['taxonomies']) : array('category', 'post_tag');

            //Build labels
            $labels = array(
                'name' => $cpt['title'],
                'singular_name' => isset($cpt['singular']) ? $cpt['singular'] : $cpt['title'],
                'menu_name' => isset($cpt['menu_name']) ? $cpt['menu_name'] : $cpt['title'],
                'all_items' => isset($cpt['all_items']) ? $cpt['all_items'] : $cpt['title'],
                'add_new' => isset($cpt['add_new']) ? $cpt['add_new'] : __('Add new', TTO_I18N),
                'add_new_item' => isset($cpt['add_new_item']) ? $cpt['add_new_item'] : sprintf(__('Add new %s', TTO_I18N), $cpt['title']),
                'edit' => isset($cpt['edit']) ? $cpt['edit'] : __('Edit', TTO_I18N),
                'edit_item' => isset($cpt['edit_item']) ? $cpt['edit_item'] : sprintf(__('Edit %s', TTO_I18N), $cpt['title']),
                'new_item' => isset($cpt['new_item']) ? $cpt['new_item'] : sprintf(__('New %s', TTO_I18N), $cpt['title']),
                'view' => isset($cpt['view']) ? $cpt['view'] : __('View', TTO_I18N),
                'view_item' => isset($cpt['view_item']) ? $cpt['view_item'] : sprintf(__('View %s', TTO_I18N), $cpt['title']),
                'search_items' => isset($cpt['search_items']) ? $cpt['search_items'] : sprintf(__('Search %s', TTO_I18N), $cpt['title']),
                'not_found' => isset($cpt['not_found']) ? $cpt['not_found'] : sprintf(__('No %s found', TTO_I18N), $cpt['title']),
                'not_found_in_trash' => isset($cpt['not_found_in_trash']) ? $cpt['not_found_in_trash'] : sprintf(__('No %s found in Trash', TTO_I18N), $cpt['title']),
                'parent_item_colon' => isset($cpt['parent_item_colon']) ? $cpt['parent_item_colon'] : sprintf(__('Parent %s', TTO_I18N), $cpt['title'])
            );

            //Build args
            $args = array(
                'labels' => $labels,
                'public' => isset($cpt['options']['public']) && $cpt['options']['public'] ? true : false,
                'show_ui' => isset($cpt['options']['show_ui']) && $cpt['options']['show_ui'] ? true : false,
                'show_in_menu' => isset($cpt['options']['show_ui']) && $cpt['options']['show_ui'] ? true : false,
                'capability_type' => $caps,
                'hierarchical' => isset($cpt['options']['hierarchical']) && $cpt['options']['hierarchical'] ? true : false,
                'rewrite' => isset($cpt['options']['rewrite']) && $cpt['options']['rewrite'] ? true : false,
                'supports' => $sups,
                'query_var' => isset($cpt['options']['query_var']) && $cpt['options']['query_var'] ? true : false,
                'permalink_epmask' => EP_PERMALINK,
                'taxonomies' => $taxs,
                'menu_icon' => isset($cpt['menu_icon_small']) ? $cpt['menu_icon_small'] : ''
            );

            //Action to register
            register_post_type(strtolower($cpt['title']), $args);
        }
    }

    /**
     * Hook building menus.
     *
     * @uses add_action()
     * @uses add_menu_page()
     * @uses add_submenu_page()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __buildMenuPage()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Check if no master page is defined
        if (empty($this->pages))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition: no master page found. You can simply do that by using the addPage public function.', TTO_I18N);
            return false;
        }

        //Set the current page
        $is_page = $this->identifier == $this->current ? true : false;

        //Get directory
        $directory = $this->getDirectory();

        //Set icon
        $this->icon_small = $directory . $this->icon_small;
        $this->icon_big = $directory . $this->icon_big;

        //Add submenu pages
        foreach ($this->pages as $page)
        {
            //Build slug and check it
            $is_page = $page['slug'] == $this->current ? true : $is_page;

            //Check the main page
            if ($this->identifier == $page['slug'])
            {
                //Add page
                add_menu_page(
                    $page['title'],                 //page title
                    $page['name'],                  //page name
                    $this->capability,              //capability
                    $this->identifier,              //parent slug
                    array(&$this, 'buildContent'),  //function to display content
                    $this->icon_small               //icon
                );
            }
            else
            {
                //Add subpage
                add_submenu_page(
                    $this->identifier,              //parent slug
                    $page['title'],                 //page title
                    $page['name'],                  //page name
                    $this->capability,              //capability
                    $page['slug'],                  //menu slug
                    array(&$this, 'buildContent')   //function to display content
                );
            }

            //Build breadcrumb
            $this->breadcrumb[] = array(
                'title' => $page['name'],
                'slug' => $page['slug']
            );
        }

        //Unload unwanted assets
        if (!empty($this->current) && $is_page)
        {
            add_action('admin_head', array(&$this, '__assetUnloaded'));
        }

        //Load assets action hook
        add_action('admin_print_scripts', array(&$this, '__assetScripts'));
        add_action('admin_print_styles', array(&$this, '__assetStyles'));
    }

    /**
     * Display a warning message on the admin panel.
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __cronSchedules()
    {
        //Require file
        require_once(TTO_PATH . 'classes/fields/network/class-tea-fields-network.php');

        //Make the magic
        $field = new Tea_Fields_Network();
        $field->updateNetworks();
    }

    /**
     * Display a warning message on the admin panel.
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __showAdminMessage()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        $content = $this->adminmessage;

        if (!empty($content))
        {
            //Get template
            include('tpl/layouts/__layout_admin_message.tpl.php');
        }
    }

    /**
     * BUILD METHODS
     **/

    /**
     * Add a page to the theme options panel.
     *
     * @param array $configs Array containing all configurations
     * @param array $contents Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function addPage($configs = array(), $contents = array())
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Check params and if a master page already exists
        if (empty($configs))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition: your configs are empty. See README.md for more explanations.', TTO_I18N);
            return false;
        }
        else if (empty($contents))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition: your contents are empty. See README.md for more explanations.', TTO_I18N);
            return false;
        }

        //Update capabilities
        $this->capability = 'manage_options';

        //Define the slug
        $slug = isset($configs['slug']) ? $this->getSlug($configs['slug']) : $this->getSlug();

        //Update the current page index
        $this->index = $slug;

        //Define page configurations
        $this->pages[$slug] = array(
            'title' => isset($configs['title']) ? $configs['title'] : 'Theme Options',
            'name' => isset($configs['name']) ? $configs['name'] : 'Tea Theme Options',
            'position' => isset($configs['position']) ? $configs['position'] : null,
            'description' => isset($configs['description']) ? $configs['description'] : '',
            'submit' => isset($configs['submit']) ? $configs['submit'] : true,
            'slug' => $slug,
            'contents' => $contents
        );
    }

    /**
     * Build connection content.
     *
     * @param array $contents Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function buildConnection($contents)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Default variables
        $page = empty($this->current) ? $this->identifier : $this->current;
        $includes = $this->getIncludes();

        //Include class field
        if (!isset($includes['network']))
        {
            //Set the include
            $this->setIncludes('network');

            //Require file
            require_once(TTO_PATH . 'classes/fields/network/class-tea-fields-network.php');
        }

        //Make the magic
        $field = new Tea_Fields_Network();
        $field->setCurrentPage($page);
        $field->templatePages($contents);
    }

    /**
     * Build content layout.
     *
     * @since Tea Theme Options 1.3.0
     */
    public function buildContent()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get current infos
        $current = empty($this->current) ? $this->identifier : $this->current;

        //Checks contents
        if (empty($this->pages[$current]['contents']))
        {
            $this->adminmessage = __('Something went wrong: it seems you forgot to attach contents to the current page. Use of addFields() function to make the magic.', TTO_I18N);
            return false;
        }

        //Build header
        $this->buildLayoutHeader();

        //Get globals
        $icon = $this->icon_big;

        //Get contents
        $title = $this->pages[$current]['title'];
        $contents = $this->pages[$current]['contents'];

        //Build contents relatively to the type (special case: Dashboard and Connection pages)
        if ($current == $this->identifier)
        {
            $contents = 1 == count($contents) ? $contents[0] : $contents;
            $this->buildDashboard($contents);
        }
        else if ($this->identifier . '_connections' == $current)
        {
            $contents = 1 == count($contents) ? $contents[0] : $contents;
            $this->buildConnection($contents);
        }
        else
        {
            $this->buildType($contents);
        }

        //Build footer
        $this->buildLayoutFooter();
    }

    /**
     * Build dashboard content.
     *
     * @param array $contents Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function buildDashboard($contents)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Default variables
        $page = empty($this->current) ? $this->identifier : $this->current;
        $includes = $this->getIncludes();

        //Include class field
        if (!isset($includes['dashboard']))
        {
            //Set the include
            $this->setIncludes('dashboard');

            //Require file
            require_once(TTO_PATH . 'classes/fields/dashboard/class-tea-fields-dashboard.php');
        }

        //Make the magic
        $field = new Tea_Fields_Dashboard();
        $field->setCurrentPage($page);
        $field->templatePages($contents);
    }

    /**
     * Build default contents
     *
     * @param number $step Define which default pages do we need
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function buildDefaults($step = 1)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get first usefull default pages to build admin menu
        if (1 == $step)
        {
            //Get dashboard page contents
            include('tpl/contents/__content_dashboard.tpl.php');

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);
        }
        //Get the next one at the end
        else
        {
            //Get network connections page contents
            include('tpl/contents/__content_connections.tpl.php');

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);

            //Get documentation page contents
            include('tpl/contents/__content_documentation.tpl.php');

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);
        }
    }

    /**
     * Build header layout.
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function buildLayoutHeader()
    {
        //Get all pages with link, icon and title
        $links = $this->breadcrumb;
        $icon = $this->icon_big;
        $page = empty($this->current) ? $this->identifier : $this->current;
        $title = empty($this->current) ? $this->pages[$this->identifier]['title'] : $this->pages[$this->current]['title'];
        $title = empty($title) ? __('Tea Theme Options', TTO_I18N) : $title;
        $description = empty($this->current) ? $this->pages[$this->identifier]['description'] : $this->pages[$this->current]['description'];
        $submit = empty($this->current) ? $this->pages[$this->identifier]['submit'] : $this->pages[$this->current]['submit'];

        //Include template
        include('tpl/layouts/__layout_header.tpl.php');
    }

    /**
     * Build footer layout.
     *
     * @since Tea Theme Options 1.2.0
     */
    protected function buildLayoutFooter()
    {
        //Get all pages with submit button
        $submit = empty($this->current) ? $this->pages[$this->identifier]['submit'] : $this->pages[$this->current]['submit'];
        $version = TTO_VERSION;

        //Include template
        include('tpl/layouts/__layout_footer.tpl.php');
    }

    /**
     * Build each type content.
     *
     * @param array $contents Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function buildType($contents)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get includes
        $includes = $this->getIncludes();

        //Get all default fields in the Tea T.O. package
        $defaults_fields = $this->getFields();

        //Iteration on all array
        foreach ($contents as $key => $content)
        {
            //Get type
            $type = $content['type'];

            //Check if the asked field is unknown
            if (!in_array($type, $defaults_fields))
            {
                $this->adminmessage = sprintf(__('Something went wrong in your parameters definition with the id <b>%s</b>: the defined type is unknown!', TTO_I18N), $content['id']);
                continue;
            }

            //Set types in special case
            if(in_array($type, array('categories', 'menus', 'pages', 'posts', 'posttypes', 'tags', 'wordpress')))
            {
                $type = 'wordpress';
            }

            //Set vars
            $class = 'Tea_Fields_' . ucfirst($type);
            $inc = TTO_PATH . 'classes/fields/' . $type . '/class-tea-fields-' . $type . '.php';
            $includes = $this->getIncludes();

            //Include class field
            if (!isset($includes[$type]))
            {
                //Set the include
                $this->setIncludes($type);

                //Check if the class file exists
                if (!file_exists($inc))
                {
                    $this->adminmessage = sprintf(__('Something went wrong in your parameters definition: the file <b>%s</b> does not exist!', TTO_I18N), $inc);
                    continue;
                }

                //Require file
                require_once($inc);
            }

            //Make the magic
            $field = new $class();
            $field->templatePages($content);
        }
    }

    /**
     * CONTENTS METHODS
     **/

    /**
     * Get Tea TO directory.
     *
     * @param string $type Type of the wanted directory
     * @return string $directory Path of the Tea TO directory
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function getDirectory($type = 'uri')
    {
        return $this->directory[$type];
    }

    /**
     * Set Tea TO directory.
     *
     * @param string $type Type of the wanted directory
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function setDirectory($type = 'uri')
    {
        $this->directory['uri'] = TTO_URI;
        $this->directory['normal'] = TTO_PATH;
    }

    /**
     * Get transient duration.
     *
     * @return number $duration Transient duration in secondes
     *
     * @since Tea Theme Options 1.2.3
     */
    protected function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set transient duration.
     *
     * @param number $duration Transient duration in secondes
     *
     * @since Tea Theme Options 1.2.3
     */
    protected function setDuration($duration = 86400)
    {
        $this->duration = $duration;
    }

    /**
     * Return default values.
     *
     * @param string $return Define what to return
     * @param array $wanted Usefull in social case to return only what the user wants
     * @return array $defaults All defaults data provided by the Tea TO
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function getFields()
    {
        $defaults = array(
            'br', 'heading', 'hr', 'list', 'p', 'checkbox',
            'hidden', 'radio', 'select', 'multiselect',
            'text', 'textarea', 'background', 'color', 'font',
            'include', 'rte', 'social', 'upload', 'wordpress'
        );

        //Return the array
        return $defaults;
    }

    /**
     * Get includes.
     *
     * @return array $includes Array of all included files
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Set includes.
     *
     * @param string $context Name of the included file's context
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function setIncludes($context)
    {
        $includes = $this->getIncludes();
        $this->includes[$context] = true;
    }

    /**
     * Get is_admin.
     *
     * @return bool $is_admin Define if we are in admin panel or not
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function getIsAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set is_admin.
     *
     * @param bool $is_admin Define if we are in admin panel or not
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function setIsAdmin()
    {
        $this->is_admin = is_admin() ? true : false;
    }

    /**
     * Return option's value from transient.
     *
     * @param string $key The name of the transient
     * @param var $default The default value if no one is found
     * @return var $value
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function getOption($key, $default)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Return value from DB
        return _get_option($key, $default);
    }

    /**
     * Register uniq option into transient.
     *
     * @uses get_cat_name()
     * @uses get_categories()
     * @uses get_category()
     * @uses get_category_feed_link()
     * @uses get_category_link()
     * @param string $key The name of the transient
     * @param var $value The default value if no one is found
     * @param array $dependancy The default value if no one is found
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function setOption($key, $value, $dependancy = array())
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Check the category
        if (empty($key))
        {
            $this->adminmessage = sprintf(__('Something went wrong. Key "%s" and/or its value is empty.', TTO_I18N), $key);
            return false;
        }

        //Check the key for special "NONE" value
        $value = 'NONE' == $value ? '' : $value;

        //Get duration
        $duration = $this->getDuration();

        //Set the option
        _set_option($key, $value, $duration);

        //Special usecase: category. We can also register information as title, slug, description and children
        if (false !== strpos($key, '__category'))
        {
            //Make the value as an array
            $value = !is_array($value) ? array($value) : $value;

            //All contents
            $details = array();

            //Iterate on categories
            foreach ($value as $c)
            {
                //Get all children
                $cats = get_categories(array('child_of' => $c, 'hide_empty' => 0));
                $children = array();

                //Iterate on children to get ID only
                foreach ($cats as $ca)
                {
                    //Idem
                    $children[$ca->cat_ID] = array(
                        'id' => $ca->cat_ID,
                        'name' => get_cat_name($ca->cat_ID),
                        'link' => get_category_link($ca->cat_ID),
                        'feed' => get_category_feed_link($ca->cat_ID),
                        'children' => array()
                    );
                }

                //Get all details
                $category = get_category($c);

                //Build details with extra options
                $details[$c] = array(
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'slug' => get_category_link($c),
                    'feed' => get_category_feed_link($c),
                    'children' => $children
                );
            }

            //Set the other parameters: width
            _set_option($key . '_details', $details, $duration);
        }

        //Special usecase: checkboxes. When it's not checked, no data is sent through the $_POST array
        else if (false !== strpos($key, '__checkbox') && !empty($dependancy))
        {
            //Get the key
            $previous = str_replace('__checkbox', '', $key);

            //Check if it exists (if not that means the user unchecked it) and set the option
            if (!isset($dependancy[$previous]))
            {
                _set_option($previous, $value, $duration);
            }
        }

        //Special usecase: image. We can also register information as width, height, mimetype from upload and image inputs
        else if (false !== strpos($key, '__upload'))
        {
            //Get the image details
            $image = getimagesize($value);

            //Build details
            $details = array(
                'width' => $image[0],
                'height' => $image[1],
                'mime' => $image['mime']
            );

            //Set the other parameters
            _set_option($key . '_details', $details, $duration);
        }
    }

    /**
     * Add a page or a custom post type to the theme options panel.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function updateContents($request)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Defaults variables
        $page = empty($this->current) ? $this->identifier : $this->current;
        $includes = $this->getIncludes();

        //Include class field
        if (!isset($includes['dashboard']))
        {
            //Set the include
            $this->setIncludes('dashboard');

            //Require file
            require_once(TTO_PATH . 'classes/fields/dashboard/class-tea-fields-dashboard.php');
        }

        //Make the magic
        $field = new Tea_Fields_Dashboard();
        $field->setCurrentPage($page);
        $field->actionDashboard($request);
    }

    /**
     * Register $_POST and $_FILES into transients.
     *
     * @uses wp_handle_upload()
     * @param array $request Contains all data in $_POST
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function updateNetworks($request)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Check if a network connection is asked
        if (!isset($request['tea_to_callback']) && !isset($request['tea_to_network']))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition. You need to specify a network to make the connection happens.', TTO_I18N);
            return false;
        }

        //Defaults variables
        $page = empty($this->current) ? $this->identifier : $this->current;
        $includes = $this->getIncludes();

        //Include class field
        if (!isset($includes['network']))
        {
            //Set the include
            $this->setIncludes('network');

            //Require file
            require_once(TTO_PATH . 'classes/fields/network/class-tea-fields-network.php');
        }

        //Make the magic
        $field = new Tea_Fields_Network();
        $field->setCurrentPage($page);
        $field->actionNetwork($request);
    }

    /**
     * Register $_POST and $_FILES into transients.
     *
     * @uses wp_handle_upload()
     * @param array $request Contains all data in $_REQUEST
     * @param array $files Contains all data in $_FILES
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function updateOptions($request, $files)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Set all options in transient
        foreach ($request as $k => $v)
        {
            //Don't register this default value
            if ('tea_to_settings' == $k || 'submit' == $k)
            {
                continue;
            }

            //Special usecase: checkboxes. When it's not checked, no data is sent through the $_POST array
            $p = false !== strpos($k, '__checkbox') ? $request : array();

            //Register option and transient
            $this->setOption($k, $v, $p);
        }

        //Check if files are attempting to be uploaded
        if (!empty($files))
        {
            //Get required files
            require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
            require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
            require_once(ABSPATH . 'wp-admin' . '/includes/media.php');

            //Set all URL in transient
            foreach ($files as $k => $v)
            {
                //Don't do nothing if no file is defined
                if (empty($v['tmp_name']))
                {
                    continue;
                }

                //Do the magic
                $file = wp_handle_upload($v, array('test_form' => false));

                //Register option and transient
                $this->setOption($k, $file['url']);
            }
        }
    }

    /**
     * Returns automatical slug.
     *
     * @param string $slug
     * @return string $identifier.$slug
     *
     * @since Tea Theme Options 1.2.3
     */
    protected function getSlug($slug = '')
    {
        return $this->identifier . $slug;
    }
}

//Instanciate a new Tea_Theme_Options
$tea = new Tea_Theme_Options();

/**
 * Set a value into options
 *
 * @since Tea Theme Options 1.2.9
 */
function _del_option($option, $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Delete the transient
        delete_transient($option);
    }

    //Delete value from DB
    delete_option($option);
}

/**
 * Return a value from options
 *
 * @since Tea Theme Options 1.2.1
 */
function _get_option($option, $default = '', $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Get value from transient
        $value = get_transient($option);

        if (false === $value)
        {
            //Get it from DB
            $value = get_option($option);

            //Put the default value if not
            $value = false === $value ? $default : $value;

            //Set the transient for this value
            set_transient($option, $value, TTO_DURATION);
        }
    }
    //Else...
    else
    {
        //Get value from DB
        $value = get_option($option);

        //Put the default value if not
        $value = false === $value ? $default : $value;
    }

    //Return value
    return $value;
}

/**
 * Set a value into options
 *
 * @since Tea Theme Options 1.2.1
 */
function _set_option($option, $value, $transient = false)
{
    //If a transient is asked...
    if ($transient)
    {
        //Set the transient for this value
        set_transient($option, $value, TTO_DURATION);
    }

    //Set value into DB
    update_option($option, $value);
}
