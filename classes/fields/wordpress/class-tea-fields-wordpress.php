<?php
/**
 * Tea Theme Options Wordpress field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Wordpress
 * @since Tea Theme Options 1.4.0
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
 * Tea Fields Wordpress
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.0
 *
 */
class Tea_Fields_Wordpress extends Tea_Fields
{
    //Define protected vars

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
        //Default variables
        $id = isset($content['id']) ? $content['id'] : '';
        $mode = isset($content['mode']) ? $content['mode'] : '';
        $title = isset($content['title']) ? $content['title'] : '';
        $multiple = isset($content['multiple']) && (true === $content['multiple'] || '1' == $content['multiple']) ? true : false;
        $description = isset($content['description']) ? $content['description'] : '';

        //Get the categories
        $wordpress = $this->getDefaults('wordpress');
        $contents = $this->getWPContents($mode, $multiple);

        //Get template
        include('in_dashboard.tpl.php');
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
        //Check if an id is defined at least
        $this->checkId($content);

        //Default variables
        $id = $content['id'];
        $mode = isset($content['mode']) ? $content['mode'] : 'posts';
        $title = isset($content['title']) ? $content['title'] : __('Tea Wordpress Contents', TTO_I18N);
        $multiple = isset($content['multiple']) && (true === $content['multiple'] || '1' == $content['multiple']) ? true : false;
        $description = isset($content['description']) ? $content['description'] : '';

        //Get the categories
        $contents = $this->getWPContents($mode, $multiple);

        //Check selected
        $vals = $this->getOption($id, array());
        $vals = empty($vals) ? array(0) : (is_array($vals) ? $vals : array($vals));

        //Get template
        include('in_pages.tpl.php');
    }
}
