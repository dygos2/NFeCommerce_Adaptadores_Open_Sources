<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                                                 *
 * Módulo para gerar a Nota Fiscal Eletrônica                      P
 * Versão 2.1 / Compativel com o Prestashop 1.4.X e 1.5.X          R
 *                                                                 E
 * NFe4Web - http://nfe4web.com.br                                 S
 * comercial@nfe4web.com.br                                        T
 *                                                                 A
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  B
 * sac@prestabr.com.br                                             R
 *                                                                 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
//@ini_set('display_errors', 'on');
// Avoid direct access to the file
if (!defined('_PS_VERSION_'))
  exit;
class nfe4web extends Module 
{
    
    private $_html = '';
    private $_postErrors = array();
    private $_moduleName = 'nfe4web';
    
    function __construct() {
        $this->name = 'nfe4web';
        $this->tab = 'administration';
        $this->author = 'PrestaBR';
        $this->version = '2.2.1';
		//$this->ps_versions_compliancy = array('min' => '1.4.0'); 
        
        parent::__construct ();
        $this->displayName = $this->l('NFe4Web');
        $this->description = $this->l('Automatic issuing of Tax Invoice');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall nfe4web module and remove the database involved?');
        
        if (self::isInstalled($this->name))
        {
            $warning = array();
            if (!Configuration::get('NFE4WEB_CNPJ'))
                $warning[] .= $this->l('Configure Company\'s CNPJ').' ';
            if (!Configuration::get('NFE4WEB_TOKEN'))
                $warning[] .= $this->l('Configure the TOKEN informed by NFe4Web').' ';
            
            if (!Configuration::get('NFE4WEB_EMAIL'))
                $warning[] .= $this->l('Configure the e-mail registered in NFe4Web').' ';
            
            if (count($warning))
                $this->warning .= implode(' , ',$warning).$this->l('You must configure the module to work properly').' ';
        }
        //displayFormInformations
    }
    
    public function install()
    {   
        if (!parent::install() OR
			!$this->registerHook("orderDetailDisplayed") OR
			!$this->addColumnsProduct() OR
			!$this->installIbgeTable() OR
			!$this->installModuleTab() OR
		    !Configuration::updateValue('NFE4WEB_ENVIRONMENT', '2') OR
		    !Configuration::updateValue('NFE4WEB_CNPJ', '') OR
			!Configuration::updateValue('NFE4WEB_TOKEN', '') OR
			!Configuration::updateValue('NFE4WEB_EMAIL', '') OR
			!Configuration::updateValue('NFE4WEB_STRTOKEN', $this->random_gen(20))
			)
			return false;
		return true;
    }
    
    public function getLanguages()
    {
        $languages = Language::getLanguages(true);
        foreach($languages as $key => $lang)
        {
            $result[$lang['id_lang']] = 'NFe4Web';
        }
        return $result;
    }
    
