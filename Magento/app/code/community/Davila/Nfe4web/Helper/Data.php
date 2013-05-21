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
class Davila_Nfe4web_Helper_Data extends Mage_Core_Helper_Abstract {

	/**
	 * Busca todos os campos do Custmer Address
	**/
	public static function getFields($type = 'customer_address') {
		$entityType = Mage::getModel('eav/config')->getEntityType($type);
		$entityTypeId = $entityType->getEntityTypeId();
		$attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($entityTypeId);
		
		return $attributes->getData();
	}

	public static function jsonConvert($arrData) {
		if(is_null($arrData)) {
			$arrData = array();
		}
		return '('.json_encode($arrData).');';
	}

	public static function justNumbers($string) {
		return preg_replace('/[^0-9]/', '', $string);
	}

	public static function justLetters($string) {
		return preg_replace('/[^a-zA-Z]/', '', $string);
	}

	public static function truncate($string, $max, $type, $pad = false) {
		$pad_string = ' ';
		$pad_direction = STR_PAD_RIGHT;	
		
		if($type == 'numeric') {
			$string        = Davila_Nfe4web_Helper_Data::justNumbers($string);
			$pad_string    = 0;
			$pad_direction = STR_PAD_LEFT;
		} elseif($type == 'decimal') {
			$string = (float) $string;
			$string = number_format($string, $max, '.', '');
			$pad_string    = 0;
			$pad_direction = STR_PAD_LEFT;
		}
		
		if($type != 'decimal') {
			$string = substr($string, 0, $max);	
		}
		
		if($pad) {
			return str_pad($string, $max, $pad_string, $pad_direction);	
		}
		return $string;
	}

	public static function createGet($object, $method, $complement = '') {
		$camelCase = new Zend_Filter_Word_UnderscoreToCamelCase();
		$numbers   = (int) Davila_Nfe4web_Helper_Data::justNumbers($method);
		$method    = 'get'.$camelCase->filter($method).ucfirst($complement);
		$method    = (string) Davila_Nfe4web_Helper_Data::justLetters($method);

		if($numbers == 0){
			return $object->$method();
		}
		return $object->$method($numbers);
	}
}
	 