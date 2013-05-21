<?php
/**
 * MIT LICENSE 
 * Copyright (c) 2013 Gabriela Davila, http://davila.blog.br
 * http://github.com/gabidavila
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category    Community
 * @package     Davila_Nfe4web
 * @copyright   Copyright (c) 2013 Gabriela Davila (http://davila.blog.br)
 * @license     http://opensource.org/licenses/MIT  MIT License
 */

/**
 * Essa classe compoe o objeto de customer
 **/
class Davila_Nfe4web_Model_Objects_Customer extends Mage_Core_Model_Abstract {

	private $address_type = 'billing';

	private $fields = array(
							'CNPJ'   => array('required' => 1, 'min' => 14,  'max' => 14,    'type' => 'numeric',    'entity_type' => 'customer', 	'custom_field' => 'company_document_id', 	'pad' => false),
							'CPF'	 => array('required' => 1, 'min' => 11,  'max' => 11,    'type' => 'numeric',    'entity_type' => 'customer', 	'custom_field' => 'pf_document_id',  		'pad' => false),
							'xName'  => array('required' => 1, 'min' => 2,   'max' => 60,    'type' => 'string',     'entity_type' => 'customer', 	'field' => array('firstname', 'lastname'), 	'pad' => false),
							'xLgr'   => array('required' => 1, 'min' => 2,   'max' => 60,    'type' => 'string',     'entity_type' => 'address', 	'field' => 'street_1', 						'pad' => false),
							'nro'    => array('required' => 1, 'min' => 1,   'max' => 60,    'type' => 'string',     'entity_type' => 'address', 	'custom_field' => 'number', 				'pad' => false),
							'xCpl'   => array('required' => 0, 'min' => 1,   'max' => 60,    'type' => 'string',     'entity_type' => 'address', 	'custom_field' => 'complement', 			'pad' => false),
							'xBairro'=> array('required' => 1, 'min' => 1,   'max' => 60,    'type' => 'string',     'entity_type' => 'address', 	'custom_field' => 'district', 				'pad' => false),
							'UF'     => array('required' => 1, 'min' => 2,   'max' => 2,     'type' => 'string',     'entity_type' => 'address', 												'pad' => false),
							'xMun'   => array('required' => 1, 'min' => 2,   'max' => 60,    'type' => 'string',     'entity_type' => 'address', 	'field' => 'city', 							'pad' => false),
							'cMun'   => array('required' => 1, 'min' => 7,   'max' => 7,     'type' => 'numeric',    'entity_type' => 'address', 												'pad' => true),
							'CEP'    => array('required' => 0, 'min' => 8,   'max' => 8,     'type' => 'numeric',    'entity_type' => 'address', 	'field' => 'postcode', 						'pad' => true),
							'fone'   => array('required' => 0, 'min' => 6,   'max' => 14,    'type' => 'string',     'entity_type' => 'address', 	'field' => 'telephone', 					'pad' => false),
							'IE'     => array('required' => 0, 'min' => 2,   'max' => 14,    'type' => 'numeric',    'entity_type' => 'customer', 	'custom_field' => 'pj_state_id', 			'pad' => false),
							'email'  => array('required' => 0, 'min' => 1,   'max' => 60,    'type' => 'string',     'entity_type' => 'customer', 	'field' => 'email', 						'pad' => false),

							);

	protected function _construct() {
		$this->_init('nfe4web/objects_customer');
		$this->address_type = Mage::getStoreConfig('nfe4web_config_parametrization/fields/address_type');
	}

	public function populate($order){
		$helper   = Mage::helper('nfe4web/data');
		$address  = $helper::createGet($order, $this->address_type, 'address');
		$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
		$data     = array();
		
		foreach($this->fields as $key => $field){
			$value	   = '';
			$entity_type = $field['entity_type'];
						
			switch($key) {
				case 'xName': 
					$firstname = $helper::createGet($$entity_type, $field['field'][0]);
					$lastname  = $helper::createGet($$entity_type, $field['field'][1]);
					$value = $firstname.' '.$lastname;
					break;

				case 'cMun': 
					$ibge  = Mage::getModel('nfe4web/ibge')->getCollection()
								->addFieldTofilter('municipio', $data['xMun'])
								->addFieldTofilter('uf', $data['UF'])
								->getFirstItem();

					$value = $ibge->getCodIbge();
					break;

				case 'UF': 
					$region_id = $address->getRegionId();
						if(!$region_id) {
							$value = null;
							break;
						}
					$value = Mage::getModel('directory/region')->load($region_id)->getCode();
					break;

				default:
					$fieldname = $field['field'];
					//pega os campos customizados
					if(array_key_exists('custom_field', $field)) {
						$fieldname = Mage::getStoreConfig('nfe4web_config_parametrization/fields/'.$field['custom_field']);
					}
					$value = $helper::createGet($$entity_type, $fieldname);
					break;
			}
			$value = $helper::truncate($value, $field['max'], $field['type'], $field['pad']);

			if(!$value) {
				$value = null;
			}
			$data[$key] = $value;

			if($data['CNPJ'] == $data['CPF']) {
				if(strlen($data['CPF']) <= 11) {
					$data['CNPJ'] = null;
				} else {
					$data['CPF'] = null;
				}
			}

			if(($data['CPF'] == $data['IE']) || ($data['CNPJ'] == $data['IE'])) {
				unset($data['IE']);
			}
		}
		return $data;
	}

}