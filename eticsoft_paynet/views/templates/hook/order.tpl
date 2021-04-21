<div class="panel >
	<h3><i class="icon-credit-card"></i> {l s='Payment Details' mod='eticsoft_paynet'}</h3>
	<img src="../{$eticsoft_paynet_url}logo.png" width="150px"/>
	{if $paynet_error != false}
	<div class="alert alert-danger">
		{$paynet_error}
	</div>
	{else}
	<span class="badge">{$tr->reference_code }</span>
	<table class="table table-bordered">
		<tr>
			<td>{l s='Ödenen Tutar' mod='eticsoft_paynet'}
			</td><td> {$tr->amount} {$tr->currency }</td>
		</tr>
		<tr>
			<td>{l s='Net Tutar' mod='eticsoft_paynet'}</td><td> {$tr->netAmount} {$tr->currency }</td>
		</tr>
		<tr>
			<td>{l s='Komisyon Oranı ' mod='eticsoft_paynet'}</td><td> &percnt; {$tr->ratio*100} {$tr->payment_string}</td>
		</tr>
		<tr>
			<td>{l s='Tarih' mod='eticsoft_paynet'} </td><td> {$tr->xact_date }</td>
		</tr>
		<tr>
			<td>{l s='IP' mod='eticsoft_paynet'} </td><td> {$tr->ipaddress }</td>
		</tr>
		<tr>
			<td>{l s='Sonuç' mod='eticsoft_paynet'} </td><td> {$tr->message }</td>
		</tr>
		<tr>
			<td>{l s='Kredi Kartı' mod='eticsoft_paynet'}</td><td> {$tr->card_no}
			<br/>{$tr->card_holder}
			<br/>{$tr->card_type} {$tr->bank_id}</td>
		</tr>
	</table>
	<hr/>
	<div class="row" align="right">
		<a href="https://eticsoft.com"><img style="width: 110px;" src="../modules/eticsoft_paynet/views/img/eticsoft_logo.png"></a>
	</div>
	{/if}
</div>
<hr/>