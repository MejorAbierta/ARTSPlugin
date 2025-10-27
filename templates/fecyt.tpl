<div id="page" class="page">


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

	{function name="showArray" data=[] title=""}
		{if $data|@count > 0}


			{if $title == 'about'}
				</br><b>About</b>
				{$data[0][0]}
			{else if $title == 'STATUS_QUEUED'}
				<b>Submissions:</b></br>
				</br> QUEUED : {$data['count']}
			{else if $title == 'STATUS_PUBLISHED'}
				</br> PUBLISHED : {$data['count']}
			{else if $title == 'STATUS_DECLINED'}
				</br> DECLINED : {$data['count']}
			{else if $title == 'STATUS_SCHEDULED'}
				</br> SCHEDULED : {$data['count']}
			{else if $title == 'Export_author_data'}
				</br></br><b> Authors data </b>
				<table>
					<tr>
						<th>Affiliation</th>
						<th>Family Name</th>
						<th>Given Name</th>
					</tr>
					{call name="detailUser" data=$data}
				</table>
			{else if $title == 'Export_reviewer_data'}
				</br> <b> Reviewers data</b> </br>
				<table>
					<tr>
						<th>Affiliation</th>
						<th>Family Name</th>
						<th>Given Name</th>
					</tr>
					{call name="detailUser" data=$data}
				</table>
			{else if $title == 'Export_issues_documentation'}
				</br>
				<b>Issues documentation</b></br>
				<ul>
					<li><b>volume</b> {$data[0]['volume']}</li>
					<li><b>number</b> {$data[0]['number']}</li>
					<li><b>year</b> {$data[0]['year']}</li>
				</ul>




			{else if $title == 'Export_journal_identification_data'}
				</br></br> <b>Journal identification</b> </br>

				{$data[0]['name']['en']}

			{else if $title == 'Export_information_from_the_article_submission_page'}

				</br></br>

				<b>Author Guidelines</b> </br>
				{$data[0]['authorGuidelines']['en']}
				<b>Submission Checklist</b> </br>
				{$data[0]['submissionChecklist']['en']}
				<b>Privacy Statement</b> </br>
				{$data[0]['privacyStatement']['en']}

			{else if $title == 'Export_information_from_the_article_submission_page_2'}
				<b>Title</b></br>
				{$data[0]['title']['en']}</br>
				</br>
				<b>Policy</b></br>
				{$data[0]['policy']['en']}</br>

			{else if $title == 'Export_URLs'}
				</br><b>URLs</b>
				{call name="detailUrls" data=$data}


			{else if $title == 'Export_editorial_flow_of_the_selected_submission_reviews'}
				{*/
					Export_editorial_flow_of_the_selected_submission_reviews 
					/*}
				</br>
				</br>
				Export_editorial_flow_of_the_selected_submission_reviews
			{else if $title == 'Export_editorial_flow_of_the_selected_submission_eventlogs'}
				</br> <b> Editorial flow </b> </br>
				<table>
					<tr>
						<th>username</th>
						<th>dateLogged</th>
						<th>message</th>
						<th>filename</th>
					</tr>
					{call name="detailEventLog" data=$data}
				</table>
			{/if}




			{foreach from=$data item=$row key=$key}

				{* is array *}
				{if substr(var_export($row, true), 0, 5) === 'array'}
					{call name="showArray" data=$row title=$key}
				{else}

				{/if}

			{/foreach}
		{else}
		{/if}
	{/function}

	{function name="detailUrls" data=[] title=$title}

		<div>{$data|json_encode|escape:'html'|replace:",":", "}</div>
		{foreach from=$data item=item key=key name=name}
			{if substr(var_export($item, true), 0, 5) === 'array'}{* is array *}
				{call name="detailUrls" data=$item title=$key}
			{else}
				</br>
				{$title}<a href="{$item}">{$item}</a>
			{/if}
		{/foreach}
	{/function}

	{function name="detailUser" data=[] title=""}
		{foreach from=$data item=$item key=$key}

			{if $item['id']}
				<tr>
					<td>{$item['affiliation']['en']}</td>
					<td>{$item['familyName']['en']}</td>
					<td>{$item['givenName']['en']}</td>
				</tr>

			{else}
				{if substr(var_export($item, true), 0, 5) === 'array'}{* is array *}
					{call name="detailUser" data=$item title=$key}
				{/if}
			{/if}

		{/foreach}
	{/function}

	{function name="detailEventLog" data=[] title=""}
		{foreach from=$data item=$item key=$key}
			{if $item['id']}
				<tr>
					<td>{$item['username']}</td>
					<td>{$item['dateLogged']}</td>
					<td>{$item['message']}</td>
					<td>{$item['filename'][0]}</td>
				</tr>

			{else}
				{if substr(var_export($item, true), 0, 5) === 'array'}{* is array *}
					{call name="detailEventLog" data=$item title=$key}
				{/if}
			{/if}

		{/foreach}
	{/function}

	{call name="showArray" data=$data}


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