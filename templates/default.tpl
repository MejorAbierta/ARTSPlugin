{include file="frontend/components/header.tpl" pageTitle='plugins.generic.arts.description'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.arts.description'}


	<style>
		code {
			background: #0b1220;
			color: #d6deeb;
			padding: 16px;
			display: flex;
			font-display: flex;
			border-radius: 4px;
			font-family: ui-monospace, monospace;
		}
	</style>

	{function name="showArray" data=[]}
		{if $data|@count > 0}
			<ul>
				{foreach from=$data item=$row key=$key}
					
					{* is array *}
					{if is_array($row) || is_object($row)}
						{call name="showArray" data=$row}
					{else}
							
							{$key}
							
							{$row}
					{/if}

				{/foreach}
			</ul>
		{else}
			<em>No data</em>
		{/if}
	{/function}

	{call name="showArray" data=$data}

	{if $data|@count > 0}
		
		<h4>JSON</h4>
		<code>
			<div>{$data|json_encode|escape:'html'|replace:",":", "}</div>
		</code>

	{/if}

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}