    public function installModuleTab()
    {
		if (!$idTab = Tab::getIdFromClassName('AdminNfe4webModule')) {
			$tab = new Tab();
			$tab->name = $this->getLanguages();
			$tab->class_name = 'AdminNfe4webModule';
			$tab->module = $this->name;
			if ((_PS_VERSION_ < '1.5') && (_PS_VERSION_ > '1.4'))
			{
				$tab->id_parent = '3';
			}else{
				$tab->id_parent = '10';
			}
			if(!$tab->save()){
				return false;
			}
			return true;
		}
    }
    // Add new columns in product table
    private function addColumnsProduct()
	{
        $column = array('ncm', 'subst');
        $query = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = "'._DB_PREFIX_.'product" AND COLUMN_NAME = "ncm"');
        
        if (!$query OR $query == ''){
			if(!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `ncm` VARCHAR( 8 ) NULL;') OR
			   !Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` ADD `subst` tinyint(1) DEFAULT 0;'))
				return false;
			return true;
		}else{
			return true;
		}
    }
    
	private function installIbgeTable()
	{
		$content = file_get_contents(dirname(__FILE__).'/tbibge.sql');
		// Create IBGE table
		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tbibge` (
				`estado` varchar(150) COLLATE latin1_general_ci NOT NULL, 
				`municipio` varchar(150) COLLATE latin1_general_ci NOT NULL, 
				`cod_ibge` int(8) NOT NULL, 
				`uf` varchar(45) COLLATE latin1_general_ci DEFAULT NULL, 
				PRIMARY KEY (`cod_ibge`)
				) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
			') OR
		// Insert data in IBGE table
        !Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."tbibge` (`estado`, `municipio`, `cod_ibge`, `uf`) VALUES ".$content.""))
		    return false;
	    return true;
	}
    public function uninstall() 
    {
		if (!parent::uninstall() OR
			!$this->uninstallDb() OR
			!$this->uninstallModuleTab() OR
			//!$this->removeColumnsProduct() OR
		    !Configuration::updateValue('NFE4WEB_CNPJ', '') OR
			!Configuration::updateValue('NFE4WEB_TOKEN', '') OR
			!Configuration::updateValue('NFE4WEB_EMAIL', '') OR
			!Configuration::updateValue('NFE4WEB_ENVIRONMENT', '') OR
			!Configuration::updateValue('NFE4WEB_STRTOKEN', '')
		)
		return false;
	return true;
	}
	private function uninstallModuleTab()
	{
		$idTab = Tab::getIdFromClassName('AdminNfe4webModule');
		if($idTab!=0){
			$tab=new Tab($idTab);
			$tab->delete();
		return true;
		}
	return false;
    }
    
