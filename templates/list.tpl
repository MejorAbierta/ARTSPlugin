{include file="frontend/components/header.tpl" pageTitle='plugins.generic.arts.name'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.arts.name'}
	<h1>{translate key='plugins.generic.arts.displayName'}</h1>
	Current configuration files:
	<ul>
		
		{foreach from=$filesnames item=item key=key name=name}
			
			<li><a href="{$baseURL}{$item.filename}"> {$item.name}</a></li>
			<p>{$item.description}
			</p>
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

	<a href="https://github.com/MejorAbierta/ARTSPlugin">ReadMe</a>

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}