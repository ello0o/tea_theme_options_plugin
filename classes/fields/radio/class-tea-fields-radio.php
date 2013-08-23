<?php
/**
 * Tea Theme Options Radio field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Radio
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
 * Tea Fields Radio
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.5
 *
 */
class Tea_Fields_Radio extends Tea_Fields
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
        $title = isset($content['title']) ? $content['title'] : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $std = isset($content['std']) ? $content['std'] : array();
        $options = isset($content['options']) ? $content['options'] : array();

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
        $title = isset($content['title']) ? $content['title'] : __('Tea Radio', TTO_I18N);
        $description = isset($content['description']) ? $content['description'] : '';
        $std = isset($content['std']) ? $content['std'] : '';
        $options = isset($content['options']) ? $content['options'] : array();

        //Check selected
        $val = $this->getOption($id, $std);

        //Get template
        include('in_pages.tpl.php');
    }


    //--------------------------------------------------------------------------//

    /**
     * PRE SAVE METHOD
     **/

    /**
     * Edit contents before saving.
     *
     * @param array $content Content sent throught Dahsboard forms.
     * @return array $content Content modified.
     *
     * @since Tea Theme Options 1.4.5
     */
    static function saveContent($content)
    {
        //Treat all defaults value
        $default = '';

        //Check for options
        if (!isset($content['options']))
        {
            return $content;
        }

        //Check defaults
        if (isset($content['default']))
        {
            $default = $content['default'];
            unset($content['default']);
        }

        //Check for __OPTNUM__
        if (isset($content['options']['__OPTNUM__']))
        {
            unset($content['options']['__OPTNUM__']);
        }

        //Iterate on each options
        foreach ($content['options'] as $k => $ctn)
        {
            //Check label
            if (empty($ctn[1]))
            {
                unset($content['options'][$k]);
                continue;
            }

            //Create value from label
            $value_sanitized = sanitize_title($ctn[1]);
            $content['options'][$k][0] = $value_sanitized;
        }

        //Get default
        $content['std'] = empty($default) ? $content['std'] : (isset($content['options'][$default]) ? $content['options'][$default][0] : '');

        //Return modified contents
        return $content;
    }
}
