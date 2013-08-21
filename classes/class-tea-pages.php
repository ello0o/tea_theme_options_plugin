<?php
/**
 * Tea TO backend functions and definitions
 * 
 * @package TakeaTea
 * @subpackage Tea Pages
 * @since Tea Theme Options 1.4.3
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}


//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Pages
 *
 * To get its own Pages
 *
 * @since Tea Theme Options 1.4.3
 *
 */
class Tea_Pages
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
     * @since Tea Theme Options 1.4.2
     */
    public function __construct($identifier)
    {
        //Check if we are in admin panel
        $this->setIsAdmin();

        //Admin panel
        if ($this->getIsAdmin())
        {
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
    }


    //--------------------------------------------------------------------------//

    /**
     * MAIN FUNCTIONS
     **/

    /**
     * Register menus.
     *
     * @uses add_action()
     *
     * @since Tea Theme Options 1.4.2
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
     * Hook building scripts.
     *
     * @uses wp_enqueue_media()
     * @uses wp_enqueue_script()
     *
     * @since Tea Theme Options 1.4.3
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
        if (function_exists('wp_enqueue_media'))
        {
            wp_enqueue_media();
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_script('accordion');
        }
        else
        {
            wp_enqueue_script('media-upload');
            wp_enqueue_script('farbtastic');
        }

        wp_enqueue_script('tea-modal', $directory . '/js/teamodal.js', array('jquery'));
        wp_enqueue_script('tea-to', $directory . '/js/teato.js', array('jquery', 'tea-modal'));
    }

    /**
     * Hook building styles.
     *
     * @uses wp_enqueue_style()
     *
     * @since Tea Theme Options 1.4.3
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
        wp_enqueue_style('farbtastic');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('tea-to', $directory . '/css/teato.css');
    }

    /**
     * Hook unload scripts.
     *
     * @uses wp_deregister_script()
     *
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * Hook building menus.
     *
     * @uses add_action()
     * @uses add_menu_page()
     * @uses add_submenu_page()
     *
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
     */
    protected function getFields()
    {
        $defaults = array(
            'br', 'features', 'heading', 'hr', 'list', 'p', 'checkbox',
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
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
     * @since Tea Theme Options 1.4.2
     */
    protected function getSlug($slug = '')
    {
        return $this->identifier . $slug;
    }
}
