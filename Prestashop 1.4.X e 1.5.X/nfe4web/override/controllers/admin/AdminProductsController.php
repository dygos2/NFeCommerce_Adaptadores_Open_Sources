<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* 
* Adapted by PrestaBR <http://prestabr.com.br> - Nfe4Web
*/

class AdminProductsController extends AdminProductsControllerCore
{

	public function initFormInformations($product)
	{
		$data = $this->createTemplate($this->tpl_form);

		$currency = $this->context->currency;
		$data->assign('languages', $this->_languages);
		$data->assign('currency', $currency);
		$this->object = $product;
		$this->display = 'edit';
		$data->assign('product_name_redirected', Product::getProductName((int)$product->id_product_redirected, null, (int)$this->context->language->id));
		/*
		* Form for adding a virtual product like software, mp3, etc...
		*/
		$product_download = new ProductDownload();
		if ($id_product_download = $product_download->getIdFromIdProduct($this->getFieldValue($product, 'id')))
			$product_download = new ProductDownload($id_product_download);
		$product->{'productDownload'} = $product_download;

		$cache_default_attribute = (int)$this->getFieldValue($product, 'cache_default_attribute');

		$product_props = array();
		// global informations
		array_push($product_props, 'reference', 'ean13', 'upc',
		'available_for_order', 'show_price', 'online_only',
		'id_manufacturer', 'ncm', 'subst'
		);

		// specific / detailled information
		array_push($product_props,
		// physical product
		'width', 'height', 'weight', 'active',
		// virtual product
		'is_virtual', 'cache_default_attribute',
		// customization
		'uploadable_files', 'text_fields'
		);
		// prices
		array_push($product_props,
			'price', 'wholesale_price', 'id_tax_rules_group', 'unit_price_ratio', 'on_sale',
			'unity', 'minimum_quantity', 'additional_shipping_cost',
			'available_now', 'available_later', 'available_date'
		);

		if (Configuration::get('PS_USE_ECOTAX'))
			array_push($product_props, 'ecotax');

		foreach ($product_props as $prop)
			$product->$prop = $this->getFieldValue($product, $prop);

		$product->name['class'] = 'updateCurrentText';
		if (!$product->id)
			$product->name['class'] .= ' copy2friendlyUrl';

		$images = Image::getImages($this->context->language->id, $product->id);

		foreach ($images as $k => $image)
			$images[$k]['src'] = $this->context->link->getImageLink($product->link_rewrite[$this->context->language->id], $product->id.'-'.$image['id_image'], 'small_default');
		$data->assign('images', $images);
		$data->assign('imagesTypes', ImageType::getImagesTypes('products'));

		$product->tags = Tag::getProductTags($product->id);

		$data->assign('product_type', (int)Tools::getValue('type_product', $product->getType()));
		$data->assign('is_in_pack', (int)Pack::isPacked($product->id));

		$check_product_association_ajax = false;
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			$check_product_association_ajax = true;

		// TinyMCE
		$iso_tiny_mce = $this->context->language->iso_code;
		$iso_tiny_mce = (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');
		$data->assign('ad', dirname($_SERVER['PHP_SELF']));
		$data->assign('iso_tiny_mce', $iso_tiny_mce);
		$data->assign('check_product_association_ajax', $check_product_association_ajax);
		$data->assign('id_lang', $this->context->language->id);
		$data->assign('product', $product);
		$data->assign('token', $this->token);
		$data->assign('currency', $currency);
		$data->assign($this->tpl_form_vars);
		$data->assign('link', $this->context->link);
		$data->assign('PS_PRODUCT_SHORT_DESC_LIMIT', Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') ? Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT') : 400);
		$this->tpl_form_vars['product'] = $product;
		$this->tpl_form_vars['custom_form'] = $data->fetch();
	}
}

