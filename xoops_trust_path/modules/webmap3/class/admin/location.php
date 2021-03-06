<?php
// $Id: location.php,v 1.2 2012/04/09 11:52:19 ohwada Exp $

// 2012-04-02 K.OHWADA
// webmap3_api_form

//=========================================================
// webmap3 module
// 2012-03-01 K.OHWADA
//=========================================================

//=========================================================
// class webmap3_admin_location
//=========================================================
class webmap3_admin_location extends webmap3_admin_base
{
	var $_config_item_class;
	var $_form_class;
	var $_header_class;

	var $_THIS_FCT = 'location';
	var $_THIS_URL = null;

//---------------------------------------------------------
// constructor
//---------------------------------------------------------
function webmap3_admin_location( $dirname, $trust_dirname )
{
	$this->webmap3_admin_base( $dirname, $trust_dirname );

	$this->_config_item_class = webmap3_xoops_config_item::getInstance( $dirname );
	$this->_form_class   =& webmap3_api_form::getSingleton( $dirname );
	$this->_header_class =& webmap3_xoops_header::getSingleton( $dirname );

	$this->_THIS_URL = $this->build_this_url( $this->_THIS_FCT ) ;
}

public static function &getInstance( $dirname, $trust_dirname )
{
	static $instance;
	if (!isset($instance)) {
		$instance = new webmap3_admin_location( $dirname, $trust_dirname );
	}
	return $instance;
}

//---------------------------------------------------------
// function
//---------------------------------------------------------
function main()
{
	$op = isset($_POST['op']) ? $_POST['op'] : null ;

	if ( $op == 'edit' ) {
		if ( $this->check_token() ) {
			$this->save();
			redirect_header( $this->_THIS_URL, 1, _EDIT );
		}
	}

	xoops_cp_header();

	echo $this->build_admin_menu();
	echo $this->build_admin_title( 'location' );

	if ( $this->_token_error_flag ) {
		xoops_error('Ticket Error');
	}

	echo $this->build_content();

	xoops_cp_footer();
	exit();
}

function save()
{
	$this->_config_item_class->get_objects();
	if ( isset($_POST['webmap3_address']) ) {
		$this->_config_item_class->save( 'address', $_POST['webmap3_address'] );
	}
	if ( isset($_POST['webmap3_latitude']) ) {
		$this->_config_item_class->save( 'latitude', floatval($_POST['webmap3_latitude']) );
	}
	if ( isset($_POST['webmap3_longitude']) ) {
		$this->_config_item_class->save( 'longitude', floatval($_POST['webmap3_longitude']) );
	}
	if ( isset($_POST['webmap3_zoom']) ) {
		$this->_config_item_class->save( 'zoom', intval($_POST['webmap3_zoom']) );
	}
	if ( isset($_POST['webmap3_gicon_id']) ) {
		$this->_config_item_class->save( 'marker_gicon', intval($_POST['webmap3_gicon_id']) );
	}
}

function build_content()
{
	$template = 'db:'. $this->_DIRNAME .'_admin_location.html' ;
	$param   = $this->build_param();

	$tpl = new XoopsTpl();
	$tpl->assign( $param );
	return $tpl->fetch( $template );
}

function build_param()
{
	list( $show_default_css, $default_css ) = 
		$this->_header_class->assign_or_get_default_css( false ) ;

	$arr = array(
		'xoops_dirname'    => $this->_DIRNAME ,
		'default_css'      => $default_css ,
		'form_js'          => $this->_form_class->build_form_js( false ) ,
		'form_location'    => $this->build_form_location(),

// style
		'display_js'       => $this->_form_class->build_display_style_js() ,
		'map_display'      => $this->_form_class->build_form_desc_style(),
		'map_iframe'       => $this->_form_class->build_div_iframe(),

// html
//		'display_js'       => $this->_form_class->build_display_html_js() ,
//		'map_display'      => $this->_form_class->build_form_desc_html(),
//		'map_iframe'       => $this->_form_class->build_div_html(),
	);

	return $arr;
}

function build_form_location()
{
	$template = 'db:'. $this->_DIRNAME .'_inc_set_location_form.html' ;
	$param = $this->build_param_form();

	$tpl = new XoopsTpl();
	$tpl->assign( $param );
	return $tpl->fetch( $template );
}

function build_param_form()
{
	$arr = array(
		'ticket'          => $this->get_token() ,
		'address'         => $this->get_config('address') ,
		'latitude'        => $this->get_config('latitude') ,
		'longitude'       => $this->get_config('longitude') ,
		'zoom'            => $this->get_config('zoom') ,
		'icon_content'    => $this->build_icon_content() ,
		'lang_address'    => $this->get_lang('ADDRESS') ,
		'lang_latitude'   => $this->get_lang('LATITUDE') ,
		'lang_longitude'  => $this->get_lang('LONGITUDE') ,
		'lang_zoom'       => $this->get_lang('ZOOM') ,
		'lang_title'      => _AM_WEBMAP3_LOCATION ,
		'lang_icon'       => _AM_WEBMAP3_ICON ,
		'lang_edit'       => _EDIT ,
	);

	return $arr;
}

function build_icon_content()
{
	$id = $this->get_config('marker_gicon');

	$this->_form_class->set_gicon_select_name( 'webmap3_gicon_id' );
	$this->_form_class->set_gicon_select_id(   'webmap3_gicon_id' );
	$this->_form_class->set_gicon_img_id(      'webmap3_gicon_img' );

	$text  = $this->_form_class->build_gicon_icon();
	$text .= "<br />\n";
	$text .= $this->_form_class->build_ele_gicon( $id );

	return $text;
}

// --- class end ---
}

?>