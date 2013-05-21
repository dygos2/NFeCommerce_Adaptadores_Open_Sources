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
 * Essa classe compoe o objeto de Produto para a Api 3
 **/
class Davila_Nfe4web_Model_Objects_Product extends Mage_Core_Model_Abstract {

	private $fields = array(
						'cProd'  => array('required' => 1, 'min' => 1, 'max' => 60, 'type' => 'string', 'entity_type' => 'item', 'field' => 'sku', 'pad' => false),
						'xProd'  => array('required' => 1, 'min' => 1, 'max' => 120, 'type' => 'string', 'entity_type' => 'item', 'field' => 'name', 'pad' => false),
						'Orig'   => array('required' => 1, 'min' => 1, 'max' => 1, 'type' => 'numeric', 'entity_type' => 'product', 'field' => 'orig', 'pad' => false),
						'NCM'    => array('required' => 1, 'min' => 2, 'max' => 8, 'type' => 'string', 'entity_type' => 'product', 'field' => 'ncm', 'pad' => false),
						'uCom'   => array('required' => 1, 'min' => 1, 'max' => 6, 'type' => 'string', 'entity_type' => 'product', 'field' => 'ucom', 'pad' => false),
						'qCom'   => array('required' => 1, 'min' => 11, 'max' => 4, 'type' => 'decimal', 'entity_type' => 'item', 'field' => 'qty_ordered', 'pad' => false),
						'subst'  => array('required' => 1, 'min' => 1, 'max' => 1, 'type' => 'numeric', 'entity_type' => 'product', 'field' => 'subst', 'pad' => false),
						'vUnCom' => array('required' => 1, 'min' => 1, 'max' => 2, 'type' => 'decimal', 'entity_type' => 'item', 'field' => 'price', 'pad' => false),
						'vFrete' => array('required' => 0, 'min' => 1, 'max' => 2, 'type' => 'decimal', 'entity_type' => 'order', 'pad' => false),
						'vDesc'  => array('required' => 0, 'min' => 1, 'max' => 2, 'type' => 'decimal', 'entity_type' => 'item', 'field' => 'discount_amount', 'pad' => false)
						);

	protected function _construct() {
		$this->_init('nfe4web/objects_product');
	}

	public function populate($order) {
		$helper      = Mage::helper('nfe4web/data');
		$data        = array();
		$i           = 0;
		$order_items = $order->getAllVisibleItems();


		foreach ($order_items as $item) {
			$product = Mage::getModel('catalog/product')->load($item->getProductId());

			foreach($this->fields as $key => $field) {
				$entity = $field['entity_type'];
				if($key != 'vFrete') {
					$value = $helper::createGet($$entity, $field['field']);

				} else {
					$total_frete = $order->getShippingAmount();
					$total_items = $order->getTotalQtyOrdered();
					$frete_unit  = $total_frete/$total_items;
					
					$value       = $frete_unit * $item->getQtyOrdered();
				}
				$value          = $helper::truncate($value, $field['max'], $field['type'], $field['pad']);
				if($value) {
					$data[$i][$key] = $value;
				}
			}
			$i++;
		}

		return $data;
	}

}