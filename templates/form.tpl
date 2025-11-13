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
	Create new yaml configuration file:
	
	</br>
	</br>
	<button id="addButton" class="pkp_button"> {translate key='plugins.generic.arts.add'}</button>
	</br>
	</br>

	<form class="pkp_form" id="openIDSettings" method="POST" enctype="multipart/form-data">
	

		{fbvElement readonly="on" type="textarea" id="textyaml" maxlength="250" inline=true size=$fbvStyles.size.MEDIUM}
		{fbvElement placeholder="plugins.generic.arts.file" type="text" id="titlefile" }

		<input class="pkp_button submitFormButton" type="submit"
			value="{translate key="plugins.generic.arts.accept"}" class="button defaultButton" />

	</form>
	
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

	<dialog class="pkp_modal_panel macenter" id="addConfigDialog">

		<div style="position: absolute;right: 0; margin-right: 13px;" id="closeButton2" class="pkp_button"><span
				:aria-hidden="true">×</span><span class="pkp_screen_reader">Close Panel</span></div>

		<fieldset class="search_advanced">
			<legend><b>{translate key='plugins.generic.arts.add'}</b></legend>

			<div class="ma">

				</br>
				</br>

				{translate key='plugins.generic.arts.operation'}
				<section>

					<select id="operation" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.arts.operation'} --</option>
						{foreach from=$operations item=operation key=name}

							<option value="{$operation}">{$operation}</option>

						{/foreach}
					</select>
				</section>
				</br>
				{translate key='plugins.generic.arts.filter'}
				</br>
				<textarea class="field text textyaml" id="params"></textarea>
				</br>
				</br>
				{translate key='plugins.generic.arts.fields'}
				</br>
				<textarea class="field text textyaml" id="fields"></textarea>
				</br>
				</br>
				{translate key='plugins.generic.arts.description'}
				</br>
				<textarea class="field text textyaml" id="title" ></textarea>

				<p>

					<strong>WARNING!</strong> Is recommended the use of filter to avoid big querys.

					<strong>Filter</strong>: If are available the filterByMethod you like to use.</br>
					&nbsp;&nbsp;&nbsp;&nbsp; Example for reviewers: Year=2023,2025;
					</br>
					<strong>Fields</strong>: FieldName.</br>
					&nbsp;&nbsp;&nbsp;&nbsp; Example for reviewers: id,userName,email

				</p>
				</br>
			</div>
			<button id="addConfigButton" class="pkp_button"> {translate key='plugins.generic.arts.add'}</button>

		</fieldset>


	</dialog>

	<dialog class="pkp_modal_panel macenter" id="addHeaderDialog">

		<div style="position: absolute;right: 0; margin-right: 13px;" id="closeButton" class="pkp_button"><span
				:aria-hidden="true">×</span><span class="pkp_screen_reader">Close Panel</span></div>

		<fieldset class="search_advanced">
			<legend><b>{translate key='plugins.generic.arts.add'}</b></legend>

			<div class="ma">
				{translate key='plugins.generic.arts.title'}
				<input class="field text" type="text" id="name" />
				</br>
				</br>

				{translate key='plugins.generic.arts.auth'}
				<section>

					<select id="auth" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.arts.auth'} --</option>
						{foreach from=$auths item=auth key=name}

							<option value="{$auth}">{$auth}</option>

						{/foreach}
					</select>
				</section>
				</br>
				</br>
				{translate key='plugins.generic.arts.format'}
				<section>

					<select id="format" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.arts.format'} --</option>
						{foreach from=$formats item=format key=name}

							<option value="{$format}">{$format}</option>

						{/foreach}
					</select>
				</section>
				</br>
				</br>

				{translate key='plugins.generic.arts.operation'}
				<section>

					<select id="operationheader" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.arts.operation'} --</option>
						{foreach from=$operationsheader item=operationheader key=name}

							<option value="{$operationheader}">{$operationheader}</option>

						{/foreach}
					</select>
				</section>
				</br>
				</br>

			</div>
			<button id="addHeaderButton" class="pkp_button"> {translate key='plugins.generic.arts.add'}</button>

		</fieldset>


	</dialog>

	<script>
		function autoResizeTextarea(textarea) {
			textarea.style.height = 'auto';
			textarea.style.height = textarea.scrollHeight + 'px';
		}

		(function() {
			var id = 0;
			var addButton = document.getElementById("addButton");
			var addConfigButton = document.getElementById("addConfigButton");
			var addHeaderButton = document.getElementById("addHeaderButton");
			var closeButton = document.getElementById("closeButton");
			var addConfigDialog = document.getElementById("addConfigDialog");
			var addHeaderDialog = document.getElementById("addHeaderDialog");

			addButton.addEventListener("click", function() {
				const textarea = document.getElementsByName('textyaml')[0];

				if (textarea.value.includes("report")) {
					addConfigDialog.showModal();
				} else {
					addHeaderDialog.showModal();
				}
			});

			closeButton.addEventListener("click", function() {
				addConfigDialog.close();
			});
			closeButton2.addEventListener("click", function() {
				addHeaderDialog.close();
			});

			addHeaderButton.addEventListener("click", function() {
				addHeaderDialog.close();
				const textarea = document.getElementsByName('textyaml')[0];

				var title = document.getElementById('name');
				var auth = document.getElementById('auth');
				var format = document.getElementById('format');
				var operation = document.getElementById('operationheader');


				textarea.value += "report:\n" +
					"  config:\n" +
					"    id: 01\n" +
					"    name: " + title.value + "\n" +
					"    authorization: " + auth.value + "\n" +
					"    format: " + format.value + "\n" +
					"    operation: " + operation.value + "\n" +
					"  data:\n";
				autoResizeTextarea(textarea);


			});

			addConfigButton.addEventListener("click", function() {
				addConfigDialog.close();
				const textarea = document.getElementsByName('textyaml')[0];

				var title = document.getElementById('title');
				var operation = document.getElementById('operation');
				var params = document.getElementById('params');
				var fields = document.getElementById('fields');

				id++;


				textarea.value += "\n" +
					"    - id: " + id + "\n" +
					"      description: " + title.value + "\n" +
					"      operation: " + operation.value + "\n" +
					"      params: " + params.value + "\n" +
					"      output:\n" +
					"        fields: " + fields.value + "\n";

				autoResizeTextarea(textarea);


			});


		})();
	</script>

</div><!-- .page -->
{include file="frontend/components/footer.tpl"}