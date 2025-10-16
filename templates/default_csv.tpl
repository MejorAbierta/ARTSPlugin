{include file="frontend/components/header.tpl" pageTitle='plugins.generic.mejorAbierta.description'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.mejorAbierta.description'}


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
				{foreach from=$data item=row key=key}
					
					{* is array *}
					{if substr(var_export($row, true), 0, 5) === 'array'}
						{call name="showArray" data=$row}
					{else}
						

						{if $key=="published"}
							<dd>
								{if $row == 1}
									STATUS_QUEUED
								{else if $row == 3}
									STATUS_PUBLISHED
								{else if $row == 4}
									STATUS_DECLINED
								{else if $row == 5}
									STATUS_SCHEDULED
								{/if}
							<dd>
						{else}
							{$key}
							<li>{$row}</li>
						{/if}
					{/if}

				{/foreach}
			</ul>
		{else}
			<em>No data</em>
		{/if}
	{/function}

	<h2>Product list</h2>

	{call name="showArray" data=$data}

	{if $data|@count > 0}
		
		<h4>JSON</h4>
		<code>
			<div>{$data|json_encode|escape:'html'|replace:",":", "}</div>
		</code>

	{/if}

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}