<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
	exit;
}
include(dirname(__FILE__) . '/lib/PaynetClass.php');
class Eticsoft_Paynet extends PaymentModule
{

	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'eticsoft_paynet';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'EticSoft';
		$this->need_instance = 0;
        $this->id_eticsoft = 23;
        $this->key_eticsoft = '8022a3d3822bd67114d7f399c1675d3b';
        $this->curVersionFileURL = 'http://api.eticsoft.net/license/';

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('PayNet Credit Cart Payment Gateway');
		$this->description = $this->l('PayNet allows you to accept payments online via any type of credit cards.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
		$this->test_mode = Configuration::get('ETICSOFT_PAYNET_LIVE_MODE') ? 'prod' : 'test';
		$this->secretkey = Configuration::get('ETICSOFT_PAYNET_SECRET_KEY');
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		if (extension_loaded('curl') == false) {
			$this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
			return false;
		}
		Configuration::updateValue('ETICSOFT_PAYNET_LIVE_MODE', true);
		Configuration::updateValue('ETICSOFT_PAYNET_3DS_MODE', false);
		Configuration::updateValue('ETICSOFT_PAYNET_INS_FEE', true);
		Configuration::updateValue('ETICSOFT_PAYNET_DATA_KEY', '');
		Configuration::updateValue('ETICSOFT_PAYNET_SECRET_KEY', '');
		Configuration::updateValue('ETICSOFT_PAYNET_AGENT_CODE', '');
		Configuration::updateValue('ETICSOFT_PAYNET_INSTALLMENT_OPTIONS', '');
		Configuration::updateValue('ETICSOFT_PAYNET_RATIO_CODE', '');
		Configuration::updateValue('ETICSOFT_PAYNET_RATES_CACHE', null);
		Configuration::updateValue('ETICSOFT_PAYNET_LOGO_URL', _PS_IMG_.Configuration::get('PS_LOGO'));

		return parent::install() &&
			$this->registerHook('header') &&
			$this->registerHook('backOfficeHeader') &&
			$this->registerHook('payment') &&
			$this->registerHook('paymentReturn') &&
            $this->registerHook('productfooter') &&
            $this->registerHook('adminOrder') &&
			$this->registerHook('paymentOptions');
	}

