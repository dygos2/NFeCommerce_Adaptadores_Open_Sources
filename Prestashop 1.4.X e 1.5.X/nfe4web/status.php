<?php
/*
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  
 * sac@prestabr.com.br
 */
 
global $smarty;
include('../../config/config.inc.php');
//include('../../header.php');

$token = $_GET['token'];

if ( $token != Configuration::get('NFE4WEB_STRTOKEN') || !isset($token) ) exit;

$defaultLang = Configuration::get('PS_LANG_DEFAULT');
$statuses = OrderState::getOrderStates($defaultLang);

$status = array();
foreach ( $statuses as $key => $value ){
    $status[$key] = array("xOrderStatusID" => $value['id_order_state'], "xStatus" => $value['name']);
}

$outStatus = $_GET["callback"] . '(' . Tools::jsonEncode($status) . ')';

$smarty->assign('status', $outStatus);

$smarty->display(dirname(__FILE__).'/status.tpl');
 
//include('../../footer.php');

?>
