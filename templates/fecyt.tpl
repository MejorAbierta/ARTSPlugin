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

	</br><b>About</b>
	{$data.about.en}

	<b>Submissions:</b></br>
	</br> QUEUED : {$data.STATUS_QUEUED.count}
	</br> PUBLISHED : {$data.STATUS_PUBLISHED.count}
	</br> DECLINED : {$data.STATUS_DECLINED.count}
	</br> SCHEDULED : {$data.STATUS_SCHEDULED.count}

	</br></br><b> Authors data </b>
	<table>
		<tr>
			<th>Affiliation</th>
			<th>Family Name</th>
			<th>Given Name</th>
		</tr>
		{call name="detailUser" data=$data.Export_author_data}
	</table>

	{if $data.Export_reviewer_data|@count > 0}
		</br> <b> Reviewers data</b> </br>
		<table>
			<tr>
				<th>Affiliation</th>
				<th>Family Name</th>
				<th>Given Name</th>
			</tr>
			{call name="detailUser" data=$data.Export_reviewer_data}
		</table>
	{/if}

	{if $data.Export_issues_documentation|@count > 0}
		</br>
		<b>Issues documentation</b></br>

		<table>
			<tr>
				<th>Volume</th>
				<th>Number</th>
				<th>Year</th>
			</tr>
			{call name="detailIssue" data=$data.Export_issues_documentation}
		</table>


	{/if}

	{if $data.Export_journal_identification_data|@count > 0}
		</br></br> <b>Journal identification</b> </br>

		{$data.Export_journal_identification_data.name.en}
		</br>
	{/if}

	{if $data.Export_information_from_the_article_submission_page|@count > 0}
		</br>
		<b>Author Guidelines</b> </br>
		{$data.Export_information_from_the_article_submission_page['authorGuidelines']['en']}
		<b>Submission Checklist</b> </br>
		{$data.Export_information_from_the_article_submission_page['submissionChecklist']['en']}
		<b>Privacy Statement</b> </br>
		{$data.Export_information_from_the_article_submission_page['privacyStatement']['en']}
		</br>
	{/if}

	{if $data.Export_information_from_the_article_submission_page_2|@count > 0}
		</br>
		<b>Title</b></br>
		{$data.Export_information_from_the_article_submission_page_2['title']['en']}</br>
		</br>
		<b>Policy</b></br>
		{$data.Export_information_from_the_article_submission_page_2['policy']['en']}</br>

	{/if}

	{if $data.Export_URLs|@count > 0}
		</br><b>URLs</b>
		{call name="detailUrls" data=$data.Export_URLs}
	{/if}

	{if $data.Export_editorial_flow_of_the_selected_submission_reviews|@count > 0}
		</br></br> <b>Journal identification</b> </br>

		{$data.Export_editorial_flow_of_the_selected_submission_reviews}
		</br>
	{/if}

	{if $data.Export_editorial_flow_of_the_selected_submission_eventlogs|@count > 0}
		</br> <b> Editorial flow </b> </br>
	<table>
		<tr>
			<th>username</th>
			<th>dateLogged</th>
			<th>message</th>
			<th>filename</th>
		</tr>
		{call name="detailEventLog" data=$data.Export_editorial_flow_of_the_selected_submission_eventlogs}
	</table>
	{/if}

	
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

	{function name="detailIssue" data=[] title=""}
		{foreach from=$data item=$item key=$key}

			{if $item.volume}
				<tr>
					<td>{$item.volume}</td>
					<td>{$item.number}</td>
					<td>{$item.year}</td>
				</tr>

			{else}
				{if substr(var_export($item, true), 0, 5) === 'array'}{* is array *}
					{call name="detailIssue" data=$item title=$key}
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