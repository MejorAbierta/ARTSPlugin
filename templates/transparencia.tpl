<div id="page" class="page">


	<style>
		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		code {
			background: #0b1220;
			color: #d6deeb;
			padding: 16px;
			display: flex;
			font-display: flex;
			border-radius: 4px;
			font-family: ui-monospace, monospace;
		}

		table,
		th,
		td {
			border: 2px solid;
			border-collapse: collapse;
		}

		table {
			width: 100%;
		}

		.page {
			width: 80%;
			margin: auto;
		}
	</style>

	{debug}

	<table>
		<thead>
			Identificación:
		</thead>
		<tr>
			<td>ID: journal_id</td>
			<td>{$data.journalIdentity.id}</td>
		</tr>

		<tr>
			<td>Nombre de la revista</td>
			<td>{$data.journalIdentity.name.en}</td>
		</tr>

		<tr>
			<td>Abreviación</td>
			<td>{$data.journalIdentity.abbreviation.en}</td>
		</tr>

		<tr>
			<td>Print Issn</td>
			<td>{$data.journalIdentity.printIssn}</td>
		</tr>

		<tr>
			<td>Online Issn</td>
			<td>{$data.journalIdentity.onlineIssn}</td>
		</tr>

		<tr>
			<td>URL</td>
			<td>{$data.journalIdentity.urlPath}</td>
		</tr>

		<tr>
			<td>Descripción</td>
			<td>{$data.journalIdentity.description.en}</td>
		</tr>

		<tr>
			<td>Publisher Institution</td>
			<td>{$data.journalIdentity.publisherInstitution}</td>
		</tr>


		<tr>
			<td>Contacto Nombre</td>
			<td>{$data.journalIdentity.contactName}</td>
		</tr>

		<tr>
			<td>Contacto Email</td>
			<td>{$data.journalIdentity.contactEmail}</td>
		</tr>
	</table>

	</br>


	<table>
		<thead>
			Características de la revista:
		</thead>
		<tr>
			<td>Licencia por defecto del journal</td>
			<td>{$data.journalPreferences.copyrightHolderType}</td>
			<td>{$data.journalPreferences.copyrightYearBasis}</td>
		</tr>

		<tr>
			<td>Acceso a la revista</td>
			<td>{$data.journalPreferences.openAccessPolicy.en}</td>
		</tr>

		<tr>
			<td>Categorias</td>
			<td>{$data.category.title.en}</td>
		</tr>

		<tr>
			<td>Secciones</td>
			<td>{$data.section.title.en}</td>
		</tr>

		<tr>
			<td>Número de secciones</td>
			<td>{$data.sectionActives.count}</td>
		</tr>


		<tr>
			<td>Keywords mas usadas</td>
			<td>{call name="detailKeywords" data=$data.Keywords}</td>
		</tr>

		<tr>
			<td>Versión de OJS</td>
			<td>{$data.journalPreferences.version}</td>
		</tr>
	</table>

	</br>

	<table>
		<thead>
			Multilingüismo:
		</thead>
		<tr>
			<td>Idiomas activos en la UI</td>
			<td>{call name="detail" data=$data.journalPreferences.supportedLocales}</td>

		</tr>

		<tr>
			<td>Idiomas aceptados para los envíos</td>
			<td>{call name="detail" data=$data.journalPreferences.supportedSubmissionLocales}</td>

		</tr>
	</table>

	</br>

	<table>
		<thead>
			Producción editorial:
		</thead>
		<tr>
			<td>Año del primer número.</td>
			<td>{$data.firstyear.0.year}</td>

		</tr>

		<tr>
			<td>Números en el último año vencido.</td>
			<td>{call name="detailIssue" data=$data['numeros año']}</td>
		</tr>

		<tr>
			<td>Total de números publicados.</td>
			<td>{$data['numeros publicados'].count}</td>
		</tr>

		<tr>
			<td>Artículos en el último número.</td>
			<td>{$data['cantidad articulos en el ultimo numero'].count}</td>
		</tr>


		<tr>
			<td>Media de artículos por número.</td>
			<td></td>
		</tr>

		<tr>
			<td>Tasa de aceptación.</td>
			<td>{$data.tasa_aceptacion.tasa_aceptacion_porcentaje}</td>
		</tr>

		<tr>
			<td>Artículos rechazados en el último año.</td>
			<td>{$data.articles_refused.count}</td>
		</tr>

		<tr>
			<td>Rechazos en filtro previo.</td>
			<td>{$data['filtro_previo'].count}</td>
		</tr>


		<tr>
			<td>Rechazos en filtro previo.</td>
			<td>{$data.por_pares.count}</td>
		</tr>
		
		<tr>
			<td>Formato de las galeradas.</td>
			<td>{call name="detailFile" data=$data['Formato de las galeradas']}</td>
		</tr>
		<tr>
			<td>Media de revisores por artículo publicado</td>
			<td>{$data.media_revisores}</td>
		</tr>
		

		<tr>
			<td>% de revisiones contempladas en plazo</td>
			<td>{$data.porcentaje_revisiones}</td>
		</tr>

		<tr>
			<td>Tiempo medio de revisión</td>
			<td>{$data.media_revision}</td>
		</tr>
		
	</table>

	</br>

	<table>
		<thead>
			Difusión:
		</thead>
		<tr>
			<td>DOI de la revista</td>
			<td>{$data.journal_doi}</td>

		</tr>

		<tr>
			<td>OAI</td>
			<td></td>

		</tr>
	</table>




</div><!-- .page -->
</br>
<button id="printBtn">Imprimir / Exportar PDF</button>

{function name="detailKeywords" data=[]}
	<ul>
		{foreach from=$data item=item key=key name=name}
			<li>{$item.keyword_text}</li>
		{/foreach}
	</ul>
{/function}

{function name="detailIssue" data=[]}

	{foreach from=$data item=item key=key name=name}
		{$item.id},
	{/foreach}

{/function}

{function name="detailFile" data=[]}

	{foreach from=$data item=item key=key name=name}
		{$item.mimetype},
	{/foreach}

{/function}

{function name="detail" data=[]}

	{foreach from=$data item=item key=key name=name}
		{$item}
	{/foreach}

{/function}


<script>
	document.getElementById('printBtn').addEventListener('click', function() {
		var contenido = document.getElementById('page').innerHTML;
		var win = window.open('', '', 'height=500,width=700');
		win.document.write('<html><body>');
		win.document.write(contenido);
		win.document.write('</body></html>');
		win.document.close();
		win.print();
		win.close();
	});
</script>