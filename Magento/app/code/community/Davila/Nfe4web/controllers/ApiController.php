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

class Davila_Nfe4web_ApiController extends Mage_Core_Controller_Front_Action {
	
	private $flag_is_secure_to_render = false;
	private $callback;

	public function preDispatch() {
		$token        = $this->getRequest()->getParam('token');
		$token_config = Mage::getStoreConfig('nfe4web_config_general/api/store_key');
		
		if($token == $token_config) {
			$this->flag_is_secure_to_render = true;
			$this->callback = $this->getRequest()->getParam('callback');
		}
	}

	public function statusAction() {
		if($this->flag_is_secure_to_render){
			echo $this->callback.Mage::getModel('nfe4web/status')->getAllStatusJSON();
		}
	}

	public function ordersearchAction() {
		if(!$this->flag_is_secure_to_render){
			return ;
		}

		$orderSearch = Mage::getModel('nfe4web/ordersearch');
		echo $this->callback.$orderSearch->getOrders($this->getRequest()->getParams());
	}

	public function nfeAction() {
		if(!$this->flag_is_secure_to_render){
			return ;
		}

		$increment_id = $this->getRequest()->getParam('para1');
		
		if(!$increment_id){
			return ;
		}
		$invoice = Mage::getModel('nfe4web/invoice')->getInvoice($increment_id);

		echo $this->callback.$invoice;
	}
}