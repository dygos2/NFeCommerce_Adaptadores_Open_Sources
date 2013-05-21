<?php
/*
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  
 * sac@prestabr.com.br
 */
 
include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminNfe4webModule extends AdminTab
{
	private $module = 'nfe4web';

    public function __construct()
    {
        global $cookie;
        
        parent::__construct();
    }

    public function display()
	{
		return $this->displayForm();
	}

    public function displayForm()
    {
        global $cookie;
        
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		//$obj = $this->loadObject(true);
        
		$html = '';
        
        $alert = array();
        if (!Configuration::get('NFE4WEB_CNPJ') || Configuration::get('NFE4WEB_CNPJ') == '')
        {
            $alert[] = $this->l('Inform the company CNPJ');
        }

        if (!Configuration::get('NFE4WEB_TOKEN') || Configuration::get('NFE4WEB_TOKEN') == '')
        {
            $alert[] = $this->l('Inform the TOKEN generated by NFe4Web');
        }

        if (!Configuration::get('NFE4WEB_EMAIL') || Configuration::get('NFE4WEB_EMAIL') == '')
        {
            $alert[] = $this->l('Inform the e-mail registered at NFe4Web');
        }

        if (!count($alert)){

            $url = 'http://amazon-nfe4web.elasticbeanstalk.com/nfe4web/integracoes/emissor.php';
            $url .= '?tk2=' . Configuration::get('NFE4WEB_CNPJ');
            $url .= '&token=' . Configuration::get('NFE4WEB_TOKEN');
            $url .= '&email=' . Configuration::get('NFE4WEB_EMAIL');
            
            $html .= '<iframe width="100%" src="'.$url.'" style="overflow:auto; min-height:80%;"></iframe> ';
        
            $html .= '<br /><br /><p class="warning" align="center" style="margin:0 auto; width:820px;">'.$this->l('If you have any difficulties or doubts integrating this module, please send us an e-mail at').' <b><a href="mailto:sac@prestabr.com.br">sac@prestabr.com.br</a></b></p> <br />';
        }else{
            $html .= '<img src="'._PS_IMG_.'admin/warn2.png" /><strong>'.$this->l('NFe4Web is not configured yet').':</strong>';
            
            for ($i=0; $i < count($alert); $i++){
                $indice = $i + 1;
                $html .= '<br />'.'<img src="'._PS_IMG_.'admin/warn2.png" /> '. $indice . ') '.$this->l($alert[$i]);
            }
            $token = Tools::getAdminToken('AdminModules'. (int)Tab::getIdFromClassName('AdminModules') .(int)$cookie->id_employee);
            $urlMod = $_SERVER['SCRIPT_NAME'].'?tab=AdminModules&configure=nfe4web&token=' . $token;
            $html .= '<br><br><a href="'.$urlMod.'"><strong>'.$this->l('Click here to configure the module').'</strong></a>';
        }
        
        echo $html;
        
    }
}

?>
