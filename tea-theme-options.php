<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Theme Options
 * @since Tea Theme Options 1.3.2.1
 *
 * Plugin Name: Tea Theme Options
 * Version: 1.3.2
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


//---------------------------------------------------------------------------------------------------------//

//Usefull definitions for the Tea Theme Options
defined('TTO_VERSION')      or define('TTO_VERSION', '1.3.2.1');
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
 * @since Tea Theme Options 1.3.2.1
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
     * @since Tea Theme Options 1.3.2
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
            //register_activation_hook(__FILE__, array(&$this, '__adminInstall'));
            register_deactivation_hook(__FILE__, array(&$this, '__adminUninstall'));

            //Check identifier
            if (empty($identifier))
            {
                $this->adminmessage = __('Something went wrong in your parameters definition. You need at least an identifier.', TTO_I18N);
                return false;
            }

            //Define parameters
            $this->can_upload = true; //current_user_can('upload_files')
            $this->identifier = $identifier;

            //Set default duration and directories
            $this->setDuration();
            $this->setDirectory();

            //Get current page
            $this->current = isset($_GET['page']) ? $_GET['page'] : '';

            //Add page or custom post type
            if (isset($_POST['tea_to_dashboard']))
            {
                $this->updateContents($_POST);
            }
            //...Or update options...
            else if (isset($_POST['tea_to_settings']))
            {
                $this->updateOptions($_POST, $_FILES);
            }
            //...Or update network data...
            else if (isset($_GET['tea_to_callback']))
            {
                $this->__networkCallback($_GET);
            }
            //...Or make some modifications to the asked network
            else if (isset($_POST['tea_to_network']))
            {
                $this->__networkDispatch($_POST);
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


    //--------------------------------------------------------------------------//

    /**
     * WORDPRESS HOOKS
     **/

    /**
     * Hook uninstall plugin.
     *
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.3.0
     */
    public function __adminUninstall()
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }
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
     * @since Tea Theme Options 1.3.0
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
        $bgdetails = $this->getDefaults('background-details');
        $bgimages = $this->getDefaults('images');
        $fonts = $this->getDefaults('fonts');
        $types = $this->getDefaults('typesraw');
        $choices = $this->getDefaults('typeschoices');
        $networks = $this->getDefaults('typesnetworks');
        $socials = $this->getDefaults('social');
        $texts = $this->getDefaults('text');
        $wordpress = $this->getDefaults('typeswordpress');

        //Get icons
        $urlsocial = $this->getDirectory() . 'img/social/icon-';

        //Check if the submitted content is unknown
        if (!isset($request['content']) || !in_array($request['content'], $types))
        {
            //Set code 500
            header('HTTP/1.1 500 Internal Server Error');
            echo __('The specified content type is unknown. Please, try again.', TTO_I18N);
            die;
        }

        //Set code 200
        header('HTTP/1.1 200 OK');

        //Get wanted contents
        include('tpl/options/__opt_' . $request['content'] . '.tpl.php');
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
     * @since Tea Theme Options 1.3.0
     */
    public function __cronSchedules()
    {
        //Get networks values from DB
        $flickr = $this->getOption('tea_flickr_user_info', '');
        $instagram = $this->getOption('tea_instagram_access_token', '');
        $twitter = $this->getOption('tea_twitter_access_token', '');

        //Check FlickR
        if (false !== $flickr && !empty($flickr))
        {
            $this->updateNetworks(array('tea_to_network' => 'flickr'));
        }

        //Check Instagram
        if (false !== $instagram && !empty($instagram))
        {
            $this->updateNetworks(array('tea_to_network' => 'instagram'));
        }

        //Check Twitter
        if (false !== $twitter && !empty($twitter))
        {
            $this->updateNetworks(array('tea_to_network' => 'twitter'));
        }
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


    //--------------------------------------------------------------------------//

    /**
     * BUILD METHODS
     **/

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

        //Build contents relatively to the type
        $this->buildType($contents);

        //Build footer
        $this->buildLayoutFooter();
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
            include('tpl/layouts/__layout_dashboard.tpl.php');

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);
        }
        //Get the next one at the end
        else
        {
            //Get network connections page contents
            include('tpl/layouts/__layout_connections.tpl.php');

            //Build page with contents
            $this->addPage($titles, $details);
            unset($titles, $details);

            //Get documentation page contents
            include('tpl/layouts/__layout_documentation.tpl.php');

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
     * @param bool $group Define if we are in group display or not
     *
     * @since Tea Theme Options 1.3.2
     */
    protected function buildType($contents)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Get all fields without ID
        $do_not_have_ids = $this->getDefaults('withoutids');

        //Iteration on all array
        foreach ($contents as $key => $content)
        {
            //Check if an id is defined at least
            if (!isset($content['id']) && !in_array($content['type'], $do_not_have_ids))
            {
                $this->adminmessage = sprintf(__('Something went wrong in your parameters definition: no id is defined for your <b>%s</b> field!', TTO_I18N), $content['type']);
                //$this->__showAdminMessage();
                continue;
            }

            //Get the right template

            //Dashboard input
            if ('dashboard' == $content['type'])
            {
                $this->__fieldDashboard($content);
            }

            //Display inputs
            else if ('br' == $content['type'])
            {
                $this->__fieldBr();
            }
            else if ('features' == $content['type'])
            {
               $this->__fieldFeatures($content);
            }
            else if ('heading' == $content['type'])
            {
                $this->__fieldHeading($content);
            }
            else if('hr' == $content['type'])
            {
                $this->__fieldHr();
            }
            else if('list' == $content['type'])
            {
                $this->__fieldList($content);
            }
            else if('p' == $content['type'])
            {
                $this->__fieldP($content);
            }

            //Normal inputs
            else if(in_array($content['type'], array('checkbox', 'radio', 'select', 'multiselect')))
            {
                $this->__fieldChoice($content['type'], $content);
            }
            else if('hidden' == $content['type'])
            {
                $this->__fieldHidden($content);
            }
            else if('text' == $content['type'])
            {
                $this->__fieldText($content);
            }
            else if('textarea' == $content['type'])
            {
                $this->__fieldTextarea($content);
            }

            //Special inputs
            else if('background' == $content['type'])
            {
                $this->__fieldBackground($content);
            }
            else if('color' == $content['type'])
            {
                $this->__fieldColor($content);
            }
            else if('font' == $content['type'])
            {
                $this->__fieldFont($content);
            }
            else if('include' == $content['type'])
            {
                $this->__fieldInclude($content);
            }
            else if('rte' == $content['type'])
            {
                $this->__fieldRTE($content);
            }
            else if('social' == $content['type'])
            {
                $this->__fieldSocial($content);
            }
            else if('upload' == $content['type'])
            {
                $this->__fieldUpload($content);
            }

            //Wordpress inputs
            else if(in_array($content['type'], array('categories', 'menus', 'pages', 'posts', 'posttypes', 'tags', 'wordpress')))
            {
                $this->__fieldWordpressContents($content);
            }

            //Specials
            else if ('flickr' == $content['type'])
            {
                $this->__fieldFlickr($content);
            }
            else if ('instagram' == $content['type'])
            {
                $this->__fieldInstagram($content);
            }
            else if ('twitter' == $content['type'])
            {
                $this->__fieldTwitter($content);
            }

            //Default action
            else
            {
                $this->adminmessage = sprintf(__('Something went wrong in your parameters definition with the id <b>%s</b>: the defined type is unknown!', TTO_I18N), $content['id']);
                //$this->__showAdminMessage();
            }
        }
    }


    //--------------------------------------------------------------------------//

    /**
     * CONTENTS METHODS
     **/


    //-------------------------------------//

    /**
     * Build dashboard component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldDashboard($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Dashboard', TTO_I18N);
        $page = empty($this->current) ? $this->identifier : $this->current;

        //Get pages and contents
        $pages = $this->getOption('tea_config_pages', array());
        $cpts = $this->getOption('tea_config_cpts', array());

        //Get lists
        $bgdetails = $this->getDefaults('background-details');
        $bgimages = $this->getDefaults('images');
        $fonts = $this->getDefaults('fonts');
        $typesgood = $this->getDefaults('types');
        $types = $this->getDefaults('typesraw');
        $choices = $this->getDefaults('typeschoices');
        $networks = $this->getDefaults('typesnetworks');
        $socials = $this->getDefaults('social');
        $texts = $this->getDefaults('text');
        $wordpress = $this->getDefaults('typeswordpress');

        //Get icons
        $urlsocial = $this->getDirectory() . '/img/social/icon-';

        //Define ajax vars
        $action = TTO_ACTION;
        $nonce = esc_js(wp_create_nonce(TTO_NONCE));
        $ajax = admin_url() . 'admin-ajax.php';

        //Count pages and default pages
        $count_page = count($pages);
        $count_cpt = count($cpts);

        //Get template
        include('tpl/fields/__field_dashboard.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build br component.
     *
     * @since Tea Theme Options 1.1.0
     */
    protected function __fieldBr()
    {
        //Get template
        include('tpl/fields/__field_br.tpl.php');
    }

    /**
     * Build features component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.2.2
     */
    protected function __fieldFeatures($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Features', TTO_I18N);
        $contents = isset($content['contents']) ? $content['contents'] : array();

        //Get template
        include('tpl/fields/__field_features.tpl.php');
    }

    /**
     * Build heading component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.0.1
     */
    protected function __fieldHeading($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Heading', TTO_I18N);

        //Get template
        include('tpl/fields/__field_heading.tpl.php');
    }

    /**
     * Build hr component.
     *
     * @since Tea Theme Options 1.1.0
     */
    protected function __fieldHr()
    {
        //Get template
        include('tpl/fields/__field_hr.tpl.php');
    }

    /**
     * Build list component (ul > li).
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.2.2
     */
    protected function __fieldList($content)
    {
        //Default variables
        $li = isset($content['contents']) ? $content['contents'] : array();

        //Get template
        include('tpl/fields/__field_list.tpl.php');
    }

    /**
     * Build p component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.2.1
     */
    protected function __fieldP($content)
    {
        //Default variables
        $content = isset($content['content']) ? $content['content'] : '';

        //Get template
        include('tpl/fields/__field_p.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build choice component.
     *
     * @param string $type Contains the type's field
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function __fieldChoice($type, $content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Choice', TTO_I18N);
        $options = isset($content['options']) ? $content['options'] : array();
        $description = isset($content['description']) ? $content['description'] : '';

        //Expand & multiple variables
        $multiple = isset($content['multiple']) ? $content['multiple'] : false;
        $type = 'select' == $type && $multiple ? 'multiselect' : $type;

        //Check types
        if ('checkbox' == $type || 'multiselect' == $type)
        {
            //Define default value
            $std = isset($content['std']) ? $content['std'] : array();

            //Check selected
            $vals = $this->getOption($id, $std);
            $vals = empty($vals) ? array(0) : (is_array($vals) ? $vals : array($vals));
        }
        else
        {
            //Define default value
            $std = isset($content['std']) ? $content['std'] : '';

            //Check selected
            $val = $this->getOption($id, $std);
        }

        //Get template
        include('tpl/fields/__field_' . $type . '.tpl.php');
    }

    /**
     * Build hidden component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldHidden($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : '';

        //Check selected
        $val = $this->getOption($id, $title);

        //Get template
        include('tpl/fields/__field_hidden.tpl.php');
    }

    /**
     * Build text component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function __fieldText($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Text', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $placeholder = isset($content['placeholder']) ? 'placeholder="' . $content['placeholder'] . '"' : '';
        $maxlength = isset($content['maxlength']) ? 'maxlength="' . $content['maxlength'] . '"' : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $options = isset($content['options']) ? $content['options'] : array();

        //Special variables
        $min = $max = $step = '';
        $options['type'] = !isset($options['type']) || empty($options['type']) ? 'text' : $options['type'];

        //Check options
        if ('number' == $options['type'] || 'range' == $options['type'])
        {
            //Infos
            $type = $options['type'];
            //Special variables
            $min = isset($options['min']) ? 'min="' . $options['min'] . '"' : 'min="1"';
            $max = isset($options['max']) ? 'max="' . $options['max'] . '"' : 'max="50"';
            $step = isset($options['step']) ? 'step="' . $options['step'] . '"' : 'step="1"';
        }
        else
        {
            //Infos
            $type = $options['type'];
        }

        //Check selected
        $val = $this->getOption($id, $std);
        $val = stripslashes($val);

        //Get template
        include('tpl/fields/__field_text.tpl.php');
    }

    /**
     * Build textarea component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.1.1
     */
    protected function __fieldTextarea($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Textarea', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $placeholder = isset($content['placeholder']) ? 'placeholder="' . $content['placeholder'] . '"' : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $rows = isset($content['rows']) ? $content['rows'] : '8';

        //Check selected
        $val = $this->getOption($id, $std);
        $val = stripslashes($val);

        //Get template
        include('tpl/fields/__field_textarea.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build background component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldBackground($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Background', TTO_I18N);
        $height = isset($content['height']) ? $content['height'] : '60';
        $width = isset($content['width']) ? $content['width'] : '150';
        $description = isset($content['description']) ? $content['description'] : '';
        $defaults = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;
        $can_upload = $this->can_upload;
        $delete = __('Delete selection', TTO_I18N);

        //Default values
        $std = isset($content['std']) ? $content['std'] : array(
            'image' => '',
            'image_custom' => '',
            'color' => '',
            'position' => array(
                'x' => 'left',
                'y' => 'top'
            ),
            'repeat' => 'repeat'
        );

        //Get options
        $options = isset($content['options']) ? $content['options'] : array();

        if ($defaults)
        {
            $defaults = $this->getDefaults('images');
            $options = array_merge($defaults, $options);
        }

        //Positions
        $details = $this->getDefaults('background-details');

        //Get value
        $val = $this->getOption($id, $std);

        //Get template
        include('tpl/fields/__field_background.tpl.php');
    }

    /**
     * Build color component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.0
     */
    protected function __fieldColor($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Color', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';

        //Check selected
        $val = $this->getOption($id, $std);

        //Get template
        include('tpl/fields/__field_color.tpl.php');
    }

    /**
     * Build font component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function __fieldFont($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Font', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $defaults = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;

        //Get options
        $options = isset($content['options']) ? $content['options'] : array();

        if ($defaults)
        {
            $defaults = $this->getDefaults('fonts');
            $options = array_merge($defaults, $options);
        }

        //Get includes
        $includes = $this->getIncludes();
        $style = true;
        $linkstylesheet = '';
        $gfontstyle = '';

        //Check if Google Font has already been included
        if (!isset($includes['googlefonts']))
        {
            $style = false;
            $this->setIncludes('googlefonts');

            //Define our stylesheets
            foreach ($options as $option)
            {
                if (empty($option[0]) || 'sansserif' == $option[0])
                {
                    continue;
                }

                $linkstylesheet .= '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' . $option[0] . ':' . $option[2] . '" />' . "\n";
                $gfontstyle .= '.gfont_' . str_replace(' ', '_', $option[1]) . ' {font-family:\'' . $option[1] . '\',sans-serif;}' . "\n";
            }
        }

        //Radio selected
        $val = $this->getOption($id, $std);

        //Get template
        include('tpl/fields/__field_font.tpl.php');
    }

    /**
     * Build include component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function __fieldInclude($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Include', TTO_I18N);
        $file = isset($content['file']) ? $content['file'] : false;

        //Get template
        include('tpl/fields/__field_include.tpl.php');
    }

    /**
     * Build RTE component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.8
     */
    protected function __fieldRTE($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea RTE', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';

        //Check selected
        $val = $this->getOption($id, $std);
        $val = stripslashes($val);

        //Get template
        include('tpl/fields/__field_rte.tpl.php');
    }

    /**
     * Build social component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.6
     */
    protected function __fieldSocial($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Social', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : array();
        $description = isset($content['description']) ? $content['description'] : '';
        $url = $this->getDirectory() . 'img/social/icon-';

        //Get options
        $default = isset($content['default']) ? $content['default'] : array();
        $options = $this->getDefaults('social', $default);

        //Get values
        $val = $this->getOption($id, $std);

        //Get template
        include('tpl/fields/__field_social.tpl.php');
    }

    /**
     * Build upload component.
     *
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.0
     */
    protected function __fieldUpload($content)
    {
        //Default variables
        $id = $content['id'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Upload', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $library = isset($content['library']) ? $content['library'] : 'image';
        $description = isset($content['description']) ? $content['description'] : '';
        $multiple = isset($content['multiple']) && true == $content['multiple'] ? '1' : '0';
        $can_upload = $this->can_upload;
        $delete = __('Delete selection', TTO_I18N);

        //Check selected
        $val = $this->getOption($id, $std);

        //Get template
        include('tpl/fields/__field_upload.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build wordpress contents component.
     *
     * @uses get_categories()
     * @uses get_pages()
     * @uses get_post_types()
     * @uses get_the_tags()
     * @uses wp_get_nav_menus()
     * @uses wp_get_recent_posts()
     * @param array $content Contains all data
     * @param bool $group Define if the field is displayed in group or not
     *
     * @since Tea Theme Options 1.2.7
     */
    protected function __fieldWordpressContents($content)
    {
        //Default variables
        $id = $content['id'];
        $type = 'wordpress' == $content['type'] ? $content['mode'] : $content['type'];
        $title = isset($content['title']) ? $content['title'] : __('Tea Wordpress Contents', TTO_I18N);
        $multiple = isset($content['multiple']) ? $content['multiple'] : false;
        $description = isset($content['description']) ? $content['description'] : '';

        //Access the WordPress Categories via an Array
        if (empty($this->wp_contents) || !isset($this->wp_contents[$type]))
        {
            $this->wp_contents[$type] = array();

            //Set the first item
            if (!$multiple)
            {
                $this->wp_contents[$type][-1] = '---';
            }

            //Get asked contents

            //Menus
            if ('menus' == $type)
            {
                //Build request
                $menus_obj = wp_get_nav_menus(array('hide_empty' => false, 'orderby' => 'none'));

                //Iterate on menus
                foreach ($menus_obj as $menu)
                {
                    //For Wordpress version < 3.0
                    if (empty($menu->term_id))
                    {
                        continue;
                    }

                    //Get the id and the name
                    $this->wp_contents[$type][$menu->term_id] = $menu->name;
                }
            }
            //Pages
            else if ('pages' == $type)
            {
                //Build request
                $pages_obj = get_pages(array('sort_column' => 'post_parent,menu_order'));

                //Iterate on pages
                foreach ($pages_obj as $pag)
                {
                    //For Wordpress version < 3.0
                    if (empty($pag->ID))
                    {
                        continue;
                    }

                    //Get the id and the name
                    $this->wp_contents[$type][$pag->ID] = $pag->post_title;
                }
            }
            //Posts
            else if ('posts' == $type)
            {
                //Get vars
                $post = !isset($content['posttype']) ? 'post' : (is_array($content['posttype']) ? implode(',', $content['posttype']) : $content['posttype']);
                $number = isset($content['number']) ? $content['number'] : 50;

                //Build request
                $posts_obj = wp_get_recent_posts(array('numberposts' => $number, 'post_type' => $post, 'post_status' => 'publish'), OBJECT);

                //Iterate on posts
                foreach ($posts_obj as $pos)
                {
                    //For Wordpress version < 3.0
                    if (empty($pos->ID))
                    {
                        continue;
                    }

                    //Get the id and the name
                    $this->wp_contents[$type][$pos->ID] = $pos->post_title;
                }
            }
            //Post types
            else if ('posttypes' == $type)
            {
                //Build request
                $types_obj = get_post_types(array(), 'object');

                //Iterate on posttypes
                foreach ($types_obj as $typ)
                {
                    //Get the the name
                    $this->wp_contents[$type][$typ->name] = $typ->labels->name;
                }
            }
            //Tags
            else if ('tags' == $type)
            {
                //Build request
                $tags_obj = get_the_tags();

                //Iterate on tags
                foreach ($tags_obj as $tag)
                {
                    //Get the id and the name
                    $this->wp_contents[$type][$tag->term_id] = $tag->name;
                }
            }
            //Categories
            else
            {
                //Build request
                $categories_obj = get_categories(array('hide_empty' => 0));

                //Iterate on categories
                foreach ($categories_obj as $cat)
                {
                    //For Wordpress version < 3.0
                    if (empty($cat->cat_ID))
                    {
                        continue;
                    }

                    //Get the id and the name
                    $this->wp_contents[$type][$cat->cat_ID] = $cat->cat_name;
                }
            }
        }

        //Set the categories
        $contents = $this->wp_contents[$type];

        //Check selected
        $vals = $this->getOption($id, array());
        $vals = empty($vals) ? array(0) : (is_array($vals) ? $vals : array($vals));

        //Get template
        include('tpl/fields/__field_wordpress.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build FlickR component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldFlickr($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea FlickR', TTO_I18N);
        $description = isset($content['description']) ? $content['description'] : '';
        $icon = $this->getDirectory() . '/img/social/icon-flickr.png';
        $page = empty($this->current) ? $this->identifier : $this->current;
        $update = $this->getOption('tea_flickr_connection_update', '');
        $display_form = false;

        //Check if we display form or user informations
        $user_info = $this->getOption('tea_flickr_user_info', array());

        if (false === $user_info || empty($user_info))
        {
            //Default vars
            $display_form = true;
        }
        else
        {
            //Get user Flickr info from DB
            $user_details = $this->getOption('tea_flickr_user_details', array());
            $user_details = false === $user_details ? array() : $user_details;

            //Get recent photos from DB
            $user_recent = $this->getOption('tea_flickr_user_recent', array());
            $user_recent = false === $user_recent ? array() : $user_recent;

            //Display date of update
            $update = false === $update || empty($update) ? '' : $update;
        }

        //Get template
        include('tpl/fields/__field_network_flickr.tpl.php');
    }

    /**
     * Build Instagram component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldInstagram($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Instagram', TTO_I18N);
        $description = isset($content['description']) ? $content['description'] : '';
        $icon = $this->getDirectory() . '/img/social/icon-instagram.png';
        $page = empty($this->current) ? $this->identifier : $this->current;
        $update = $this->getOption('tea_instagram_connection_update', '');
        $display_form = false;

        //Check if we display form or user informations
        $token = $this->getOption('tea_instagram_access_token', '');

        if (false === $token || empty($token))
        {
            //Default vars
            $display_form = true;
        }
        else
        {
            //Get user Instagram info from DB
            $user_info = $this->getOption('tea_instagram_user_info', array());
            $user_info = false === $user_info ? array() : $user_info;

            //Get recent photos from DB
            $user_recent = $this->getOption('tea_instagram_user_recent', array());
            $user_recent = false === $user_recent ? array() : $user_recent;

            //Display date of update
            $update = false === $update || empty($update) ? '' : $update;
        }

        //Get template
        include('tpl/fields/__field_network_instagram.tpl.php');
    }

    /**
     * Build Instagram component.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __fieldTwitter($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Twitter', TTO_I18N);
        $description = isset($content['description']) ? $content['description'] : '';
        $icon = $this->getDirectory() . '/img/social/icon-twitter.png';
        $page = empty($this->current) ? $this->identifier : $this->current;
        $update = $this->getOption('tea_twitter_connection_update', '');
        $display_form = false;

        //Check if we display form or user informations
        $token = $this->getOption('tea_twitter_access_token', '');

        if (false === $token || empty($token))
        {
            //Default vars
            $display_form = true;
        }
        else
        {
            //Get user Instagram info from DB
            $user_info = $this->getOption('tea_twitter_user_info', array());
            $user_info = false === $user_info ? array() : $user_info;

            //Get recent photos from DB
            $user_recent = $this->getOption('tea_twitter_user_recent', array());
            $user_recent = false === $user_recent ? array() : $user_recent;

            //Display date of update
            $update = false === $update || empty($update) ? '' : $update;
        }

        //Get template
        include('tpl/fields/__field_network_twitter.tpl.php');
    }


    //-------------------------------------//

    /**
     * Build dispatch method.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.3.2
     */
    protected function __networkDispatch($request)
    {
        //Check if a network connection is asked
        if (!isset($request['tea_to_network']))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition. You need to specify a network to make the connection happens.', TTO_I18N);
            return false;
        }

        //...Or update connection network
        if (isset($request['tea_to_connection']))
        {
            $this->__networkConnection($request);
        }
        //...Or update disconnection network
        else if (isset($request['tea_to_disconnection']))
        {
            $this->__networkDisconnection($request);
        }
        //...Or update data from network
        else if (isset($request['tea_to_update']))
        {
            $this->updateNetworks($request);
        }
    }

    /**
     * Build data from the asked network.
     *
     * @uses add_query_arg()
     * @uses admin_url()
     * @uses header()
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.3.2
     */
    protected function __networkCallback($request)
    {
        //Check if a network connection is asked
        if (!isset($request['tea_to_callback']))
        {
            $this->adminmessage = __('Something went wrong in your parameters definition. You need to specify a callback network to update the informations.', TTO_I18N);
            return false;
        }

        //Default vars
        $page = empty($this->current) ? $this->identifier : $this->current;

        //Check Instagram
        if (isset($request['instagram_token']))
        {
            //Update DB with the token
            $token = $request['instagram_token'];
            _set_option('tea_instagram_access_token', $token);

            //Get all data
            $request['tea_to_network'] = 'instagram';
            $this->updateNetworks($request);
        }
        //Check Twitter
        else if (isset($request['twitter_token']))
        {
            //Update DB with the token
            $token = array(
                'oauth_token' => $request['twitter_token'],
                'oauth_token_secret' => $request['twitter_secret']
            );
            _set_option('tea_twitter_access_token', $token);

            //Get all data
            $request['tea_to_network'] = 'twitter';
            $this->updateNetworks($request);
        }

        //Build callback
        $return = add_query_arg(array('page' => $page), admin_url('/admin.php'));

        //Redirect
        header('Location: ' . $return, false, 307);
        exit;
    }

    /**
     * Build connection to the asked network.
     *
     * @uses add_query_arg()
     * @uses admin_url()
     * @uses header()
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.3.2
     */
    protected function __networkConnection($request)
    {
        //Default vars
        $page = empty($this->current) ? $this->identifier : $this->current;

        //Check Instagram
        if ('instagram' == $request['tea_to_network'])
        {
            //Build callback
            $return = add_query_arg(array('page' => $page), admin_url('/admin.php'));
            $uri = add_query_arg('return_uri', urlencode($return), TTO_INSTAGRAM);

            //Redirect to network
            header('Location: ' . $uri, false, 307);
            exit;
        }
        //Check Flickr
        else if ('flickr' == $request['tea_to_network'])
        {
            $request['tea_flickr_install'] = true;
            $this->updateNetworks($request);
        }
        //Check Twitter
        else if ('twitter' == $request['tea_to_network'])
        {
            //Build callback
            $return = add_query_arg(array('page' => $page), admin_url('/admin.php'));
            $uri = add_query_arg('return_uri', urlencode($return), TTO_TWITTER);

            //Redirect to network
            header('Location: ' . $uri, false, 307);
            exit;
        }
    }

    /**
     * Build disconnection to the asked network.
     *
     * @uses add_query_arg()
     * @uses admin_url()
     * @uses header()
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function __networkDisconnection($request)
    {
        //Default vars
        $page = empty($this->current) ? $this->identifier : $this->current;

        //Check Instagram
        if ('instagram' == $request['tea_to_network'])
        {
            //Delete all data from DB
            _del_option('tea_instagram_access_token');
            _del_option('tea_instagram_user_info');
            _del_option('tea_instagram_user_recent');
            _del_option('tea_instagram_connection_update');

            //Build callback
            $return = add_query_arg(array('page' => $page), admin_url('/admin.php'));
            $uri = add_query_arg(array('return_uri' => urlencode($return), 'logout' => 'true'), TTO_INSTAGRAM);

            //Redirect to network
            header('Location: ' . $uri, false, 307);
            exit;
        }
        //Check Flickr
        else if ('flickr' == $request['tea_to_network'])
        {
            //Delete all data from DB
            _del_option('tea_flickr_user_info');
            _del_option('tea_flickr_user_details');
            _del_option('tea_flickr_user_recent');
            _del_option('tea_flickr_connection_update');

        }
        //Check Twitter
        else if ('twitter' == $request['tea_to_network'])
        {
            //Delete all data from DB
            _del_option('tea_twitter_access_token');
            _del_option('tea_twitter_user_info');
            _del_option('tea_twitter_user_recent');
            _del_option('tea_twitter_connection_update');

            //Build callback
            $return = add_query_arg(array('page' => $page), admin_url('/admin.php'));
            $uri = add_query_arg(array('return_uri' => urlencode($return), 'logout' => 'true'), TTO_TWITTER);

            //Redirect to network
            header('Location: ' . $uri, false, 307);
            exit;
        }
    }


    //--------------------------------------------------------------------------//

    /**
     * Return default values.
     *
     * @param string $return Define what to return
     * @param array $wanted Usefull in social case to return only what the user wants
     * @return array $defaults All defaults data provided by the Tea TO
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function getDefaults($return = 'images', $wanted = array())
    {
        $defaults = array();
        $directory = $this->getDirectory();

        //Return defaults background values
        if ('background-details' == $return)
        {
            $defaults = array(
                'position'  => array(
                    'x'     => array(
                        'left'      => __('Left', TTO_I18N),
                        'center'    => __('Center', TTO_I18N),
                        'right'     => __('Right', TTO_I18N)
                    ),
                    'y'     => array(
                        'top'       => __('Top', TTO_I18N),
                        'middle'    => __('Middle', TTO_I18N),
                        'bottom'    => __('Bottom', TTO_I18N)
                    )
                ),
                'repeat'    => array(
                    'no-repeat'     => __('Background is displayed only once.', TTO_I18N),
                    'repeat-x'      => __('Background is repeated horizontally only.', TTO_I18N),
                    'repeat-y'      => __('Background is repeated vertically only.', TTO_I18N),
                    'repeat'        => __('Background is repeated.', TTO_I18N)
                )
            );
        }
        //Return defauls FLickr API keys
        else if ('flickr' == $return)
        {
            $defaults = array(
                'api_key'       => '202431176865b4c5f725087d26bd78af',
                'api_secret'    => '2efaf89685c295ea'
            );
        }
        //Return defaults font
        else if ('fonts' == $return)
        {
            $defaults = array(
                array('sansserif', 'Sans serif', ''),
                array('Arvo', 'Arvo', '400,700'),
                array('Bree+Serif', 'Bree Serif', '400'),
                array('Cabin', 'Cabin', '400,500,600,700'),
                array('Cantarell', 'Cantarell', '400,700'),
                array('Copse', 'Copse', '400'),
                array('Cuprum', 'Cuprum', '400,700'),
                array('Droid+Sans', 'Droid Sans', '400,700'),
                array('Lobster+Two', 'Lobster Two', '400,700'),
                array('Open+Sans', 'Open Sans', '300,400,600,700,800'),
                array('Oswald', 'Oswald', '300,400,700'),
                array('Pacifico', 'Pacifico', '400'),
                array('Patua+One', 'Patua One', '400'),
                array('PT+Sans', 'PT Sans', '400,700'),
                array('Puritan', 'Puritan', '400,700'),
                array('Qwigley', 'Qwigley', '400'),
                array('Titillium+Web', 'Titillium Web', '200,300,400,600,700,900'),
                array('Vollkorn', 'Vollkorn', '400,700'),
                array('Yanone+Kaffeesatz', 'Yanone Kaffeesatz', '200,300,400,700')
            );
        }
        //Return defauls background images
        else if ('images' == $return)
        {
            $url = $directory . 'img/patterns/';

            $defaults = array(
                $url . 'none.png'           => __('No background', TTO_I18N),
                $url . 'bright_squares.png' => __('Bright squares', TTO_I18N),
                $url . 'circles.png'        => __('Circles', TTO_I18N),
                $url . 'crosses.png'        => __('Crosses', TTO_I18N),
                $url . 'crosslines.png'     => __('Crosslines', TTO_I18N),
                $url . 'cubes.png'          => __('Cubes', TTO_I18N),
                $url . 'double_lined.png'   => __('Double lined', TTO_I18N),
                $url . 'honeycomb.png'      => __('Honeycomb', TTO_I18N),
                $url . 'linen.png'          => __('Linen', TTO_I18N),
                $url . 'project_paper.png'  => __('Project paper', TTO_I18N),
                $url . 'texture.png'        => __('Tetxure', TTO_I18N),
                $url . 'vertical_lines.png' => __('Vertical lines', TTO_I18N),
                $url . 'vichy.png'          => __('Vichy', TTO_I18N),
                $url . 'wavecut.png'        => __('Wavecut', TTO_I18N),
                $url . 'custom.png'         => 'CUSTOM'
            );
        }
        //Return defaults social button
        else if ('social' == $return)
        {
            $socials = array(
                'addthis'       => array(),
                'bloglovin'     => array(__('Follow me on Bloglovin', TTO_I18N), __('http://www.bloglovin.com/blog/__userid__/__username__', TTO_I18N)),
                'deviantart'    => array(__('Follow me on Deviantart', TTO_I18N), __('http://__username__.deviantart.com/', TTO_I18N)),
                'dribbble'      => array(__('Follow me on Dribbble', TTO_I18N), __('http://dribbble.com/__username__', TTO_I18N)),
                'facebook'      => array(__('Follow me on Facebook', TTO_I18N), __('http://www.facebook.com/__username__', TTO_I18N)),
                'flickr'        => array(__('Follow me on Flickr', TTO_I18N), __('http://www.flickr.com/photos/__username__', TTO_I18N)),
                'forrst'        => array(__('Follow me on Forrst', TTO_I18N), __('http://forrst.com/people/__username__', TTO_I18N)),
                'friendfeed'    => array(__('Follow me on FriendFeed', TTO_I18N), __('http://friendfeed.com/__username__', TTO_I18N)),
                'hellocoton'    => array(__('Follow me on Hellocoton', TTO_I18N), __('http://www.hellocoton.fr/mapage/__username__', TTO_I18N)),
                'googleplus'    => array(__('Follow me on Google+', TTO_I18N), __('http://plus.google.com/__username__', TTO_I18N)),
                'instagram'     => array(__('Follow me on Instagram', TTO_I18N), __('http://www.instagram.com/__username__', TTO_I18N)),
                'lastfm'        => array(__('Follow me on LastFM', TTO_I18N), __('http://www.lastfm.fr/user/__username__', TTO_I18N)),
                'linkedin'      => array(__('Follow me on LinkedIn', TTO_I18N), __('http://fr.linkedin.com/in/__username__', TTO_I18N)),
                'pinterest'     => array(__('Follow me on Pinterest', TTO_I18N), __('http://pinterest.com/__username__', TTO_I18N)),
                'rss'           => array(__('Subscribe to my RSS feed', TTO_I18N)),
                'skype'         => array(__('Connect us on Skype', TTO_I18N)),
                'tumblr'        => array(__('Follow me on Tumblr', TTO_I18N), __('http://', TTO_I18N)),
                'twitter'       => array(__('Follow me on Twitter', TTO_I18N), __('http://www.twitter.com/__username__', TTO_I18N)),
                'vimeo'         => array(__('Follow me on Vimeo', TTO_I18N), __('http://www.vimeo.com/__username__', TTO_I18N)),
                'youtube'       => array(__('Follow me on Youtube', TTO_I18N), __('http://www.youtube.com/user/__username__', TTO_I18N))
            );

            $defaults = array();

            //Return only wanted
            if (isset($wanted) && !empty($wanted))
            {
                foreach ($wanted as $want)
                {
                    if (array_key_exists($want, $socials))
                    {
                        $defaults[$want] = $socials[$want];
                    }
                }
            }
            else
            {
                $defaults = $socials;
            }
        }
        //Return defauls Twitter API keys
        else if ('twitter' == $return)
        {
            $defaults = array(
                'consumer_key'      => 'T6K5yb4oGrS5UTZxsvDdhw',
                'consumer_secret'   => 'gpamCLVGgNZGN3jprq40A4JD5KzQ2PLqFIu5lUQyw'
            );
        }
        //Return defaults text types
        else if ('text' == $return)
        {
            $defaults = array(
                'text' => __('Text', TTO_I18N),
                'email' => __('Email', TTO_I18N),
                'number' => __('Number', TTO_I18N),
                'range' => __('Range', TTO_I18N),
                'password' => __('Password', TTO_I18N),
                'search' => __('Search', TTO_I18N),
                'url' => __('URL', TTO_I18N)
            );
        }
        //Return defauls TTO types
        else if ('types' == $return)
        {
            $defaults = array(
                __('Display fields', TTO_I18N) => array(
                    'br' => __('Breakline', TTO_I18N),
                    'heading' => __('Heading', TTO_I18N),
                    'hr' => __('Horizontal rule', TTO_I18N),
                    'list' => __('List items', TTO_I18N),
                    'p' => __('Paragraphe', TTO_I18N)
                ),
                __('Common fields', TTO_I18N) => array(
                    'checkbox' => __('Checkbox', TTO_I18N),
                    'hidden' => __('Hidden field', TTO_I18N),
                    'multiselect' => __('Multiselect', TTO_I18N),
                    'radio' => __('Radio', TTO_I18N),
                    'select' => __('Select', TTO_I18N),
                    'text' => __('Basic text, email, number and more', TTO_I18N),
                    'textarea' => __('Textarea', TTO_I18N)
                ),
                __('Special fields', TTO_I18N) => array(
                    'background' => __('Background', TTO_I18N),
                    'color' => __('Color', TTO_I18N),
                    'font' => __('Google Fonts', TTO_I18N),
                    'include' => __('Include PHP file', TTO_I18N),
                    'rte' => __('Wordpress RTE', TTO_I18N),
                    'social' => __('Social', TTO_I18N),
                    'upload' => __('Wordpress Upload', TTO_I18N)
                ),
                __('Wordress fields', TTO_I18N) => array(
                    'wordpress' => __('Categories, menus, pages, posts, posttypes and tags', TTO_I18N)
                )/*,
                __('Social Networks fields', TTO_I18N) => array(
                    'flickr' => __('FlickR', TTO_I18N),
                    'instagram' => __('Instagram', TTO_I18N),
                    'twitter' => __('Twitter', TTO_I18N)
                )*/
            );
        }
        //Return defauls TTO types and only choices
        else if ('typeschoices' == $return)
        {
            $defaults = array(
                'checkbox', 'radio', 'select', 'multiselect'
            );
        }
        //Return defauls TTO types and only networks
        else if ('typesnetworks' == $return)
        {
            $defaults = array(
                'flickr', 'instagram', 'twitter'
            );
        }
        //Return defauls TTO types without format
        else if ('typesraw' == $return)
        {
            $defaults = array(
                'br', 'heading', 'hr', 'list', 'p', 'checkbox',
                'hidden', 'radio', 'select', 'multiselect',
                'text', 'textarea', 'background', 'color',
                'font', 'include', 'rte', 'social', 'upload',
                'wordpress'/*, 'flickr', 'instagram', 'twitter'*/
            );
        }
        //Return defauls TTO types and only Wordpress
        else if ('typeswordpress' == $return)
        {
            $defaults = array(
                'categories' => __('Categories', TTO_I18N),
                'menus' => __('Menus', TTO_I18N),
                'pages' => __('Pages', TTO_I18N),
                'posts' => __('Posts', TTO_I18N),
                'posttypes' => __('Post types', TTO_I18N),
                'tags' => __('Tags', TTO_I18N)
            );
        }
        //Return defaults field without IDs
        else if ('withoutids' == $return)
        {
            $defaults = array(
                'br', 'dashboard', 'features', 'flickr', 'include',
                'instagram', 'heading', 'hr', 'group', 'list', 'p', 'twitter'
            );
        }

        //Return the array
        return $defaults;
    }

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
     * @since Tea Theme Options 1.3.2
     */
    protected function updateContents($request)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Add page
        if (isset($request['tea_add_page']))
        {
            //Check if a title has been defined
            if (!isset($request['tea_add_page_title']) || empty($request['tea_add_page_title']))
            {
                $this->adminmessage = __('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N);
                return false;
            }

            //Get vars
            $title = $request['tea_add_page_title'];
            $slug = '_' . sanitize_title($title);
            $description = isset($request['tea_add_page_description']) ? $request['tea_add_page_description'] : '';
            $submit = isset($request['tea_add_page_submit']) ? $request['tea_add_page_submit'] : '1';
            $submit = '1' == $submit ? true : false;

            //Get all pages
            $pages = $this->getOption('tea_config_pages', array());
            $pages = false === $pages || empty($pages) ? array() : $pages;

            //Check if slug is already in
            if (array_key_exists($slug, $pages))
            {
                $this->adminmessage = __('Something went wrong in your form: a page with your title already exists. Please, try another one.', TTO_I18N);
                return false;
            }

            $pages[$slug] = array(
                'title' => $title,
                'name' => $title,
                'description' => $description,
                'submit' => $submit,
                'slug' => $slug
            );

            //Insert pages in DB
            _set_option('tea_config_pages', $pages);
        }
        //Add page content
        else if (isset($request['tea_add_pagecontent']))
        {
            //Check if a page has been defined
            if (!isset($request['tea_page']) || empty($request['tea_page']))
            {
                $this->adminmessage = __('Something went wrong in your form: no page is defined. Please, try again by filling properly the form.', TTO_I18N);
                return false;
            }

            //Get all pages
            $pages = $this->getOption('tea_config_pages', array());
            $pages = false === $pages || empty($pages) ? array() : $pages;
            $slug = $request['tea_page'];

            //Check if the defined page exists properly
            if (!array_key_exists($slug, $pages))
            {
                $this->adminmessage = __('Something went wrong in your form: the defined page does not exist. Please, try again by using the form properly.', TTO_I18N);
                return false;
            }

            //Check if the user want to delete a page
            if (isset($request['delete_page']))
            {
                //Delete slug from pages
                unset($pages[$slug]);

                //Insert pages in DB
                _set_option('tea_config_pages', $pages);
            }
            //Check if the user want to edit a page
            else if (isset($request['edit_page']))
            {
                //Check if a title has been defined
                if (!isset($request['tea_edit_page_title']) || empty($request['tea_edit_page_title']))
                {
                    $this->adminmessage = __('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N);
                    return false;
                }

                //Get vars
                $title = $request['tea_edit_page_title'];
                $description = isset($request['tea_edit_page_description']) ? $request['tea_edit_page_description'] : '';
                $submit = isset($request['tea_edit_page_submit']) ? $request['tea_edit_page_submit'] : '1';
                $submit = '1' == $submit ? true : false;

                //Edit slug from pages
                $pages[$slug] = array(
                    'title' => $title,
                    'name' => $title,
                    'description' => $description,
                    'submit' => $submit,
                    'slug' => $slug
                );

                //Insert pages in DB
                _set_option('tea_config_pages', $pages);
            }
            //Check if the user want to save a page
            else if (isset($request['save_page']))
            {
                //Get vars
                $do_not_have_ids = $this->getDefaults('withoutids');
                $currents = array();
                $adminmessage = '';

                //Iterate on each content
                if (isset($request['tea_add_contents']))
                {
                    foreach ($request['tea_add_contents'] as $key => $ctn)
                    {
                        //Check if our type needs an id
                        $needid = !in_array($ctn['type'], $do_not_have_ids) ? true : false;

                        //Check if a title has been defined for all fields without IDs
                        if ($needid && (!isset($ctn['title']) || empty($ctn['title'])))
                        {
                            $adminmessage .= '<li>&bull; ' . sprintf(__('No title defined for your <b>%s</b> field.', TTO_I18N), $ctn['type']) . '</li>';
                            continue;
                        }

                        //Check if an id is required
                        if ($needid)
                        {
                            //Get the old ID if it was defined
                            $old_id = isset($pages[$slug]['contents'][$key]['id']) && !empty($pages[$slug]['contents'][$key]['id']) ? $pages[$slug]['contents'][$key]['id'] : '';

                            //Make the new ID
                            $ctn['id'] = !empty($old_id) ? $old_id : $slug . '_' . sanitize_title($ctn['title']);
                        }

                        //Add content
                        $currents[] = $ctn;
                    }
                }

                //Check if contents are already defined, so unset it
                if (isset($pages[$slug]['contents']) && !empty($pages[$slug]['contents']))
                {
                    unset($pages[$slug]['contents']);
                }

                //Assign options to the current page
                $pages[$slug]['contents'] = $currents;

                //Insert contents in DB
                _set_option('tea_config_pages', $pages);

                //Check error messages without disturbing actions
                if (!empty($adminmessage))
                {
                    $this->adminmessage = '<p>' . __('Something went wrong in your form:', TTO_I18N) . '</p><ul>' . $adminmessage . '</ul><p>' . __('Please, try again by filling properly the form.', TTO_I18N) . '</p>';
                    return false;
                }
            }
        }
        //Add custom post type
        else if (isset($request['tea_add_cpt']))
        {
            //Check if a title has been defined
            if (!isset($request['tea_add_cpt_title']) || empty($request['tea_add_cpt_title']))
            {
                $this->adminmessage = __('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N);
                return false;
            }

            //Get vars
            $title = $request['tea_add_cpt_title'];
            $slug = '_' . sanitize_title($title);

            //Get all pages
            $cpts = $this->getOption('tea_config_cpts', array());
            $cpts = false === $cpts || empty($cpts) ? array() : $cpts;

            //Check if slug is already in
            if (array_key_exists($slug, $cpts))
            {
                $this->adminmessage = __('Something went wrong in your form: a custom post type with your title already exists. Please, try another one.', TTO_I18N);
                return false;
            }

            $cpts[$slug] = array(
                'title' => $title,
                'slug' => $slug
            );

            //Insert pages in DB
            _set_option('tea_config_cpts', $cpts);
        }
        //Add cutsom post type content
        else if (isset($request['tea_add_cptcontent']))
        {
            //Check if a page has been defined
            if (!isset($request['tea_cpt']) || empty($request['tea_cpt']))
            {
                $this->adminmessage = __('Something went wrong in your form: no custom post type is defined. Please, try again by filling properly the form.', TTO_I18N);
                return false;
            }

            //Get all pages
            $cpts = $this->getOption('tea_config_cpts', array());
            $cpts = false === $cpts || empty($cpts) ? array() : $cpts;
            $slug = $request['tea_cpt'];

            //Check if the defined page exists properly
            if (!array_key_exists($slug, $cpts))
            {
                $this->adminmessage = __('Something went wrong in your form: the defined custom post type does not exist. Please, try again by using the form properly.', TTO_I18N);
                return false;
            }

            //Check if the user want to delete a custom post type
            if (isset($request['delete_cpt']))
            {
                //Delete slug from cpts
                unset($cpts[$slug]);

                //Insert pages in DB
                _set_option('tea_config_cpts', $cpts);
            }
            //Check if the user want to save a page
            else if (isset($request['save_cpt']))
            {
                //Get vars
                $currents = array();

                //Iterate on each content
                if (isset($request['tea_add_contents']))
                {
                    //Check if a title has been defined for all fields without IDs
                    if (!isset($request['tea_add_contents']['title']) || empty($request['tea_add_contents']['title']))
                    {
                        $adminmessage = sprintf(__('Something went wrong in your form: no title defined for your <b>%s</b> Custom post type. Please, try again by filling properly the form.', TTO_I18N), $slug);
                        return false;
                    }

                    //Add content
                    $currents = $request['tea_add_contents'];
                }

                //Check if contents are already defined, so unset it
                if (isset($cpts[$slug]) && !empty($cpts[$slug]))
                {
                    unset($cpts[$slug]);
                }

                //Assign options to the current custom post type
                $cpts[$slug] = $currents;
                $cpts[$slug]['slug'] = $slug;

                //Insert contents in DB
                _set_option('tea_config_cpts', $cpts);
            }
        }
    }

    /**
     * Build data from the asked network.
     *
     * @uses date_i18n()
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.3.2
     */
    protected function updateNetworks($request)
    {
        //Define date of update
        $timer = date_i18n(get_option('date_format') . ', ' . get_option('time_format'));

        //Get includes
        $includes = $this->getIncludes();

        //Check Instagram
        if ('instagram' == $request['tea_to_network'])
        {
            //Define date of update
            _set_option('tea_instagram_connection_update', $timer);

            //Check if Google Font has already been included
            if (!isset($includes['instagram']))
            {
                $this->setIncludes('instagram');
                include_once $this->getDirectory('normal') . '/includes/instaphp/instaphp.php';
            }

            //Get token from DB
            $token = $this->getOption('tea_instagram_access_token', '');

            //Get user info
            $api = Instaphp\Instaphp::Instance($token);
            $user_info = $api->Users->Info();
            $user_recent = $api->Users->Recent('self');

            //Uodate DB with the user info
            _set_option('tea_instagram_user_info', $user_info->data);

            //Update DB with the user info
            $recents = array();
                //Iterate
            foreach ($user_recent->data as $item)
            {
                $recents[] = array(
                    'link' => $item->link,
                    'url' => $item->images->thumbnail->url,
                    'title' => empty($item->caption->text) ? __('Untitled', TTO_I18N) : $item->caption->text,
                    'width' => $item->images->thumbnail->width,
                    'height' => $item->images->thumbnail->height,
                    'likes' => $item->likes->count,
                    'comments' => $item->comments->count
                );
            }
                //Update
            _set_option('tea_instagram_user_recent', $recents);
        }
        //Check Flickr
        else if ('flickr' == $request['tea_to_network'])
        {
            //Check if a username is defined
            if (isset($request['tea_flickr_install']) && (!isset($request['tea_flickr_username']) || empty($request['tea_flickr_username'])))
            {
                $this->adminmessage = __('Something went wrong in your parameters definition. You need to specify a username to get connected.', TTO_I18N);
                return false;
            }

            //Define date of update
            _set_option('tea_flickr_connection_update', $timer);

            //Check if Flickr has already been included
            if (!isset($includes['flickr']))
            {
                $this->setIncludes('flickr');
                include_once $this->getDirectory('normal') . '/includes/phpflickr/phpFlickr.php';
            }

            //Get Flickr configurations
            $defaults = $this->getDefaults('flickr');

            //Get Flickr instance with token
            $api = new phpFlickr($defaults['api_key']);

            //Install a new user
            if (isset($request['tea_flickr_install']))
            {
                //Get Flickr instance with token
                $user_info = $api->people_findByUsername($request['tea_flickr_username']);

                //Check if the API returns value
                if (false === $user_info || empty($user_info))
                {
                    $this->adminmessage = __('Something went wrong in your parameters definition. The username specified is unknown.', TTO_I18N);
                    return false;
                }

                //Update DB with the user info
                _set_option('tea_flickr_user_info', $user_info);
            }

            //Get user info
            $user_info = isset($user_info) ? $user_info : $this->getOption('tea_flickr_user_info', array());

            //Update DB with the user details
            $user_details = $api->people_getInfo($user_info['id']);
            _set_option('tea_flickr_user_details', $user_details);

            //Update DB with the user info
            $user_recent = $api->people_getPublicPhotos($user_info['id'], null, null, 20, 1);
            $recents = array();
                //Iterate
            foreach ($user_recent['photos']['photo'] as $item)
            {
                $recents[] = array(
                    'link' => 'http://www.flickr.com/photos/' . $item['owner'] . '/' . $item['id'],
                    'url' => $api->buildPhotoURL($item, 'medium_640'),
                    'url_small' => $api->buildPhotoURL($item, 'square'),
                    'title' => $item['title']
                );
            }
                //Update
            _set_option('tea_flickr_user_recent', $recents);
        }
        //Check Twitter
        else if ('twitter' == $request['tea_to_network'])
        {
            //Define date of update
            _set_option('tea_twitter_connection_update', $timer);

            //Check if Twitter has already been included
            if (!isset($includes['twitter']))
            {
                $this->setIncludes('twitter');
                include_once $this->getDirectory('normal') . '/includes/twitteroauth/twitteroauth.php';
            }

            //Get Twitter configurations
            $defaults = $this->getDefaults('twitter');

            //Get token from DB
            $token = $this->getOption('tea_twitter_access_token', '');

            //Build TwitterOAuth object
            $api = new TwitterOAuth($defaults['consumer_key'], $defaults['consumer_secret'], $token['oauth_token'], $token['oauth_token_secret']);

            //Get user info
            $user_info = $api->get('account/verify_credentials');
            _set_option('tea_twitter_user_info', $user_info);

            //Get recent tweets
            $user_recent = $api->get('statuses/user_timeline');
            _set_option('tea_twitter_user_recent', $user_recent);
        }
    }

    /**
     * Register $_POST and $_FILES into transients.
     *
     * @uses wp_handle_upload()
     * @param array $post Contains all data in $_POST
     * @param array $files Contains all data in $_FILES
     *
     * @since Tea Theme Options 1.3.0
     */
    protected function updateOptions($post, $files)
    {
        //Check if we are in admin panel
        if (!$this->getIsAdmin())
        {
            return false;
        }

        //Set all options in transient
        foreach ($post as $k => $v)
        {
            //Don't register this default value
            if ('tea_to_settings' == $k || 'submit' == $k)
            {
                continue;
            }

            //Special usecase: checkboxes. When it's not checked, no data is sent through the $_POST array
            $p = false !== strpos($k, '__checkbox') ? $post : array();

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
