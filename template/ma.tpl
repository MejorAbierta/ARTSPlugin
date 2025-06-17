
	<div id="report" class="page page_message">

		

		<h2>{translate key="plugins.reports.mejorabierta.name"}</h2>
		<select name="colors" id="color-select">
		<option value="">--Please choose an option--</option>
		<option value="Editorial">Editorial</option>
		<option value="About">About</option>
		<option value="journalId">journalId</option>
		<option value="Emails">Emails</option>
		<option value="Authors">Authors</option>
		<option value="Publications">Publications</option>
		<option value="Issues">Issues</option>
		<option value="Comments">Comments</option>
		</select>

		<details>
			<summary>Editorial</summary>
			{$editorial}
		</details>

		<details>
			<summary>journalId</summary>
			{$journalId}
		</details>

		<details>
			<summary>About</summary>
			{$about}
		</details>

		<details>
			<summary>Emails</summary>
			{$emails}
		</details>

		<details>
			<summary>Authors</summary>
			{$authors}
		</details>

		<details>
			<summary>All Publications</summary>
			{$publication}
		</details>

		<details>
			<summary>Declined</summary>
			{$declined}
		</details>

		<details>
			<summary>Queued</summary>
			{$queued}
		</details>

		<details>
			<summary>Scheduled</summary>
			{$scheduled}
		</details>

		<details>
			<summary>Issues</summary>
			{$issues}
		</details>

		<details>
			<summary>Comments</summary>
			{$comments}
		</details>
		
		<details>
			<summary>Reviewers</summary>
			{$reviewers}
		</details>
		
	</div>