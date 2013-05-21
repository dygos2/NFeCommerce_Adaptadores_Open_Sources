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
 * Carrega as opcoes de origem de mercadoria
 **/
class Davila_Nfe4web_Model_Fields_Origin extends Mage_Eav_Model_Entity_Attribute_Source_Table {
	protected function _construct() {
		$this->_init('nfe4web/fields_origin');
	}

	public function getAllOptions() {
		$options = array(
						0 => array('value' => 0, 'label' => 'Nacional - exceto as indicadas nos códigos 3 a 5'),
						1 => array('value' => 1, 'label' => 'Estrangeira - Importação direta, exceto a indicada no código 6'),
						2 => array('value' => 2, 'label' => 'Estrangeira - Adquirida no mercado interno, exceto a indicada no código 7'),
						3 => array('value' => 3, 'label' => 'Nacional - mercadoria ou bem com Conteúdo de Importação superior a 40%'),
						4 => array('value' => 4, 'label' => 'Nacional - cuja produção tenha sido feita em conformidade com os processos produtivos básicos de que tratam as legislações citadas nos Ajustes'),
						5 => array('value' => 5, 'label' => 'Nacional - mercadoria ou bem com Conteúdo de Importação inferior ou igual a 40%'),
						6 => array('value' => 6, 'label' => 'Estrangeira - Importação direta, sem similar nacional, constante em lista da CAMEX'),
						7 => array('value' => 7, 'label' => 'Estrangeira - Adquirida no mercado interno, sem similar'),
						);
		return $options;
	}
}
