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


	{if $data|@count > 0}
		{foreach from=$data item=item key=key name=name}

			{foreach from=$item item=subitem key=subkey name=subname}

				{foreach from=$subitem item=subsubitem key=subsubkey name=subsubname}
					{if $subsubitem}
					<b>
						<dt>{$subsubkey}</dt>
					</b>

					{if $subsubkey=="published"}
						<dd>
							{if $subsubitem == 1}
								STATUS_QUEUED
							{else if $subsubitem == 3}
								STATUS_PUBLISHED
							{else if $subsubitem == 4}
								STATUS_DECLINED
							{else if $subsubitem == 5}
								STATUS_SCHEDULED
							{/if}
						<dd>
						{else}
						<dd>{$subsubitem}</dd>
					{/if}
				{/if}
				{/foreach}

				
			{/foreach}
		{/foreach}

		<h4>JSON</h4>
		<code>
			<div>{$data|json_encode|escape:'html'|replace:",":", "}</div>
		</code>


	{/if}

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}