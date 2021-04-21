{*
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
*}

<div class="panel">
	<div class="row eticsoft_paynet-header">
		<img src="{$module_dir|escape:'html':'UTF-8'}/logo.png" class="col-xs-6 col-md-3 text-center" id="payment-logo" />
		<div class="col-xs-6 col-md-6 text-center text-muted">
			{l s='PayNet SanalPOS Module provides the easiest way for you to accept payments, secure and easy' mod='eticsoft_paynet'}
		</div>
		<div class="col-xs-12 col-md-3 text-center">
			<a href="https://odeme.paynet.com.tr/" class="btn btn-primary" id="create-account-btn" target="_blank">{l s='Log in to your account' mod='eticsoft_paynet'}</a><br />
			<!--{l s='Already have one?' mod='eticsoft_paynet'}<a href="#" onclick="javascript:return false;"> {l s='Log in' mod='eticsoft_paynet'}</a>-->
		</div>
	</div>

	<hr />
	
	<div class="eticsoft_paynet-content">
		<div class="row">
			<div class="col-md-4">
				<h5>{l s='Benefits of using my PayNet module' mod='eticsoft_paynet'}</h5>
				<ul class="ul-spaced">
					<li>
						<strong>{l s='It is fast and easy' mod='eticsoft_paynet'}:</strong>
						{l s='It is fully integrated with PrestaShop, so you can configure it with a few clicks.' mod='eticsoft_paynet'}
					</li>
					
					<li>
						<strong>{l s='It is global' mod='eticsoft_paynet'}:</strong>
						{l s='Accept payments at your store without directions and 3rd part payment pages.' mod='eticsoft_paynet'}
					</li>
					
					<li>
						<strong>{l s='It is trusted' mod='eticsoft_paynet'}:</strong>
						{l s='Accept all credit cards and debit cards in all around the world' mod='eticsoft_paynet'}
					</li>
					
					<li>
						<strong>{l s='It is cost-effective' mod='eticsoft_paynet'}:</strong>
						{l s='There are no setup fees or long-term contracts. You only pay a low transaction fee.' mod='eticsoft_paynet'}
					</li>
				</ul>
			</div>
			
			<div class="col-md-4">
				<h5>{l s='How does it work?' mod='eticsoft_paynet'}</h5>
				<video style="width:100%; height: 100%;" controls="controls" poster="https://www.paynet.com.tr/assets/images/urunvideogorsel/sanalpos.jpg">
                            <source src="https://www.paynet.com.tr/assets/video/sanalpos.mp4" type="video/mp4">
                        </video>
			</div>
			<div class="col-md-4 text-right">
				<h5>{l s='Pricing' mod='eticsoft_paynet'}</h5>
				<dl class="list-unstyled">
					<dt>{l s='Payment Standard' mod='eticsoft_paynet'}</dt>
					<dd>{l s='Accept 7/24 payment' mod='eticsoft_paynet'}</dd>
					<dt>{l s='Payment Express' mod='eticsoft_paynet'}</dt>
					<dd>{l s='Secure' mod='eticsoft_paynet'}</dd>
					<dt>{l s='User friendly' mod='eticsoft_paynet'}</dt>
					<dd>{l s='Customizable' mod='eticsoft_paynet'}</dd>
				</dl>
				<!--<a href="https://www.paynet.com.tr/sanal-pos" onclick="javascript:return false;">(Detailed informations here)</a>-->
			</div>
			
		</div>

		<hr />
		
		<div class="row">
			<div class="col-md-12">
				<p class="text-muted">{l s='My Payment Module accepts more than 80 localized payment methods around the world' mod='eticsoft_paynet'}</p>
				
				<div class="row">
					<img src="{$module_dir|escape:'html':'UTF-8'}views/img/template_2_cards.png" class="col-md-3" id="payment-logo" />
					<div class="col-md-9 text-center">
						<h6>{l s='For more information, visit' mod='eticsoft_paynet'} {l s='or' mod='eticsoft_paynet'} <a href="https://www.paynet.com.tr/">PayNet</a></h6>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
