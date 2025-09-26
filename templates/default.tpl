{include file="frontend/components/header.tpl" pageTitle='plugins.generic.mejorAbierta.description'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.mejorAbierta.description'}
	
	{foreach from=$data item=item key=key name=name}
		<div>{$item}</div>
		{foreach from=$item item=subitem key=subkey name=subname}
			<div>{$subitem}</div>
		{/foreach}
	{/foreach}
</div><!-- .page -->
{include file="frontend/components/footer.tpl"}