	public function uninstall()
	{
		return parent::uninstall();
		Configuration::deleteByName('ETICSOFT_PAYNET_LIVE_MODE');
		Configuration::deleteByName('ETICSOFT_PAYNET_3DS_MODE');
		Configuration::deleteByName('ETICSOFT_PAYNET_INS_FEE');
		Configuration::deleteByName('ETICSOFT_PAYNET_DATA_KEY');
		Configuration::deleteByName('ETICSOFT_PAYNET_INSTALLMENT_OPTIONS');
		Configuration::deleteByName('ETICSOFT_PAYNET_RATIO_CODE');
		Configuration::deleteByName('ETICSOFT_PAYNET_SECRET_KEY');
		Configuration::deleteByName('ETICSOFT_PAYNET_AGENT_CODE');
		Configuration::deleteByName('ETICSOFT_PAYNET_LOGO_URL');

		return parent::uninstall();
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		/**
		 * If values have been submitted in the form, process.
		 */
        if (((bool) Tools::isSubmit('confirm_eticsoft_paynet_top')) == true) {
            if($this->registerPayNet())
				Configuration::updateValue('PAYNET_ACCEPT_TOS', true);
        }
		
		if (((bool) Tools::isSubmit('submitEticsoft_paynetModule')) == true) {
			$this->postProcess();
			$this->__construct();
		}
		if(!Configuration::get('PAYNET_ACCEPT_TOS')){
			$this->context->smarty->assign(array(
			'paynet_tos' => false,
			));
			return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
		}
		
		$paynet_rates = $this->getRates();

		if ($paynet_rates->code != 0){
			$installments = $this->displayError($this->l('Could not get Ratios. Please check your secret key'). print_r($paynet_rates,true));
		}
		else
			$installments = PaynetTools::getAdminInstallments(100, $paynet_rates->data);

		$this->context->smarty->assign(array(
			'paynet_tos' => Configuration::get('PAYNET_ACCEPT_TOS'),
			'module_dir' => $this->_path,
			'installments' => $installments
			));
		$output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
		return $this->renderForm().$output;
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitEticsoft_paynetModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			. '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);
			
		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Live mode'),
						'name' => 'ETICSOFT_PAYNET_LIVE_MODE',
						'is_bool' => true,
						'desc' => $this->l('Use this module in live mode'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Force 3D Secure'),
						'name' => 'ETICSOFT_PAYNET_3DS_MODE',
						'is_bool' => true,
						'desc' => $this->l('Accept only 3DS payments'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Installment Fee(s)'),
						'name' => 'ETICSOFT_PAYNET_INS_FEE',
						'is_bool' => true,
						'desc' => $this->l('Add fee(s) of installments to total payments.'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'desc' => $this->l('Enter your data key (A.k.a. Publishable Key)'),
						'name' => 'ETICSOFT_PAYNET_DATA_KEY',
						'label' => $this->l('Data Key'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-key"></i>',
						'desc' => $this->l('Enter your secret key'),
						'name' => 'ETICSOFT_PAYNET_SECRET_KEY',
						'label' => $this->l('Secret Key'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-home"></i>',
						'desc' => $this->l('Dealer Code (optional)'),
						'name' => 'ETICSOFT_PAYNET_AGENT_CODE',
						'label' => $this->l('Dealer Code'),
					),
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-home"></i>',
						'desc' => "Oran Kodu",
						'name' => 'ETICSOFT_PAYNET_RATIO_CODE',
						'label' => "Oran Kodu",
					),
						array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-home"></i>',
						'desc' => "Taksit Seçenekleri",
						'name' => 'ETICSOFT_PAYNET_INSTALLMENT_OPTIONS',
						'label' => "Taksit Seçenekleri",
					),
					
					
					array(
						'col' => 3,
						'type' => 'text',
						'prefix' => '<i class="icon icon-link"></i>',
						'desc' => $this->l('Enter your logo url for payment page'),
						'name' => 'ETICSOFT_PAYNET_LOGO_URL',
						'label' => $this->l('Logo Url (https)'),
					),
				),

				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	public function getConfigFormValues()
	{
		return array(
			'ETICSOFT_PAYNET_LIVE_MODE' => Configuration::get('ETICSOFT_PAYNET_LIVE_MODE', true),
			'ETICSOFT_PAYNET_3DS_MODE' => Configuration::get('ETICSOFT_PAYNET_3DS_MODE', true),
			'ETICSOFT_PAYNET_INS_MODE' => Configuration::get('ETICSOFT_PAYNET_INS_MODE', true),
			'ETICSOFT_PAYNET_INS_FEE' => Configuration::get('ETICSOFT_PAYNET_INS_FEE', true),
			'ETICSOFT_PAYNET_DATA_KEY' => Configuration::get('ETICSOFT_PAYNET_DATA_KEY', ''),
			'ETICSOFT_PAYNET_SECRET_KEY' => Configuration::get('ETICSOFT_PAYNET_SECRET_KEY', ''),
			'ETICSOFT_PAYNET_AGENT_CODE' => Configuration::get('ETICSOFT_PAYNET_AGENT_CODE', ''),
			'ETICSOFT_PAYNET_RATIO_CODE' => Configuration::get('ETICSOFT_PAYNET_RATIO_CODE', ''),
			'ETICSOFT_PAYNET_INSTALLMENT_OPTIONS' => Configuration::get('ETICSOFT_PAYNET_INSTALLMENT_OPTIONS', ''),
			'ETICSOFT_PAYNET_LOGO_URL' => Configuration::get('ETICSOFT_PAYNET_LOGO_URL', _PS_IMG_.Configuration::get('PS_LOGO')),
		);
	}
	

	/**
	 * Save form data.
	 */
	protected function postProcess()
	{
		$form_values = $this->getConfigFormValues();

		foreach (array_keys($form_values) as $key) {
			Configuration::updateValue($key, Tools::getValue($key));
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be loaded in the BO.
	 */
	public function hookBackOfficeHeader()
	{
		if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name) {
			$this->context->controller->addCSS($this->_path . 'views/css/installments.css');
		}
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		$this->context->controller->addCSS($this->_path . 'views/css/installments.css');
	}
	
	public function hookproductfooter($params)
	{
		return PaynetTools::getProductInstallments($params['product']['price_amount'], $this->getRates()->data);
	}
	
    function hookAdminOrder($params)
    {
        $order = new Order($params['id_order']);
		if(!$order || $order->module != 'eticsoft_paynet')
			return;
		$paynet_error = false;
		$tr = null;
		
		$payment_col = $order->getOrderPayments();
		$payment = new OrderPayment($payment_col[0]->id);
		if(!$payment->transaction_id)
			$paynet_error = $this->l('Transaction ID is not found!');
		
		if(!$paynet_error){
			$transaction = $this->getTransactionDetails($payment->transaction_id);
				if ((int)$transaction->code != 0 )
					$paynet_error = $transaction->code.' '.$transaction->message;	
			$tr = $transaction->Data[0];
		}
		$this->smarty->assign(array(
			'eticsoft_paynet_url' => $this->_path,
			'paynet_error' => $paynet_error,
			'tr' => $tr,
		));
		echo $this->display(__FILE__, 'views/templates/hook/order.tpl');
		
    }

	public 	function getTransactionDetails($xact_id)
	{
		try{
			$paynet = new PaynetClient(Configuration::get('ETICSOFT_PAYNET_SECRET_KEY'), $this->test_mode);
			$param = new TransactionDetailParameters();
			$param->xact_id = $xact_id;
		}
		catch (PaynetException $e)
		{
			echo $e->getMessage();
		}
		return $paynet->GetTransactionDetail($param);

	}

	/**
	 * This method is used to render the payment button,
	 * Take care if the button should be displayed or not.
	 */
	public function hookPayment($params)
	{
		$this->smarty->assign('module_dir', $this->_path);
		return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
	}

	/**
	 * This hook is used to display the order confirmation page.
	 */
	public function hookPaymentReturn($params)
	{
		return;
		if ($this->active == false)
			return;

		$order = $params['objOrder'];

		$this->smarty->assign(array(
			'id_order' => $order->id,
			'reference' => $order->reference,
			'params' => $params,
			'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
		));

		return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
	}
	
	private function registerPayNet($url = "")
    {
        $d = $_SERVER['HTTP_HOST'];
        if (substr($d, 0, 4) == " www.")
            $d = substr($d, 4);
        $data = array();
        $data['product_id'] = $this->id_eticsoft;
        $data['product_version'] = $this->version;
        $data['product_name'] = $this->name;
        $data['key_eticsoft'] = $this->key_eticsoft;
        $data['merchant_email'] = Configuration::get('PS_SHOP_EMAIL');
        $data['merchant_domain'] = $d;
        $data['merchant_ip'] = $_SERVER['SERVER_ADDR'];
        $data['merchant_name'] = Configuration::get('PS_SHOP_NAME');
        $data['merchant_version'] = Configuration::get('PS_INSTALL_VERSION');
        $data['merchant_software'] = 'Prestashop';
        $data['hash_key'] = md5($d . $this->version);
        return json_decode($this->CurlPostExt(array("q" => json_encode($data)), $this->curVersionFileURL));
    }

    private function curlPostExt($data, $url, $json = false)
    {
        $ch = curl_init(); // initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        if ($json)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
        curl_setopt($ch, CURLOPT_POST, 1); // set POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
        if ($result = curl_exec($ch)) { // run the whole process
            curl_close($ch);
            return $result;
        }
        return false;
    }
	
	 public function hookPaymentOptions($params)
    {
        if (!$this->active)
            return;
        if (version_compare(_PS_VERSION_, '1.7.0', '>=') != true)
            return $this->hookPayment();
        $payment_options = array();
        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption;
        $newOption->setModuleName($this->name)
                ->setCallToActionText($this->l('Pay by credit card'))
                ->setAction($this->context->link->getModuleLink($this->name, 'payment', array(), true))
                ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo.gif'))
                ->setAdditionalInformation($this->l('Secure and Easy CC Payment for all Cards'));
        $payment_options[] = $newOption;
        return $payment_options;
	}
	
	public function getRates($price = 100, $use_cache = false)
	{
		if($use_cache){
			if(	$cache = json_decode(Configuration::get('ETICSOFT_PAYNET_RATES_CACHE')))
				return $cache;
		}
		try {
//			die($this->secretkey.$this->test_mode);
			$paynet = new PaynetClient($this->secretkey, $this->test_mode);
			$ratioParameters=new RatioParameters();
			$ratioParameters->ratio_code= Configuration::get('ETICSOFT_PAYNET_RATIO_CODE', '');
			$rates = $paynet->GetRatios($ratioParameters);
		} catch (PaynetException $e) {
			return $e->getMessage();
		}
		configuration::updateValue('ETICSOFT_PAYNET_RATES_CACHE', json_encode($rates));
		return $rates;
	}
	
}