	private function uninstallDb()
	{
		if (!Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tbibge`;'))
			return false;
		return true;
	}
	
    // Remove Nfe columns from product table
    private function removeColumnsProduct(){
        if(!Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `ncm`;') OR
           !Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'product` DROP `subst`;'))
	       return false;
       return true;
    }
    
    /*
    ** Form Config Methods
    **
    */
    public function getContent()
    {
        $this->_html.= '
		<div id="top-info" class="clearfix">
			<a href="http://www.nfe4web.com.br/" title="nfe4web" target="_blank" style="float:left; margin-right:30px;"><img src="../modules/nfe4web/images/nfe4web.png" alt="Nfe4Web" /></a>
			<a class="button" href="http://www.nfe4web.com.br/contrate_1.asp?prod=eco2" title="'.$this->l('Sign up at Nfe4Web').'" target="_blank" style="float:left; margin:13px 0 0 30px; background:#E3ECF0; padding:5px 15px; font-size:16px;">'.$this->l('Choose a Plan!').'</a>
			<a href="http://prestabr.com.br/" title="PrestaBR" target="_blank" style="float:right; margin-left:15px;"><img src="../modules/nfe4web/images/prestabr.png" alt="PrestaBR" /></a>
		</div><br />';
        $this->_html.= '
		<div id="nfe-info" class="clearfix" style="background:#E3ECF0; padding:10px; position:relative;">
			<div id="nfe-instrucao" class="clearfix" style="background:#fff; padding:10px; min-height:320px;">
				<div id="nfe-passos" style="float:left; clear:left; max-width:46%">
					<h2 style="margin-top:0; color:darkred;">'.$this->l('Follow these steps to optimize invoice submission in your store!').'</h2>
					<ul style="list-style:none; padding-left:10px; margin-bottom:20px;">
						<li style="margin:5px 10px; font-size:14px;">1 - <b><a href="http://www.nfe4web.com.br/contrate_1.asp?prod=eco2" title="'.$this->l('Sign up').'" target="_blank">'.$this->l('Sign up').'</a></b> '.$this->l('in one of our plans;').'</li>
						<li style="margin:5px 10px; font-size:14px;">2 - '.$this->l('Configure your company\'s data;').'</li>
						<li style="margin:5px 10px; font-size:14px;">3 - '.$this->l('Configure tax rules (recommended to ask your accountant\'s support);').'</li>
						<li style="margin:5px 10px; font-size:14px;">4 - '.$this->l('Sign up your company at your State\'s NF-e project (if it\'s not registered yet);').'</li>
						<li style="margin:5px 10px; font-size:14px;">5 - '.$this->l('Configure your Digital certificate (A1 type);').'</li>
						<li style="margin:5px 10px; font-size:14px;">6 - '.$this->l('Configure this module\'s API\'s and Token at').' <b>'.$this->l('Nfe4Web').'</b> '.$this->l('and fill in \'NCM\' and \'Tax Substitution\' fields in the Catalog (Product Edition);').'</li>
						<li style="margin:5px 10px; font-size:14px;">7 - '.$this->l('Issue test invoices (test environment) and verify all your data;').'</li>
						<li style="margin:5px 10px; font-size:14px;">8 - '.$this->l('After double check everything, change your environment for').' <b>'.$this->l('"Production"').'</b> '.$this->l('at Nfe4Web and start issuing your invoices in seconds!').'</li>
					</ul>
					<p align="center"><a class="fancy button" href="../modules/nfe4web/views/instrucoes.html" title="'.$this->l('View Complete Instructions').'">'.$this->l('Click here to read the Complete Instructions (pt-BR)').'</a> </p>
					'. (_PS_VERSION_ < '1.5' ? '<link rel="stylesheet" href="../css/jquery.fancybox-1.3.4.css" type="text/css" media="screen" /><script type="text/javascript" src="../js/jquery/jquery.fancybox-1.3.4.js"></script>' : '').'
					<script type="text/javascript">
						$(".fancy").fancybox({
							"height"            : 550,
							"width"				: 650,
							"centerOnScroll"	: true,
							"type"              : "iframe"
						});
					</script>
				</div>
				<div id="video" style="background:#E3ECF0; padding:10px; margin-left:20px; position:absolute; right:20px; top:20px; max-width:50%;">
					<object width="450" height="300">
						<param name="movie" value="'.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').'www.youtube.com/v/7gSxACPkJ5I?version=3&amp;hl=pt_BR&amp;rel=0"></param>
						<param name="allowFullScreen" value="true"></param>
						<param name="allowscriptaccess" value="always"></param>
						<embed src="'.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').'www.youtube.com/v/7gSxACPkJ5I?version=3&amp;hl=pt_BR&amp;rel=0" type="application/x-shockwave-flash" width="450" height="300" allowscriptaccess="always" allowfullscreen="true"></embed>
					</object>
				</div>
			</div>
		</div><br />'; 
        $this->_html .= '
		<form>
		<fieldset class="verificar">
			<legend>
				<img src="../img/admin/information.png" />'.$this->l('Installation Instructions').'
			</legend>
			<div>';
			/* 1.4.X Override Check */
			if ((_PS_VERSION_ < '1.5') && (_PS_VERSION_ > '1.4'))
			{
				$modified_file = @file(dirname(__FILE__).'/override/classes/Product.php');
				$server_file = @file(dirname(__FILE__).'/../../override/classes/Product.php');
				if (sizeof($server_file) <= 1 || !$this->overrideCheck($modified_file, $server_file)){
					$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('Copy').'&nbsp; <b>/modules/nfe4web/override/classes/Product.php</b> &nbsp;'.$this->l('to the folder').'&nbsp; <b>/override/classes</b>
					<br />
					'.$this->l('If a file with the exact name already exists, you need to merge our file and yours.').'</div>';
				}
				else
				{
					$this->_html .= '
					<p class="conf"><b>Product.php '.$this->l(' override is installed correctly.').'</b></p>';
					$check1 = true;
				}	
				
				if (!$this->adminTabCheck())
				{	
				$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('Copy').'&nbsp; <b>/modules/nfe4web/override/tabs/AdminProducts.php</b> &nbsp;'.$this->l('to the folder').'&nbsp; <b>'.PS_ADMIN_DIR.'/tabs/AdminProducts.php</b></div>';
				}else{	$this->_html .= '
					<p class="conf"><b>AdminProducts.php '.$this->l(' modified admin tab is installed correctly.').'</b></p>';
					$check2 = true;
					$check3 = true;
				}	
			}
			
			/* 1.5.X Override Check */
			if (_PS_VERSION_ > '1.5')
			{
				$modified_file1 = @file(dirname(__FILE__).'/override/classes/Product.php');
				$server_file1 = @file(dirname(__FILE__).'/../../override/classes/Product.php');
				if (sizeof($server_file1) <= 1 || !$this->overrideCheck($modified_file1, $server_file1)){
					$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('Copy').'&nbsp; <b>/modules/nfe4web/override/classes/Product.php</b> &nbsp;'.$this->l('to the folder').'&nbsp; <b>/override/classes/</b>
					<br />
					'.$this->l('If a file with the exact name already exists, you need to merge our file and yours.').'</div>';
				}
				else
				{
					$this->_html .= '
					<p class="conf"><b>Product.php '.$this->l(' override is installed correctly.').'</b></p>';
					$check1 = true;
				}	
				$modified_file2 = @file(dirname(__FILE__).'/override/controllers/admin/AdminProductsController.php');
				$server_file2 = @file(dirname(__FILE__).'/../../override/controllers/admin/AdminProductsController.php');
				if (sizeof($server_file2) <= 1 || !$this->overrideCheck($modified_file2, $server_file2)){
					$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('Copy').'&nbsp; <b>/modules/nfe4web/override/controllers/admin/AdminProductsController.php</b> &nbsp;'.$this->l('to the folder').'&nbsp; <b>/override/controllers/admin/</b>
					<br />
					'.$this->l('If a file with the exact name already exists, you need to merge our file and yours.').'</div>';
				}
				else
				{
					$this->_html .= '
					<p class="conf"><b>AdminProductsController.php '.$this->l(' override is installed correctly.').'</b></p>';
					$check2 = true;
				}	
				if (!$this->adminTabCheck())
				{	
				$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('Copy').'&nbsp; <b>/modules/nfe4web/override/controllers/admin/templates/controllers/products/informations.tpl</b> &nbsp;'.$this->l('to the folder').'&nbsp; <b>'._PS_ADMIN_DIR_.'/themes/default/template/controllers/products/</b></div>';
				}else{	$this->_html .= '
					<p class="conf"><b>AdminProducts.php '.$this->l(' modified admin tab is installed correctly.').'</b></p>';
					$check3 = true;
				}	
			}
			
			if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = "'._DB_PREFIX_.'customer" AND COLUMN_NAME = "cpf"') AND
				!Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = "'._DB_PREFIX_.'customer" AND COLUMN_NAME = "cpf_cnpj"') AND
				!Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = "'._DB_PREFIX_.'cpfmodule_data" AND COLUMN_NAME = "doc"'))
			{
				$this->_html .= '
					<div class="warning" style="width:800px; margin:0 auto;">'.$this->l('You need customer\'s CPF in your system. Please install one of the modules that create CPF in your customer\'s register form.').'</div>';
				$check4 = false;
			}else{
				$this->_html .= '
					<p class="conf"><b>'.$this->l('CPF field is installed in your system.').'</b></p>';
				$check4 = true;
			}
			if ($check1 == true && $check2 == true && $check3 == true && $check4 == true){
				$this->_html .= '
					<script type="text/javascript">
						window.onload = function(){
						setTimeout(function() {
							$(".verificar").hide(800, "swing");
							}
							,5000
							);
						}
					</script>';
			}
		$this->_html .= '
			</div>
		</fieldset>
		</form><br>';
            if (!empty($_POST) AND Tools::isSubmit('submitSave'))
            {
                    $this->_postValidation();
                    if (!sizeof($this->_postErrors))
                            $this->_postProcess();
                    else
                            foreach ($this->_postErrors AS $err)
                                    $this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
            }
            
            $this->_displayForm();
            return $this->_html;
    }
	protected function overrideCheck($mod, $srv)
	{
		$class_found = false;
		foreach ($mod as $row)
		{
			if (!$class_found)
			{
				if (substr($row,0,5) == 'class')
				{
					$class_found = true;
					//print "Class found<br />";
				}
				continue;
			}
			else
			{
				$row = trim($row);
				$row_found = false;
				foreach ($srv as $key => $orow)
				{
					if ($row == trim($orow))
					{
						$srv = array_slice($srv, $key);
						$row_found = true;
						//print "Found $row<br />";
						break;
					}
				}
				if (!$row_found)
				{
					//print "Not Found $row<br />";
					return false;
				}
			}
		}
		return true;
	}
	
    /*
     * Company CNPJ 
     * Token - generated by Nfe4web
     * E-mail - registered at Nfe4web
     */
    private function _displayForm()
    {
        $this->_html .= '<fieldset>
		<legend><img src="'.$this->_path.'images/logo.gif" alt="" style="vertical-align:middle" /> '.$this->l('NFe4Web - Status of the Module').'</legend>';
        
        $alert = array();
        if (!Configuration::get('NFE4WEB_CNPJ') || Configuration::get('NFE4WEB_CNPJ') == '')
        {
            $alert[] = $this->l('Inform the Company\'s CNPJ');
        }
        if (!Configuration::get('NFE4WEB_TOKEN') || Configuration::get('NFE4WEB_TOKEN') == '')
        {
            $alert[] = $this->l('Inform the TOKEN generated by NFe4Web');
        }
        if (!Configuration::get('NFE4WEB_EMAIL') || Configuration::get('NFE4WEB_EMAIL') == '')
        {
            $alert[] = $this->l('Inform the E-MAIL registered at NFe4Web');
        }
        
        if (!count($alert))
		{
            $this->_html .= '<img src="'._PS_IMG_.'admin/module_install.png" /><span style="color:#00be00"><strong>'.$this->l('NFe4Web is set up and online!').'</strong></span>';
        }else{
            $this->_html .= '<strong>'.$this->l('NFe4Web is not configured yet').':</strong><br />';
            
            for ($i=0; $i < count($alert); $i++)
	    {
                $indice = $i + 1;
                $this->_html .= '<br />'.'<img src="'._PS_IMG_.'admin/warning.gif" /> <span style="color:#ff0000;">'. $indice . ') '.$this->l($alert[$i]).'<span>';
            }
        }
        
        $api1 = Tools::getHttpHost(true) . _MODULE_DIR_ . $this->name . '/status.php';
        $api2 = Tools::getHttpHost(true) . _MODULE_DIR_ . $this->name . '/listOrders.php';
        $api3 = Tools::getHttpHost(true) . _MODULE_DIR_ . $this->name . '/orderDetails.php';
        $this->_html .= '
			</fieldset>
			<br />
			<script type="text/javascript" src="../modules/nfe4web/js/mascara.js"></script>
			<script type="text/javascript" src="../modules/nfe4web/js/validar.js"></script>
            <form action="index.php?tab='.Tools::getValue('tab').'&configure='.Tools::getValue('configure').'&token='.Tools::getValue('token').'&tab_module='.Tools::getValue('tab_module').'&module_name='.Tools::getValue('module_name').'&id_tab=1&section=general" method="post" class="form" id="configForm">
				<fieldset>
					<legend><img src="../img/admin/prefs.gif" />'.$this->l('General Settings').'</legend>
					<br />
					<h3>'.$this->l('Company Data').'</h3>
					<table class="table" border="0" width="900" cellpadding="0" cellspacing="2" id="form">
						<tr>
							<th style="width:250px;">'.$this->l('Environment').' : </th> 
							<td style="border:none;">
								<input type="radio" name="environment" value="1" '.((Tools::getValue('environment', Configuration::get('NFE4WEB_ENVIRONMENT')) == '1') ? 'checked="checked"':'').' /> '.$this->l('Production').'
								<input type="radio" name="environment" value="2" '.((Tools::getValue('environment', Configuration::get('NFE4WEB_ENVIRONMENT')) == '2')?'checked="checked"':'').' /> '.$this->l('Test').'
							</td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('Company CNPJ').' : </th> <td style="border:none;"><input type="text" size="100" name="cnpjEmpresa" value="'.$this->cleanCpf(Tools::getValue('cnpjEmpresa', Configuration::get('NFE4WEB_CNPJ'))).'" onkeypress="mascara(this,cnpjmask)" maxlength=18 onblur="ValidarCNPJ(cnpjEmpresa)" /></td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('TOKEN generated by NFe4Web').' : </th> <td style="border:none;"><input type="text" size="100" name="tokenEmpresa" value="'.Tools::getValue('tokenEmpresa', Configuration::get('NFE4WEB_TOKEN')).'" /></td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('E-mail registered at NFe4Web').' : </th><td style="border:none;"><input type="text" size="100" name="emailEmpresa" value="'.Tools::getValue('emailEmpresa', Configuration::get('NFE4WEB_EMAIL')).'" /></td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('Store Token').' : </th><td style="border:none;"><input type="text" size="100" readonly="readonly" value="'.Configuration::get('NFE4WEB_STRTOKEN').'" onClick="this.select();" /></td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('Update Store Token?').' : </th><td style="border:none;"><input type="checkbox" name="updateToken" value="1" style="cursor:pointer" /> </td>
						</tr>
					</table>
					<br />
 
					<h3>'.$this->l('Links API').'</h3>
					<table class="table" border="0" width="900" cellpadding="0" cellspacing="2" id="form">
						<tr>
							<th style="width:250px;">'.$this->l('Status Requests').' : </th><td style="border:none;"><input type="text" size="100" readonly="readonly" value="'.$api1.'" onClick="this.select();" /></td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('List Requests').' : </th><td style="border:none;"><input type="text" size="100" readonly="readonly" value="'.$api2.'" onClick="this.select();" /> </td>
						</tr> 
						<tr>
							<th style="width:250px;">'.$this->l('Details Request').' : </th><td style="border:none;"><input type="text" size="100" readonly="readonly" value="'.$api3.'" onClick="this.select();" /> </td>
						</tr> 
					</table>
					<br />
					<center><input class="button" name="submitSave" type="submit" style="cursor:pointer" value="'.$this->l('Save').'"></center>
				</fieldset>
			</form>';
			
        $this->_html .= '
			<br /><p class="warning" align="center" style="margin:0 auto; width:820px;">'.$this->l('If you have any difficulties or doubts integrating this module, please send us an e-mail at').' <b><a href="mailto:sac@prestabr.com.br">sac@prestabr.com.br</a></b></p> <br />
		</div>';
    }
    
    private function _postValidation()
    {
        if ( !$this->cnpjValidate(Tools::getValue('cnpjEmpresa')) ){
            $this->_postErrors[]  = $this->l('Invalid CNPJ. Please check!');
        }
        
        if ( !filter_var(Tools::getValue('emailEmpresa'), FILTER_VALIDATE_EMAIL) ){
            $this->_postErrors[]  = $this->l('Invalid E-mail. Please check!');
        }
    }
    
    private function _postProcess()
    {
        // Saving new configuration
        $updateToken = ( Tools::getValue('updateToken') ? Configuration::updateValue('NFE4WEB_STRTOKEN', $this->random_gen(20)) : true );
        
        if ( Configuration::updateValue('NFE4WEB_CNPJ', $this->cleanCpf(Tools::getValue('cnpjEmpresa'))) && 
                Configuration::updateValue('NFE4WEB_TOKEN', Tools::getValue('tokenEmpresa')) && 
				Configuration::updateValue('NFE4WEB_ENVIRONMENT', Tools::getValue('environment')) &&
                Configuration::updateValue('NFE4WEB_EMAIL', Tools::getValue('emailEmpresa')) && $updateToken
                )
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        else
                $this->_html .= $this->displayErrors($this->l('Settings failed'));
    }
    
    private function cleanCpf($string){
        $vowels = array(".", "-", "/", " ");
        $cpf = str_replace($vowels, "", $string);
        
        return $cpf;
    }
 
    private function cnpjValidate($str)
    {
        $nulos = array("12345678909123","11111111111111","111111111111111","22222222222222","222222222222222","33333333333333","333333333333333","44444444444444","444444444444444","55555555555555", "555555555555555","66666666666666", "666666666666666","77777777777777", "777777777777777",
            "88888888888888", "888888888888888", "99999999999999","999999999999999", "00000000000000", "000000000000000");
        if (!preg_match('|^(\d{2,3})\.?(\d{3})\.?(\d{3})\/?(\d{4})\-?(\d{2})$|', $str, $matches))
        {
            return false;
        }
        if (in_array($str, $nulos))
        {
             return false;
        }
        array_shift($matches);

        $str = implode('', $matches);
        if (strlen($str) > 14)
        $str = substr($str, 1);
        $sum1 = 0;
        $sum2 = 0;
        $sum3 = 0;
        $calc1 = 5;
        $calc2 = 6;
        for ($i=0; $i <= 12; $i++)
        {
            $calc1 = $calc1 < 2 ? 9 : $calc1;
            $calc2 = $calc2 < 2 ? 9 : $calc2;
            if ($i <= 11)
            $sum1 += $str[$i] * $calc1;
            $sum2 += $str[$i] * $calc2;
            $sum3 += $str[$i];
            $calc1--;
            $calc2--;
        }
        $sum1 %= 11;
        $sum2 %= 11;
        $result = ($sum3 && $str[12] == ($sum1 < 2 ? 0 : 11 - $sum1) && $str[13] == ($sum2 < 2 ? 0 : 11 - $sum2)) ? true : false;
        if(!$result)
        {
            return false;
        }
        return true;
    }
    
    private function random_gen($length)
    {
      $random= "";
      srand((double)microtime()*1000000);
      $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $char_list .= "abcdefghijklmnopqrstuvwxyz";
      $char_list .= "1234567890";
      // Add the special characters to $char_list if needed
      for($i = 0; $i < $length; $i++)  
      {    
         $random .= substr($char_list,(rand()%(strlen($char_list))), 1);  
      }  
      return $random;
    }
    
    // Generate NFe button in Store Order Details Page
	public function hookOrderDetailDisplayed($params)
    {
		if(!$this->active)
			return;
		global $smarty, $order;
		$nfe_cnpj=Configuration::get('NFE4WEB_CNPJ');
		$nfe_token=Configuration::get('NFE4WEB_TOKEN');
		$nfe_ambiente=Configuration::get('NFE4WEB_ENVIRONMENT');
		$nfe_order = Tools::getValue("id_order");
		$nfe_strtoken = Configuration::get('NFE4WEB_STRTOKEN');
		
		$url="http://amazon-nfe4web.elasticbeanstalk.com/nfe4web/retornos_banco/busca_peds.php?cnpj=".$nfe_cnpj."&pedidos=".$nfe_order."&tp_amb=".$nfe_ambiente."&token=".$nfe_token."&callback";
		$ch=curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,1);
		curl_setopt($ch,CURLOPT_HEADER,false);
		$r=curl_exec($ch);
		curl_close($ch);
	//	echo $r.'<br /><br />';
		$rep = array("([", "]);");
		$json = str_replace($rep,"",$r);
		//$var = json_decode($json);
		$var = Tools::jsonDecode($json);
		
		$url = $var->url_danfe;
		
		$vars = $nfe_cnpj.' - '.$nfe_order.' - '.$nfe_strtoken.' - '.$nfe_token.' - '.$nfe_ambiente;
		
		$smarty->assign(array(
			'nfe_link' => $url,
			'array' => $r,
			'vars' => $vars
			));
		return $this->display(__file__, 'views/templates/front/botao_visualiza_nfe.tpl');
    }
	
	private function adminTabCheck() 
	{
		
		if (_PS_VERSION_ > '1.5')
		{
			$file = file_get_contents(_PS_ADMIN_DIR_.'/themes/default/template/controllers/products/informations.tpl');
		}else{
			$file = file_get_contents(PS_ADMIN_DIR.'/tabs/AdminProducts.php'); 
		}
		$ncm = 'NCM Code';
		$subst = 'Tax Substitution';
		if (preg_match('/.*'.$ncm.'.*/',$file) && preg_match('/.*'.$subst.'.*/',$file)) {
			return true;	
		} else { 
			return false;	
		} 	
	}
}
	
?>