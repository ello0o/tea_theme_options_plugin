<?php
/**
 * Tea Theme Options Dashboard field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Dashboard
 * @since Tea Theme Options 1.4.5
 *
 */

if (!defined('ABSPATH')) {
    die('You are not authorized to directly access to this page');
}


//---------------------------------------------------------------------------------------------------------//

//Require master Class
require_once(TTO_PATH . 'classes/class-tea-fields.php');

//---------------------------------------------------------------------------------------------------------//

/**
 * Tea Fields Dashboard
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.5
 *
 */
class Tea_Fields_Dashboard extends Tea_Fields
{
    //Define protected vars
    private $currentpage;

    /**
     * Constructor.
     *
     * @since Tea Theme Options 1.4.0
     */
    public function __construct(){}


    //--------------------------------------------------------------------------//

    /**
     * MAIN FUNCTIONS
     **/

    /**
     * Build HTML component to display in the Tea T.O. dashboard.
     *
     * @param number $number Define the position of the input
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    public function templateDashboard($number = '__NUM__', $content = array())
    {
        return false;
    }

    /**
     * Build HTML component to display in all the Tea T.O. defined pages.
     *
     * @param array $content Contains all data
     *
     * @since Tea Theme Options 1.4.0
     */
    public function templatePages($content)
    {
        //Default variables
        $title = isset($content['title']) ? $content['title'] : __('Tea Dashboard', TTO_I18N);
        $page = $this->getCurrentPage();
        $includes = $this->getIncludes();
        $contents = array();

        //Get pages and contents
        $pages = _get_option('tea_config_pages', array());
        $cpts = _get_option('tea_config_cpts', array());

        //Get lists
        $fonts = $this->getDefaults('fonts');
        $typesgood = $this->getDefaults('types');

        //Work on fonts
        $linkstylesheet = '';
        $gfontstyle = '';

        //Iterate on them
        foreach ($fonts as $ft)
        {
            if (empty($ft[0]) || 'sansserif' == $ft[0])
            {
                continue;
            }
            $linkstylesheet .= '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' . $ft[0] . ':' . $ft[2] . '" />' . "\n";
            $gfontstyle .= '.gfont_' . str_replace(' ', '_', $ft[1]) . ' {font-family:\'' . $ft[1] . '\',sans-serif;}' . "\n";
        }

        //Define ajax vars
        $action = TTO_ACTION;
        $nonce = esc_js(wp_create_nonce(TTO_NONCE));
        $ajax = admin_url() . 'admin-ajax.php';

        //Count pages and default pages
        $count_page = count($pages);
        $count_cpt = count($cpts);

        //Work on contents
        foreach ($pages as $pg)
        {
            if (!isset($pg['contents']))
            {
                continue;
            }

            foreach ($pg['contents'] as $cont)
            {
                if (!isset($includes[$cont['type']]))
                {
                    $this->setIncludes($cont['type']);
                    require_once(TTO_PATH . 'classes/fields/' . $cont['type'] . '/class-tea-fields-' . $cont['type'] . '.php');
                }
            }
        }

        //Get template
        include('in_pages_header.tpl.php');
        include('in_pages_menu.tpl.php');
        include('in_pages_content.tpl.php');
        include('in_pages_footer.tpl.php');
    }

    /**
     * Build action method.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.0
     */
    public function actionDashboard($request)
    {
        //Add page
        if (isset($request['tea_add_page']))
        {
            $this->addPage($request);
        }
        //Add page content
        else if (isset($request['tea_add_pagecontent']))
        {
            $this->addPageContent($request);
        }
        //Add custom post type
        else if (isset($request['tea_add_cpt']))
        {
            $this->addCustomPostType($request);
        }
        //Add cutsom post type content
        else if (isset($request['tea_add_cptcontent']))
        {
            $this->addCustomPostTypeContent($request);
        }
    }

