<?php
/*
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  
 * sac@prestabr.com.br
 */
 
global $smarty;
include('../../config/config.inc.php');
$token = $_GET['token'];
if ( $token != Configuration::get('NFE4WEB_STRTOKEN') || !isset($token) ) 
	exit;
function getOrderDta($id){
    $sql = "SELECT CONCAT_WS(' ', c.firstname, c.lastname) as fullname, 
        a.address1, a.address2, a.company, a.other, a.postcode, a.city, a.phone, a.phone_mobile, 
        c.email, o.total_shipping, o.total_discounts, a.id_state, c.id_customer 
        FROM "._DB_PREFIX_."orders o, 
             "._DB_PREFIX_."customer c, 
             "._DB_PREFIX_."address a
        WHERE o.id_customer = c.id_customer 
        AND a.id_address = o.id_address_delivery 
        AND o.id_order = '".$id."'";
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
}
function getDetailProduct($id)
{
    $sql = "SELECT od.product_id, od.product_reference, od.product_supplier_reference, od.product_name, od.product_quantity, od.group_reduction, od.product_price, od.product_weight, od.reduction_percent, od.reduction_amount, p.ncm, p.subst 
                FROM `"._DB_PREFIX_."order_detail` od, "._DB_PREFIX_."product p 
                WHERE od.product_id = p.id_product 
                AND od.id_order = '".$id."'";
    
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
}
/*
 * Extrai número do endereço e retorna um array
 */
function extract_numbers($string)
{
    preg_match_all('/([\d]+)/', $string, $match);
    return $match[0];
}
function searchMun($city, $uf){
    $sql = "select cod_ibge from "._DB_PREFIX_."tbibge where `municipio` LIKE '%".$city."%' and `uf` = '".$uf."'";
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
}
function getDocument($idCustomer){
    $sql = "select doc, type, idt from "._DB_PREFIX_."cpfmodule_data where id_customer = '".$idCustomer."'";
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
}
function getIsoCode($id){
    $sql = "select iso_code from "._DB_PREFIX_."state where id_state = '".$id."'";
    return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
}
$id = $_GET['para1'];
$productDetails = getOrderDta($id);
$order = new Order($id);
$customer = new Customer($order->id_customer);
foreach ($productDetails as $key => $value) {
    
	//Seleciona um dos tipos de CPF
    if (isset($customer->cnpj) && $customer->cnpj != '')
		{$searchDoc = $customer->cnpj;}
    elseif (isset($customer->cpf) && $customer->cpf != '')
		{$searchDoc = $customer->cpf;}
	elseif (isset($customer->cpf_cnpj))
		{$searchDoc = $customer->cpf_cnpj;}
	else
		{
			$doc = getDocument($value['id_customer']);
       		$searchDoc = $doc->doc;
		}
    
    $vowels = array(".", "-", "/", " ");
	$cpf = str_replace($vowels, "", $searchDoc);
    $document = (strlen($cpf) > 12 ? 'CNPJ' : 'CPF');

    //Seleciona o endereço com número
	$pos = strpos($value['address1'], ',');
        
    if ($pos === false)
    {
        if (isset($value['numend'])){
			$number = $value['numend'];
		}elseif (isset($value['number'])){
			$number = $value['number'];
		}else{
			$number = '0';
		}
        $address1 = $value['address1'];
    }
    else
    {
        $nb  = explode(",",ltrim($value['address1']));
        $number = $nb[1];
        $address1 = $nb[0];
    }
    $codeState = getIsoCode($value['id_state']);
    $municipio = searchMun($value['city'], $codeState['iso_code']);
    
    $products = getDetailProduct($id);
    
	$telefone = (!empty($value['phone']) ? $value['phone'] : $value['phone_mobile'] );
    $sep = array(".", "-", "(", ")", " ");
	$fone = str_replace($sep, "", $telefone);

    $pDetails = array(
            $document => $cpf,
            'xName' => $value['fullname'],
            'xLgr' => $address1,
            'nro' => (is_numeric($value['company']) ? $value['company'] : ltrim($number)), // busca o último elemento do array
            'xCpl' => (isset($nb[2]) ? ltrim($nb[2]) : $value['other']),
            'xBairro'=> $value['address2'],
            'cMun' => $municipio['cod_ibge'],
            'xMun' => $value['city'],
            'UF' => $codeState['iso_code'],
            'CEP' => str_replace("-", "", $value['postcode']),
            'fone' => $fone,
            'email' => $value['email']
        );
    
    foreach ($products as $k => $v){
		if (isset($v['reduction_percent']) && ($v['reduction_percent'] > '0'))
		{
			$pPrice = ($v['product_price'] - ($v['product_price'] * $v['reduction_percent']/100)); 
		}else{
			$pPrice = ($v['product_price'] - $v['reduction_amount']);
		}
		$precoFinal = (isset($v['group_reduction']) && ($v['group_reduction'] > '0') ? ($pPrice - ($pPrice * $v['group_reduction']/100)) : $pPrice);
		$frete = $value['total_shipping'] / count($products);
		$voucher = $value['total_discounts'] / count($products);
        $itemObject[$k] = array(
                'cProd' => (isset($v['product_reference']) && $v['product_reference'] !== '' ? $v['product_reference'] : $v['product_id']),
                'xProd' => $v['product_name'],
                'NCM' => $v['ncm'], 
                'uCom' => 'unid',
                'qCom' => $v['product_quantity'],
                'subst' => $v['subst'],
                'vUnCom' => $precoFinal,
                'vFrete' => $frete,
				'vDesc' => $voucher 
            );
		$peso[$k] = $v['product_weight'];
    }

    $pDetails['Item_Object'] = $itemObject;
	$pDetails['pesoL'] = array_sum($peso);
    
}

$outDetailsOrder = $_GET['callback'] . '(' . Tools::jsonEncode($pDetails) . ')';
$smarty->assign('orderDetails', $outDetailsOrder);
$smarty->display(dirname(__FILE__).'/orderDetails.tpl');
?>
