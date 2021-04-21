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

include(dirname(__FILE__) . '/lib/PaynetClass.php');

class Eticsoft_paynetPaymentModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;
		$this->bootstrap = true;
		$mp = false;
		parent::initContent();

        /**
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false) {
            die;
        }
		$this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/iframe.css', 'all');
		$this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/iframe.js', 'false');

        $cart = Context::getContext()->cart;
        $customer = Context::getContext()->customer;
        $currency = Context::getContext()->currency;
        $lang = Context::getContext()->language;
        $secure_key = Context::getContext()->customer->secure_key;
		$params = $this->module->getConfigFormValues();
		
		$gateway_error = false;
		if(Tools::getValue('session_id') AND Tools::getValue('token_id')){	
			try{
				$paynet = new PaynetClient($params['ETICSOFT_PAYNET_SECRET_KEY'], $this->module->test_mode);
				$chargeParams = new ChargeParameters();
				$chargeParams->session_id = Tools::getValue('session_id');
				$chargeParams->token_id = Tools::getValue('token_id');
				$chargeParams->amount = strval($cart->getOrderTotal(true,3)*100);
				$chargeParams->ratio_code = Configuration::get('ETICSOFT_PAYNET_RATIO_CODE', '');
				$chargeParams->add_comission_amount = ($params['ETICSOFT_PAYNET_INS_FEE'] ? 'true': 'false');
				$chargeParams->installments = Configuration::get('ETICSOFT_PAYNET_INSTALLMENT_OPTIONS', '');
			    $chargeParams->tds_required	= $params['ETICSOFT_PAYNET_3DS_MODE'] ? 'true' : 'false';
				$result = $paynet->ChargePost($chargeParams);				
				if($result->is_succeed == true){
					$this->module->validateOrder($cart->id, Configuration::get('PS_OS_PAYMENT'), $cart->getOrderTotal(true, 3), $this->module->displayName, null, null, $cart->id_currency, false, $customer->secure_key);
					$order = new Order($this->module->currentOrder);
					$payment_col = $order->getOrderPayments();
					$payment = new OrderPayment($payment_col[0]->id);
					$payment->transaction_id = $result->xact_id;
					$payment->amount = $result->net_amount;
					$payment->save();				
					$currentOrderId = (int) $this->module->currentOrder;
					PaynetClient::updateOrderTotal($result->amount, $currentOrderId);
					PaynetClient::updateOrderPayment($result->amount, $order->reference);				
					Tools::redirectLink(__PS_BASE_URI__ . "order-confirmation.php?id_cart={$cart->id}&id_module={$this->module->id}&id_order={$this->module->currentOrder}&key={$order->secure_key}");
		
				} else {
					$gateway_error = $this->l('Your bank responsed:') . '(' . $result->code . ') ' . $result->message.' '.$result->paynet_error_message;
				}
			}
			catch (PaynetException $e)
			{
				$gateway_error = $e->getMessage();
			}
		}		
		
		    
		if($this->module->test_mode == 'prod')
			$jsurl = 'https://pj.paynet.com.tr/public/js/paynet.min.js';
		else
			$jsurl = 'https://pts-pj.paynet.com.tr/public/js/paynet.js';
		
		$js = '
			 <form action="" method="post" name="checkout-form" id="checkout-form">
		<script type="text/javascript"
				class="paynet-button"
				data-platform_id="PRESTASHOP"
				src="'.$jsurl.'"
				data-key="' . $params['ETICSOFT_PAYNET_DATA_KEY']. '"
				data-amount= '.(int)($cart->getOrderTotal(true, 3)*100).'
				data-image="'.Configuration::get('ETICSOFT_PAYNET_LOGO_URL').'"
				data-ratio_code="'.Configuration::get('ETICSOFT_PAYNET_RATIO_CODE', '').'"
				data-installments="'.Configuration::get('ETICSOFT_PAYNET_INSTALLMENT_OPTIONS', '').'"
				data-button_label="'.$this->module->l('Pay by credit card').'"
				data-description="'.$this->module->l('Kredi kartınızla ödemek için lütfen Ödemeyi Tamamla butonuna tıklayın.').'"
				data-agent="'.$params['ETICSOFT_PAYNET_AGENT_CODE'].'"
				data-add_commission_amount="'.($params['ETICSOFT_PAYNET_INS_FEE'] ? 'true': 'false').'"
				data-tds_required="'.($params['ETICSOFT_PAYNET_3DS_MODE'] ? 'true' : 'false').'"
				data-pos_type="5">
				
		</script>
	    </form>';
		$this->context->smarty->assign(array(
			'errmsg' => $gateway_error,
			'js' => $js,
		));
		
		if (version_compare(_PS_VERSION_, '1.7.0', '>=') === true) {
			return $this->setTemplate('module:eticsoft_paynet/views/templates/front/17/payform.tpl');
		}
		return $this->setTemplate('payform.tpl');

		//print_r($params);
		return($txt);

        if ($this->isValidOrder() === true) {
            $payment_status = Configuration::get('PS_OS_PAYMENT');
            $message = null;
        } else {
            $payment_status = Configuration::get('PS_OS_ERROR');

            /**
             * Add a message to explain why the order has not been validated
             */
            $message = $this->module->l('An error occurred while processing payment');
        }

        $module_name = $this->module->displayName;
        $currency_id = (int)Context::getContext()->currency->id;

        return $this->module->validateOrder($cart_id, $payment_status, $amount, $module_name, $message, array(), $currency_id, false, $secure_key);
    }

    protected function isValidOrder()
    {
        /**
         * Add your checks right there
         */
        return true;
    }
}
