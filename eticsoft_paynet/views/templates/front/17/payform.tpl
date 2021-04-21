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
{extends file='page.tpl'}

{block name="page_content"}
<div>
	<h3>{l s='Secure Payment via Credit Card' mod='eticsoft_paynet'}:</h3>
	{if $errmsg}
	<div class="alert alert-danger">
		{$errmsg}	
	</div>
	{/if}
	<ul class="alert alert-info">
			<li>{l s='Please click the "complete payment" button to pay with your credit card. You can also select another payment method via clieckin "Other Payment Methods" button' mod='eticsoft_paynet'}.</li>
	</ul>
	<div class="row">
		<div class="col-xs-6">
			{$js nofilter}
		</div>
		<div class="col-xs-6 pull-right float-right" align="right">
	        <a class="btn btn-info" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button_large">{l s='Other Payment Methods' mod='eticsoft_paynet'}</a>
		</div>
	</div>
</div>
{/block}