<?php
/**
 * Tea Theme Options Text field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Text
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
 * Tea Fields Text
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.0
 *
 */
class Tea_Fields_Text extends Tea_Fields
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
        $placeholder = isset($content['placeholder']) ? $content['placeholder'] : '';
        $std = isset($content['std']) ? $content['std'] : '';
        $maxlength = isset($content['maxlength']) ? $content['maxlength'] : '';

        //Get options
        $options = isset($content['options']) ? $content['options'] : array('type' => 'text', 'min' => '', 'max' => '', 'step' => '');
        $options['type'] = isset($content['options']['type']) ? $content['options']['type'] : 'text';
        $options['min'] = isset($content['options']['min']) ? $content['options']['min'] : '';
        $options['max'] = isset($content['options']['max']) ? $content['options']['max'] : '';
        $options['step'] = isset($content['options']['step']) ? $content['options']['step'] : '';

        //Get types
        $texts = $this->getDefaults('texts');

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
        include('in_pages.tpl.php');
    }
}
