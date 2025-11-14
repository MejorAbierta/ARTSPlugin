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
		<tr>
			<td>Journal Name</td>
			<td>{$data.journalIdentity.name.en}</td>
		</tr>

		<tr>
			<td>Publisher Institution</td>
			<td>{$data.journalIdentity.publisherInstitution}</td>
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
			<td>Contact Name</td>
			<td>{$data.journalIdentity.contactName}</td>
		</tr>

		<tr>
			<td>Contact Email</td>
			<td>{$data.journalIdentity.contactEmail}</td>
		</tr>
		</tr>

		<tr>
			<td>description.en</td>
			<td>{$data.journalIdentity.description.en}</td>
		</tr>
		</tr>

		<tr>
			<td>urlPath</td>
			<td>{$data.journalIdentity.urlPath}</td>
		</tr>
		</tr>

		<tr>
			<td>abbreviation.en</td>
			<td>{$data.journalIdentity.abbreviation.en}</td>
		</tr>
	</table>




</div><!-- .page -->
</br>
<button id="printBtn">Imprimir / Exportar PDF</button>


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