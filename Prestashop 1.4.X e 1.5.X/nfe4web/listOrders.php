<?php
/*
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  
 * sac@prestabr.com.br
 */
 
global $smarty;
include('../../config/config.inc.php');

$token = $_GET['token'];

if ( $token != Configuration::get('NFE4WEB_STRTOKEN') || !isset($token) ) exit;

$convertDate = strtotime($_GET['para1']);
$convertDate2 = strtotime($_GET['para2']);

// Data Inicial
$para1 = ( $convertDate ? date("Y-m-d", $convertDate) : date("Y-m-d") );

// Data Final
$para2 = ( $convertDate2 ? date("Y-m-d", $convertDate2) : date("Y-m-d") );

// Número do Pedido
if (isset($_GET['para3'])){$para3 = (int)$_GET['para3'];}

// ID do Status do pedido
if (isset($_GET['para4'])){$para4 = (int)$_GET['para4'];}

// $params
if (isset($para1) && isset($para2))
	{
		$params = array('para1' => $para1, 'para2' => $para2);
	}
if (isset($para1) && isset($para2) && isset($para3))
	{
		$params = array('para1' => $para1, 'para2' => $para2, 'para3' => $para3);
	}
if (isset($para1) && isset($para2) && isset($para3) && isset($para4))
	{
		$params = array('para1' => $para1, 'para2' => $para2, 'para3' => $para3, 'para4' => $para4);
	}

function getOrders($params){
    
    $sql = "SELECT o.id_order, 
	o.date_add, 
	CONCAT_WS(' ', c.firstname, c.lastname) as fullname, 
	(SELECT count(*) FROM "._DB_PREFIX_."order_detail od WHERE od.id_order = o.id_order) as qtdItems,
	(SELECT oh.id_order_state  FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order = o.id_order order by oh.id_order_history desc limit 1) as id_status
        FROM "._DB_PREFIX_."orders o, "._DB_PREFIX_."customer c 
	where o.id_customer = c.id_customer ";
    
    if (isset($params['para3'])){
        $sql .= "and o.id_order = '" . $params['para3'] . "' ";
    }else{
        $sql .= "and o.date_add BETWEEN '" . $params['para1'] . " 00:00:00' and '" . $params['para2'] . " 23:59:59' ";
        if (isset($params['para4'])){
            $sql .= "and (SELECT oh.id_order_state  FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order = o.id_order order by oh.id_order_history desc limit 1) = '" . $params['para4'] . "' ";
        }
    }
    
    $sql .= "order by o.id_order desc";
//echo $sql;
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
}

$listOrders = getOrders($params);

$pedidos = array();

foreach( $listOrders as $key => $value ){
    $pedidos[$key] = array(
        "xOrderID" => $value['id_order'],
        "xProcDate" => $value['date_add'],
        "xCustName" => $value['fullname'],
        "xQtyItems" => $value['qtdItems'],
        "xOrderStatusID" => $value['id_status']
        );
}

$outPedidos = $_GET['callback'] . '(' . Tools::jsonEncode($pedidos) . ')';

$smarty->assign('pedidos', $outPedidos);

$smarty->display(dirname(__FILE__).'/listOrders.tpl');

?>
