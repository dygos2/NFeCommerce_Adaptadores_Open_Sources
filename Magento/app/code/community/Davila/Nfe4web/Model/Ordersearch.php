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
 * Essa classe traz todos os pedidos de acordo com a busca
 **/
class Davila_Nfe4web_Model_Ordersearch extends Mage_Core_Model_Abstract {

	protected function _construct() {
		$this->_init('nfe4web/ordersearch');
	}

	public function getOrders($arrParams = array()) {
		if(!is_array($arrParams) || count($arrParams) == 0)
			return;

        $from_date="";
        $to_date="";
        $order = 0;
        $status_id = "";


        //para1
        if(isset($arrParams['para1'])){
            $from_date = $arrParams['para1'];
        }

        //para2
        if(isset($arrParams['para2'])){
            $to_date   = $arrParams['para2'];
        }

        //para3
        if(isset($arrParams['para3'])) {
            $order     = $arrParams['para3'];
        }
        //para4
        if(isset($arrParams['para4'])){
            $status_id = $arrParams['para4'];
        }


        $arrStatus = Mage::getModel('nfe4web/status')->getAllStatusCode();
		$orders    = Mage::getModel('sales/order')->getCollection();
		$helper    = Mage::helper('nfe4web/data');

		if(strlen($order) != 0) {
			$orders->addAttributeToFilter('increment_id', array('eq' => $order));
		}

		if(strlen($from_date) != 0) {
			$orders->addAttributeToFilter('created_at', array('date' => true, 'from' => $from_date));
		}

		if(strlen($to_date) != 0) {
			//adicionado as horas para incluir as pesquisas do dia
			$orders->addAttributeToFilter('created_at', array('to' => $to_date.' 23:59:59'));
		}

		if((int) $status_id > 0 && $status_id <= count($arrStatus)) {
			$orders->addAttributeToFilter('status', $arrStatus[$status_id]);
		}
		
		//ordernado decrescente por data
		$orders->setOrder('created_at', 'DESC');
		$orders     = $orders->load();
		
		$api2object = Mage::getModel('nfe4web/objects_api2result');
		$api2result = $helper->jsonConvert($api2object->fillObject($orders));

		return $api2result;

	}
}