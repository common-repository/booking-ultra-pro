<?php
// General Functions for Plugin

if (!defined('PHP_EOL')) {
    switch (strtoupper(substr(PHP_OS, 0, 3))) {
        // Windows
        case 'WIN':
            define('PHP_EOL', "\r\n");
			//echo "IS WINDOW SERVER";
            break;

        // Mac
        case 'DAR':
            define('PHP_EOL', "\r");
            break;

        // Unix
        default:
            define('PHP_EOL', "\n");
    }
}

//echo "OSD: " .PHP_OS;

if (!function_exists('is_post')) {

    function is_post() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
            return true;
        else
            return false;
    }

}





if (!function_exists('is_in_post')) {

    function is_in_post($key='', $val='') {
        if ($key == '') {
            return false;
        } else {
            if (isset($_POST[$key])) {
                if ($val == '')
                    return true;
                else if ($_POST[$key] == $val)
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
    }

}

if (!function_exists('is_get')) {

    function is_get() {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'get')
            return true;
        else
            return false;
    }

}


if (!function_exists('is_in_get')) {

    function is_in_get($key='', $val='') {
        if ($key == '') {
            return false;
        } else {
            if (isset($_GET[$key])) {
                if ($val == '')
                    return true;
                else if ($_GET[$key] == $val)
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
    }

}

if(!function_exists('not_null'))
{
    function not_null($value)
    {
        if (is_array($value))
        {
            if (sizeof($value) > 0)
                return true;
            else
                return false;
        }
        else
        {
            if ( (is_string($value) || is_int($value)) && ($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0))
                return true;
            else
                return false;
        }
    } 
}



if(!function_exists('get_value'))
{
    function get_value($key='')
    {
        if($key!='')
        {
            if(isset($_GET[$key]) && not_null($_GET[$key]))
            {
                if(!is_array($_GET[$key]))
                    return trim(esc_attr($_GET[$key]));
                else
                    return esc_attr($_GET[$key]);
            }
    
            else
                return '';
        }
        else
            return '';
    }
}


if (!function_exists('remove_script_tags')) {

    function remove_script_tags($text) {
        $text = str_ireplace("<script>", "", $text);
        $text = str_ireplace("</script>", "", $text);

        return $text;
    }

}


if(!function_exists('post_value'))
{
    function post_value($key='')
    {
        if($key!='')
        {
            if(isset($_POST[$key]) && not_null($_POST[$key]))
            {
                if(!is_array($_POST[$key]))
                    return trim($_POST[$key]);
                else
                    return esc_attr($_POST[$key]);
            }
            else
                return '';
        }
        else
            return '';
    }
}


if(!function_exists('is_opera'))
{
    function is_opera()
    {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        return preg_match('/opera/i', $user_agent);
    }
}

if(!function_exists('is_safari'))
{
    function is_safari()
    {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        return (preg_match('/safari/i', $user_agent) && !preg_match('/chrome/i', $user_agent));
    }
}


// Check with the magic quotes functionality Start
/*function stripslashess(&$item)
{
    $item = stripslashes($item);
}

if(get_magic_quotes_gpc())
{
    array_walk_recursive($_GET, 'stripslashess' );
    array_walk_recursive($_POST, 'stripslashess');
    array_walk_recursive($_SERVER, 'stripslashess');
}*/

if(!function_exists('is_active'))
{

/* Check if user is active before login  */
	function is_active($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'bup_account_status', true);
		if ($checkuser == 'active')
			return true;
		return false;
	}
}

function bup_get_current_admin_url($args = array() , $remove_args = array() ) {

    $uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
    
    if ( ! $uri ) {
        return '';
    }

    $admin_url = admin_url( $uri );
    if(!empty($args)) {
        $admin_url = add_query_arg( $args, $admin_url );    
    }   
     
    return remove_query_arg( array( '_wpnonce' ), $admin_url );

}

function escape_with_custom_html($content) {
    $allowed_html = array(
        'a' => array(
            'href'  => true,
            'title' => true,
            'rel'   => true,
            'target' => true,
            'class' => true,
            'id'    => true,
        ),
        'abbr' => array(
            'title' => true,
        ),
        'acronym' => array(
            'title' => true,
        ),
        'address' => array(),
        'area' => array(
            'href'   => true,
            'alt'    => true,
            'coords' => true,
            'shape'  => true,
            'target' => true,
        ),
        'article' => array(),
        'aside' => array(),
        'b' => array(),
        'bdi' => array(
            'dir' => true,
        ),
        'bdo' => array(
            'dir' => true,
        ),
        'blockquote' => array(
            'cite' => true,
        ),
        'br' => array(),
        'button' => array(
            'type'  => true,
            'name'  => true,
            'value' => true,
            'class' => true,
            'id'    => true,
        ),
        'caption' => array(),
        'cite' => array(),
        'code' => array(),
        'col' => array(
            'span' => true,
        ),
        'colgroup' => array(
            'span' => true,
        ),
        'data' => array(
            'value' => true,
        ),
        'dd' => array(),
        'del' => array(
            'datetime' => true,
        ),
        'details' => array(),
        'dfn' => array(),
        'div' => array(
            'class' => true,
            'id'    => true,
        ),
        'dl' => array(),
        'dt' => array(),
        'em' => array(),
        'embed' => array(
            'type'    => true,
            'src'     => true,
            'width'   => true,
            'height'  => true,
            'allow'   => true,
        ),
        'fieldset' => array(),
        'figcaption' => array(),
        'figure' => array(),
        'footer' => array(),
        'form' => array(
            'action' => true,
            'method' => true,
            'enctype' => true,
            'target' => true,
        ),
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
        'header' => array(),
        'hgroup' => array(),
        'hr' => array(),
        'i' => array(),
        'iframe' => array(
            'src'    => true,
            'width'  => true,
            'height' => true,
            'frameborder' => true,
            'allow'  => true,
        ),
        'img' => array(
            'src'    => true,
            'alt'    => true,
            'title'  => true,
            'width'  => true,
            'height' => true,
            'class'  => true,
            'id'     => true,
        ),
        'input' => array(
            'type'     => true,
            'name'     => true,
            'value'    => true,
            'id'       => true,
            'class'    => true,
            'placeholder' => true,
            'checked'  => true,
            'disabled' => true,
            'readonly' => true,
            'required' => true,
            'maxlength' => true,
        ),
        'ins' => array(
            'cite'    => true,
            'datetime' => true,
        ),
        'kbd' => array(),
        'label' => array(
            'for' => true,
        ),
        'legend' => array(),
        'li' => array(
            'value' => true,
        ),
        'link' => array(
            'href'  => true,
            'rel'   => true,
            'type'  => true,
            'media' => true,
        ),
        'main' => array(),
        'map' => array(),
        'mark' => array(),
        'meta' => array(
            'name'  => true,
            'content' => true,
            'http-equiv' => true,
            'charset' => true,
        ),
        'meter' => array(
            'value'   => true,
            'min'     => true,
            'max'     => true,
            'low'     => true,
            'high'    => true,
            'optimum' => true,
        ),
        'nav' => array(),
        'noscript' => array(),
        'object' => array(
            'data'    => true,
            'type'    => true,
            'width'   => true,
            'height'  => true,
        ),
        'ol' => array(),
        'optgroup' => array(
            'label' => true,
        ),
        'option' => array(
            'value' => true,
            'disabled' => true,
            'selected' => true,
        ),
        'output' => array(
            'for' => true,
            'name' => true,
        ),
        'p' => array(),
        'param' => array(
            'name'  => true,
            'value' => true,
        ),
        'pre' => array(),
        'progress' => array(
            'value'   => true,
            'max'     => true,
        ),
        'q' => array(
            'cite' => true,
        ),
        'rp' => array(),
        'rt' => array(),
        'ruby' => array(),
        's' => array(),
        'samp' => array(),
        'script' => array(
            'type' => true,
        ),
        'section' => array(),
        'select' => array(
            'name'     => true,
            'multiple' => true,
            'size'     => true,
            'disabled' => true,
            'required' => true,
        ),
        'small' => array(),
        'source' => array(
            'src'  => true,
            'type' => true,
            'media' => true,
        ),
        'span' => array(
            'class' => true,
            'id'    => true,
        ),
        'strong' => array(),
        'style' => array(
            'type' => true,
        ),
        'sub' => array(),
        'summary' => array(),
        'sup' => array(),
        'table' => array(
            'border' => true,
            'cellspacing' => true,
            'cellpadding' => true,
            'width'  => true,
            'height' => true,
        ),
        'tbody' => array(),
        'td' => array(
            'colspan' => true,
            'rowspan' => true,
            'align'   => true,
            'valign'  => true,
            'width'   => true,
            'height'  => true,
        ),
        'textarea' => array(
            'name'      => true,
            'rows'      => true,
            'cols'      => true,
            'disabled'  => true,
            'readonly'  => true,
        ),
        'tfoot' => array(),
        'th' => array(
            'colspan' => true,
            'rowspan' => true,
            'align'   => true,
            'valign'  => true,
            'width'   => true,
            'height'  => true,
        ),
        'thead' => array(),
        'time' => array(
            'datetime' => true,
        ),
        'title' => array(),
        'tr' => array(),
        'u' => array(),
        'ul' => array(),
        'var' => array(),
        'video' => array(
            'src'    => true,
            'width'  => true,
            'height' => true,
            'controls' => true,
            'autoplay' => true,
            'loop'    => true,
            'muted'   => true,
        ),
        'wbr' => array(),
    );

    // Use wp_kses to filter the content
    return wp_kses($content, $allowed_html);
}


