{include file="frontend/components/header.tpl" pageTitle='plugins.generic.arts.description'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.arts.description'}
	<h1>{translate key='plugins.generic.arts.displayName'}</h1>
	Current configuration files:
	<ul>
		
		{foreach from=$filesnames item=item key=key name=name}

			<li><a href="{$baseURL}{$item}"> {$item}</a></li>

		{/foreach}
	</ul>


	<style>
		.macenter {
			border: 1px solid #ccc;
			box-sizing: border-box;
			width: 500px;
		}

		p {
			color: gray;
		}

		.ma {}

		.dropdown {
			width: 27vh;
		}

		#textyaml {
			width: 100%;
		}
		.textyaml{
			width: 100%;
		}
	</style>

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}