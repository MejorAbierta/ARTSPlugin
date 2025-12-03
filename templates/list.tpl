{include file="frontend/components/header.tpl" pageTitle='plugins.generic.arts.name'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.arts.name'}
	<h1>{translate key='plugins.generic.arts.displayName'}</h1>
	<p>
	The <b>ARTS</b> plugin allows advanced data extraction in <b>OJS</b>, 
	enabling the creation of <b>custom reports</b> from <b>YAML</b> configuration files or internal 
	<b>DAO</b> object calls. It also provides multiple <b>output formats</b> such as <b>JSON</b>, 
	<b>HTML</b>, or <b>CSV/ZIP</b>.</br>
	For more information and usage examples, see the plugin’s <a href="https://github.com/MejorAbierta/ARTSPlugin/blob/main/README.md">README</a> file.
	</p>
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

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}