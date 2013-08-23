<?php
/**
 * Tea Theme Options Font field
 * 
 * @package TakeaTea
 * @subpackage Tea Fields Font
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
 * Tea Fields Font
 *
 * To get its own Fields
 *
 * @since Tea Theme Options 1.4.5
 *
 */
class Tea_Fields_Font extends Tea_Fields
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
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $default = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;

        //Get options & fonts
        $options = isset($content['options']) ? $content['options'] : array();
        $fonts = $this->getDefaults('fonts');

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
        $title = isset($content['title']) ? $content['title'] : __('Tea Font', TTO_I18N);
        $std = isset($content['std']) ? $content['std'] : '';
        $description = isset($content['description']) ? $content['description'] : '';
        $default = isset($content['default']) && (true === $content['default'] || '1' == $content['default']) ? true : false;

        //Get options
        $options = isset($content['options']) ? $content['options'] : array();

        if ($default)
        {
            $default = $this->getDefaults('fonts');
            $options = array_merge($default, $options);
        }

        //Get includes
        $includes = $this->getIncludes();
        $linkstylesheet = '';
        $gfontstyle = '';

        //Check if Google Font has already been included
        if (!isset($includes['googlefonts']))
        {
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
        $stands = '';

        //Check for options
        if (!isset($content['options']))
        {
            return $content;
        }

        //Check defaults
        if (isset($content['stands']))
        {
            $stands = $content['stands'];
            unset($content['stands']);
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
        if (isset($content['options'][$stands]))
        {
            $content['std'] = $content['options'][$stands][0];
        }
        else
        {
            //Require master Class
            require_once(TTO_PATH . 'classes/class-tea-fields.php');

            //Get fonts
            $fonts = Tea_Fields::getDefaults('fonts');

            //Iterate on each font
            foreach ($fonts as $ft)
            {
                //Check 1st item
                if ($stands != $ft[0])
                {
                    continue;
                }

                //We found our default item
                $content['std'] = $ft[0];
            }
        }

        //Return modified contents
        return $content;
    }
}
