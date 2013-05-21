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

class Davila_Nfe4web_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
	private $entities = array(
							'orig' => array(
								'group'							=> 'NFE4WEB',
								'type'							=> 'int', 
								'label' 						=> 'Origem', 
								'input' 						=> 'select',
								'backend' 						=> 'eav/entity_attribute_backend_array',
								'frontend'						=> '', 
								'default'						=> 0,
								'source'						=> 'nfe4web/fields_origin',
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
								'visible'						=> true,
								'required'						=> true,
								'user_defined'					=> false,
								'searchable'					=> false,
								'filterable'					=> false,
								'comparable'					=> false,
								'visible_on_front'				=> false,
								'visible_in_advanced_search'	=> false,
								'unique'						=> false
								),
							'ncm' => array(
								'group'							=> 'NFE4WEB',
								'type'							=> 'varchar', 
								'label' 						=> 'NCM', 
								'input' 						=> 'text',
								'class'							=> 'validate-length minimum-length-2 maximum-length-8 validate-alphanum',
								'backend' 						=> '',
								'frontend'						=> '',
								'default'						=> '',
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
								'visible'						=> true,
								'required'						=> true,
								'user_defined'					=> false,
								'searchable'					=> false,
								'filterable'					=> false,
								'comparable'					=> false,
								'visible_on_front'				=> false,
								'visible_in_advanced_search'	=> false,
								'unique'						=> false
								),
							'ucom' => array(
								'group'							=> 'NFE4WEB',
								'type'							=> 'varchar', 
								'label' 						=> 'Unidade de Venda', 
								'input' 						=> 'select',
								'backend' 						=> 'eav/entity_attribute_backend_array',
								'frontend'						=> '', 
								'default'						=> 'un',
								'source'						=> 'nfe4web/fields_unity',
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
								'visible'						=> true,
								'required'						=> true,
								'user_defined'					=> false,
								'searchable'					=> false,
								'filterable'					=> false,
								'comparable'					=> false,
								'visible_on_front'				=> false,
								'visible_in_advanced_search'	=> false,
								'unique'						=> false
								),
							'subst' => array(
								'group'							=> 'NFE4WEB',
								'type'							=> 'int', 
								'label' 						=> 'Substituição Tributária', 
								'input' 						=> 'select',
								'backend' 						=> 'eav/entity_attribute_backend_array',
								'frontend'						=> '', 
								'default'						=> 0,
								'source'						=> 'nfe4web/fields_subst',
								'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
								'visible'						=> true,
								'required'						=> true,
								'user_defined'					=> false,
								'searchable'					=> false,
								'filterable'					=> false,
								'comparable'					=> false,
								'visible_on_front'				=> false,
								'visible_in_advanced_search'	=> false,
								'unique'						=> false
								),
							);


	public function getDefaultEntities() {
		foreach($this->entities as $key => $data) {
        		parent::addAttribute('catalog_product', $key, $data);
        }
	}
}