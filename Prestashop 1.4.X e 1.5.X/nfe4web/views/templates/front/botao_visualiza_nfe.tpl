{*
 * Por/By: prestaBR e-Commerce Solutions - http://prestabr.com.br  
 * sac@prestabr.com.br
 *}
 
{if isset($nfe_link) && $nfe_link neq ''}
<ul class="address alternate_item {if $order->isVirtual()}full_width{/if}" style="min-height:100px;">
	<li class="address_title">{l s='Tax Invoice' mod='nfe4web'}</li>
    <li class="align_center"><br /><a href="{$nfe_link}" target="_blank" class="exclusive">{l s='Download NFe' mod='nfe4web'}</a></li>
</ul>
{/if}