    /**
     * Add page.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.5
     */
    protected function addPage($request)
    {
        //Check if a title has been defined
        if (!isset($request['tea_add_page_title']) || empty($request['tea_add_page_title']))
        {
            $this->setAdminMessage(__('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N));
            return false;
        }

        //Get vars
        $title = $request['tea_add_page_title'];
        $slug = '_' . sanitize_title($title);
        $description = isset($request['tea_add_page_description']) ? $request['tea_add_page_description'] : '';
        $submit = isset($request['tea_add_page_submit']) ? $request['tea_add_page_submit'] : '1';
        $submit = '1' == $submit ? true : false;

        //Get all pages
        $pages = _get_option('tea_config_pages', array());
        $pages = false === $pages || empty($pages) ? array() : $pages;

        //Check if slug is already in
        if (array_key_exists($slug, $pages))
        {
            $this->setAdminMessage(__('Something went wrong in your form: a page with your title already exists. Please, try another one.', TTO_I18N));
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

    /**
     * Add page content.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.5
     */
    protected function addPageContent($request)
    {
        //Check if a page has been defined
        if (!isset($request['tea_page']) || empty($request['tea_page']))
        {
            $this->setAdminMessage(__('Something went wrong in your form: no page is defined. Please, try again by filling properly the form.', TTO_I18N));
            return false;
        }

        //Get all pages
        $pages = _get_option('tea_config_pages', array());
        $pages = false === $pages || empty($pages) ? array() : $pages;
        $slug = $request['tea_page'];

        //Check if the defined page exists properly
        if (!array_key_exists($slug, $pages))
        {
            $this->setAdminMessage(__('Something went wrong in your form: the defined page does not exist. Please, try again by using the form properly.', TTO_I18N));
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
                $this->setAdminMessage(__('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N));
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
            $includes = $this->getIncludes();
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

                    //Include class field
                    if (!isset($includes[$ctn['type']]))
                    {
                        //Set the include
                        $this->setIncludes($ctn['type']);

                        //Require file
                        require_once(TTO_PATH . 'classes/fields/' . $ctn['type'] . '/class-tea-fields-' . $ctn['type'] . '.php');
                    }

                    //Make the magic and add content
                    $class = 'Tea_Fields_' . ucfirst($ctn['type']);
                    $treated_content = $class::saveContent($ctn);
                    $currents[] = !empty($treated_content) ? $treated_content : $ctn;
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
                $this->setAdminMessage('<p>' . __('Something went wrong in your form:', TTO_I18N) . '</p><ul>' . $adminmessage . '</ul><p>' . __('Please, try again by filling properly the form.', TTO_I18N) . '</p>');
                return false;
            }
        }
    }

    /**
     * Add Custom Post Type.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.5
     */
    protected function addCustomPostType($request)
    {
        //Check if a title has been defined
        if (!isset($request['tea_add_cpt_title']) || empty($request['tea_add_cpt_title']))
        {
            $this->setAdminMessage(__('Something went wrong in your form: no title is defined. Please, try again by filling properly the form.', TTO_I18N));
            return false;
        }

        //Get vars
        $title = $request['tea_add_cpt_title'];
        $slug = '_' . sanitize_title($title);

        //Get all pages
        $cpts = _get_option('tea_config_cpts', array());
        $cpts = false === $cpts || empty($cpts) ? array() : $cpts;

        //Check if slug is already in
        if (array_key_exists($slug, $cpts))
        {
            $this->setAdminMessage(__('Something went wrong in your form: a custom post type with your title already exists. Please, try another one.', TTO_I18N));
            return false;
        }

        $cpts[$slug] = array(
            'title' => $title,
            'slug' => $slug
        );

        //Insert pages in DB
        _set_option('tea_config_cpts', $cpts);
    }

    /**
     * Add Custom Post Type content.
     *
     * @param array $request Contains all data sent in $_REQUEST method
     *
     * @since Tea Theme Options 1.4.5
     */
    protected function addCustomPostTypeContent($request)
    {
        //Check if a page has been defined
        if (!isset($request['tea_cpt']) || empty($request['tea_cpt']))
        {
            $this->setAdminMessage(__('Something went wrong in your form: no custom post type is defined. Please, try again by filling properly the form.', TTO_I18N));
            return false;
        }

        //Get all pages
        $cpts = _get_option('tea_config_cpts', array());
        $cpts = false === $cpts || empty($cpts) ? array() : $cpts;
        $slug = $request['tea_cpt'];

        //Check if the defined page exists properly
        if (!array_key_exists($slug, $cpts))
        {
            $this->setAdminMessage(__('Something went wrong in your form: the defined custom post type does not exist. Please, try again by using the form properly.', TTO_I18N));
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

    /**
     * ACCESSORS
     **/

    /**
     * Retrieve the $currentpage value
     *
     * @return string $currentpage Get the current page
     *
     * @since Tea Theme Options 1.4.0
     */
    protected function getCurrentPage()
    {
        //Return value
        return $this->currentpage;
    }

    /**
     * Define the $currentpage value
     *
     * @param string $currentpage Get the current page
     *
     * @since Tea Theme Options 1.4.0
     */
    public function setCurrentPage($currentpage = '')
    {
        $this->currentpage = $currentpage;
    }
}
