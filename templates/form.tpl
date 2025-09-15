{include file="frontend/components/header.tpl" pageTitle='plugins.generic.mejorAbierta.description'}
<div class="page">
	{include file="frontend/components/breadcrumbs.tpl" currentTitleKey='plugins.generic.mejorAbierta.description'}
	<h1>{translate key='plugins.generic.mejorAbierta.displayName'}</h1>
	Current configuration files:
	<ul>
	
	{foreach from=$filesnames item=item key=key name=name}
		
		<li><a href="{$baseURL}{$item}"> {$item}</a></li>
		
	{/foreach}
	</ul>
	Create new yaml configuration file:
	</br>
	</br>
	<button id="addButton" class="pkp_button"> {translate key='plugins.generic.mejorAbierta.add'}</button>
	</br>
	</br>
	<form class="pkp_form" id="openIDSettings" method="POST" enctype="multipart/form-data">
		{csrf}
		{fbvFormArea}

		{fbvElement readonly="on" type="textarea" id="textyaml" maxlength="250" inline=true size=$fbvStyles.size.MEDIUM}
		{fbvElement placeholder="plugins.generic.mejorAbierta.file" type="text" id="titlefile" }
		
		<input class="pkp_button submitFormButton" type="submit"
			value="{translate key="plugins.generic.mejorAbierta.accept"}" class="button defaultButton" />

		{/fbvFormArea}
	</form>

	<style>
		.macenter {
			border: 1px solid #ccc;
			box-sizing: border-box;
			width: 500px;
		}
		p{
			color: gray;
		}
		.ma {

		}

		.dropdown {
			width: 27vh;
		}

		#textyaml {
			width: 100%;
		}
	</style>

	<dialog class="pkp_modal_panel macenter" id="addConfigDialog">

		<div style="position: absolute;right: 0; margin-right: 13px;" id="closeButton2" class="pkp_button"><span
				:aria-hidden="true">×</span><span class="pkp_screen_reader">Close Panel</span></div>

		<fieldset class="search_advanced">
			<legend><b>{translate key='plugins.generic.mejorAbierta.add'}</b></legend>

			<div class="ma">

				</br>
				</br>

				{translate key='plugins.generic.mejorAbierta.operation'}
				<section>

					<select id="operation" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.mejorAbierta.operation'} --</option>
						{foreach from=$operations item=operation key=name}

							<option value="{$operation}">{$operation}</option>

						{/foreach}
					</select>
				</section>
				</br>
				{translate key='plugins.generic.mejorAbierta.filter'}
				</br>
				<textarea class="field text"  id="params"></textarea>
				</br>
				</br>
				{translate key='plugins.generic.mejorAbierta.fields'}
				</br>
				<textarea class="field text"  id="fields" ></textarea>
				</br>
				</br>
				{translate key='plugins.generic.mejorAbierta.description'}
				<input class="field text"  id="title" />

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
			<button id="addConfigButton" class="pkp_button"> {translate key='plugins.generic.mejorAbierta.add'}</button>

		</fieldset>


	</dialog>

	<dialog class="pkp_modal_panel macenter" id="addHeaderDialog">

		<div style="position: absolute;right: 0; margin-right: 13px;" id="closeButton" class="pkp_button"><span
				:aria-hidden="true">×</span><span class="pkp_screen_reader">Close Panel</span></div>

		<fieldset class="search_advanced">
			<legend><b>{translate key='plugins.generic.mejorAbierta.add'}</b></legend>

			<div class="ma">
				{translate key='plugins.generic.mejorAbierta.title'}
				<input class="field text" type="text" id="name" />
				</br>
				</br>

				{translate key='plugins.generic.mejorAbierta.auth'}
				<section>

					<select id="auth" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.mejorAbierta.auth'} --</option>
						{foreach from=$auths item=auth key=name}

							<option value="{$auth}">{$auth}</option>

						{/foreach}
					</select>
				</section>
				</br>
				</br>
				{translate key='plugins.generic.mejorAbierta.format'}
				<section>

					<select id="format" class="styled-select dropdown">
						<option value=""> -- {translate key='plugins.generic.mejorAbierta.format'} --</option>
						{foreach from=$formats item=format key=name}

							<option value="{$format}">{$format}</option>

						{/foreach}
					</select>
				</section>
				</br>
				</br>

			</div>
			<button id="addHeaderButton" class="pkp_button"> {translate key='plugins.generic.mejorAbierta.add'}</button>

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



				textarea.value += "report:\n" +
					"  config:\n" +
					"    id: 01\n" +
					"    name: " + title.value + "\n" +
					"    authorization: " + auth.value + "\n" +
					"    format: " + format.value + "\n" +
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