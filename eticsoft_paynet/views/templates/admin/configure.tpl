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

{if !$paynet_tos}
	<br/><hr/>
	<form action="" method="post">
		<div class="alert alert-warning">
			<h2>Kullanım Şartları </h2>
			<ul>
				<li>PayNet modülü PayNet Ödeme Kuruluşu A.Ş için EticSoft tarafından GPL lisansı ile açık kaynaklı ve ücretsiz sunulmaktadır. <b>Satılamaz.</b></li>
				<li>PayNet modülü PayNet Ödeme Kuruluşu A.Ş 'nin sağladığı servisleri kullanmak için geliştirilmiştir. Başka amaçla kullanılamaz.</li>
				<li>Uluslararası kabul görmüş güvenlik standartlarına göre kredi kartı bilgilerine erişim veya bilgilerin kayıt edilmesi yasaktır. Bu eklenti orijinal kaynak kodlarıyla müşterilerinizin kredi kartı bilgilerini siteminize veya herhangi bir yere asla kaydetmez. Kaynak kodlarını bu kurallara uygun tutmak sizin sorumluluğunuzdadır.</li>
				<li>Eklentinin kurulu olacağı mağazaya ait version ve iletişim bilgileriniz (mağaza eposta, Prestashop versiyonu v.b.) geliştirici teknik destek ve bilgilendirme sistemine otomatik kayıt edilecek ve bu bilgiler önemli bildirimler ile güncellemelerden haberdar olmanız için kullanılacaktır.</li>
			</ul>
			<hr>
			<input type="checkbox" value="1" name="confirm_eticsoft_paynet_top" checked><br/>
			<label for="confirm_eticsoft_paynet_top">Kullanım şartlarını kabul ediyorum</label>
			<br>
			<button type="submit" class="btn btn-primary">Mağazamı Kaydet ve Başla</button>
		</div>
	</form>
{else}
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li class="active"><a href="#template_1" role="tab" data-toggle="tab">Taksit Seçenekleri</a></li>
	<li><a href="#template_2" role="tab" data-toggle="tab">PayNet Hakkında</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="template_1">{include file='./template_1.tpl'}</div>
	<div class="tab-pane" id="template_2">{include file='./template_2.tpl'}</div>
</div>
{/if}

<!--<div class="row panel">
<img src="../modules/eticsoft_paynet/views/img/eticsoft_logo.png">
<div class="col-sm-12 text-center">
                <a href="https://www.facebook.com/EticSoft/"><img src="../modules/eticsoft_paynet/views/img/icons/facebook.png" width="48px" /></a>
                <a href="https://twitter.com/eticsoft"><img src="../modules/eticsoft_paynet/views/img/icons/twitter.png" width="48px" /></a>
                <a href="https://www.youtube.com/user/EticSoft"><img src="../modules/eticsoft_paynet/views/img/icons/youtube.png" width="48px" /></a>
                <a href="https://www.linkedin.com/company/eticsoft-yaz%C4%B1l%C4%B1m"><img src="../modules/eticsoft_paynet/views/img/icons/linkedin.png" width="48px" /></a>
                <a href="https://www.instagram.com/eticsoft/"><img src="../modules/eticsoft_paynet/views/img/icons/instagram.png" width="48px" /></a>
                <a href="https://wordpress.org/support/users/eticsoft-lab/"><img src="../modules/eticsoft_paynet/views/img/icons/wordpress.png" width="48px" /></a>
                <a href="https://github.com/eticsoft/"><img src="../modules/eticsoft_paynet/views/img/icons/github.png" width="48px" /></a>
            </div>
</div>-->