# **Advanced Report Tool Suite (ARTS)**

ARTS (Advanced Report Tool Suite) is a plugin for OJS 3.4+ designed to allow non-developer users to generate advanced reports using flexible YAML configuration files and/or by accessing OJS DAO methods directly.

### **Use Cases**

- When the built-in OJS reports do not provide the required data.
- Creating quality and monitoring indicators.
- Performing automated and periodic data extractions
- Generating reports that evolve over time.
- Feeding external applications with data from OJS.

### **Plugin Functional Overview**

The plugin offers two main functionalities:

1. **General-Purpose Report Generation**\
   Reads YAML configuration files and outputs reports in **JSON**, **HTML**, or **CSV/ZIP**. Each report defines which data to retrieve, how to process it, and how to present it. The plugin exposes an API endpoint for each configured report, returning the results according to the defined query.

2. **Direct Exposure of OJS DAO Methods**\
   The plugin automatically exposes all public methods of OJS 3.4+ Data Access Objects in **JSON** format. This allows unified data queries via the plugin API. Gradual transition to Eloquent ORM is ongoing in OJS 3.5.

> ⚠️ **Security Notice**
> 
> Access to reports and API endpoints requires specific user privileges or the appropriate token. The plugin currently is unable to reach PKP's tokens so the APIkey is hardcoded stored within the plugin directory. Ensure this token is handled securely to prevent unauthorized access. To configure the hardcoded token, rename `TEMPLATE.env` to `.env` and add your token.

### **Reports Configuration**

Each report accepts a `config` header section to define its security level, output format, and template.
The `data` section specifies a list of operations to execute, parameters, and fields to return.

**Example Reports:**

- A clone of the [FECYT plugin](https://github.com/MejorAbierta/fecytReportsPlugin): produces results similar to the FECYT plugin using DAO calls ([see config here](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/sellofecythtml.yaml)).
- A general **transparency report**: provides editorial status and metrics through DAO calls and arbitrary SQL execution ([see config here](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/transparencia_html.yaml)).

---

## **Installation Instructions**

**Recommended Method (Plugin Gallery):**

- Log in with administrator privileges.
- Navigate to `Settings > Website > Plugins`.
- Go the [plugin releases](https://github.com/MejorAbierta/ARTSPlugin/releases) and download the "arts.tar.gz" that fits with your OJS version.
- Click on "Upload A New Plugin" and select the file you just downloaded.
- Enable the plugin and click in the "Configuration" button to reach the plugin frontend.

**Manual Installation:**

```
1. Go the https://github.com/MejorAbierta/ARTSPlugin/releases and download the "arts.tar.gz" that fits with your OJS version.
2. Extract files into plugins/generic/arts.
3. Run:
   php lib/pkp/tools/installPluginVersion.php plugins/generic/ARTS/version.xml
4. Enable the plugin and visit /arts to see your reports.
```

---

## **Simple YAML Configuration**

```
report:
  config:
    name: Report
    description: Description for the report
    authorization: role
    format: json

  data:
    - id: 01
      title: Name
      operation: userGroup
      params: 1
      output:
        fields: title
```

See other examples [here](#).

**Expected JSON Output**

```
{
  "data": [
    {
      "id": "01",
      "title": "Editors"
    }
  ]
}
```

---

# **Operations**

These are the API methods that can be called directly via the ARTS API or referenced within YAML configuration files as `operation` entries. They expose core OJS data and allow structured retrieval of various entities. Methods are listed alphabetically for reference.

| Operation | Output | Fields |
|-----------|-------------|---|
| about | "About" information of the journal. | none |
| announcement | Announcement data. | `announcement_id`,`announcement_settings`,`assoc_type`,`assoc_id`,`type_id`,`announcement_types`,`date_expire`,`date_posted` |
| author | Author data. | `id`, `givenName`, `familyName`, `email`, `publicationId`, `userGroupId`, `country`, `affiliation`. |
| category | Category data. | `id`, `title`, `description`, `parentId`, `contextId`, `sequence`, `path`, `image`, `sortOption`. |
| decision | Decision data | `id`, `dateDecided`, `decision`, `editorId`, `reviewRoundId`, `round`, `stageId`, `submissionId`. |
| galleys | Galley data | `id`, `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| institution | Institution data | `institution_ip`, `institution_settings`, `institutional_subscriptions`, `metrics_counter_submission_institution_daily`, `metrics_counter_submission_institution_monthly`, `usage_stats_institution_temporary_records` |
| issues | Issue/volume data | `id`, `journalId`, `volume`, `number`, `year`, `published`, `datePublished`, `dateNotified`, `lastModified`, `accessStatus`, `openAccessDate`, `showVolume`, `showNumber`, `showYear`, `showTitle`, `styleFileName`, `originalStyleFileName`, `urlPath`, `doiId`, `description`, `title`. |
| journalIdentity | Journal identity data | `id`, `urlPath`, `enabled`, `primaryLocale`, `currentIssueId`, `acronym`, `authorGuidelines`, `contactEmail`, `editorialTeam`, `licenseUrl`, `onlineIssn`, `printIssn`, and OJS version. |
| publications | Publication data | `id`, `accessStatus`, `datePublished`, `lastModified`, `primaryContactId`, `sectionId`, `submissionId`, `status`, `urlPath`, `version`, `doiId`, `categoryIds`, `copyrightYear`, `issueId`, `abstract`, `title`, `locale`, `authors`, `keywords`, `subjects`, `disciplines`, `languages`, `supportingAgencies`, `galleys`. |
| representation | Representation data | `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| reviews | Reviews data | `review_round_file_id`, `submission_id`, `submissions`, `review_round_id`, `review_rounds`, `stage_id`, `submission_file_id` |
| users | `id`, `givenName`, `familyName`, `email`, `publicationId`, `userGroupId`, `country`, `affiliation`. |
| section | Section data | `id`, `contextId`, `reviewFormId`, `sequence`, `editorRestricted`, `metaIndexed`, `metaReviewed`, `abstractsNotRequired`, `hideTitle`, `hideAuthor`, `isInactive`, `wordCount`, `abbrev`, `policy`, `title`. |
| submissionFile | Submission file data | `assocId`, `assocType`, `createdAt`, `fileId`, `fileStage`, `genreId`, `sourceSubmissionFileId`, `submissionId`, `updatedAt`, `uploaderUserId`, `viewable`, `dateCreated`, `language`, `name`, `locale`, `path`, `mimetype`. |
| submissions | Submission data | `id`, `contextId`, `currentPublicationId`, `dateLastActivity`, `dateSubmitted`, `lastModified`, `locale`, `stageId`, `status`, `submissionProgress`, `publications`. |
| urls | urls of the journal |
| userGroup | User group data | `user_group_id`, `context_id`, `role_id`, `is_default`, `show_title`, `permit_self_registration`, `permit_metadata_edit` |

---

## **Generic Operations: DAO and doQuery**

- **DAO**: Dynamically calls any public method on any OJS DAO class. Requires specifying `dao` (the DAO class name), `method` (method name), and optional `params` (array of arguments). See [PKP documentation](https://docs.pkp.sfu.ca/dev/documentation/3.3/en/architecture-database#daos) for more information. The plugin will invoke the DAO method and return the result as JSON. This is useful for advanced queries not exposed via standard operations, but notice PKP is transitioning to Eloquent ORM in OJS 3.5 so this feature will be deprecated soon. ([See doc.](https://docs.pkp.sfu.ca/dev/documentation/en/architecture-daos#deprecated-daos))

- **doQuery**: Executes an arbitrary `SQL SELECT` query directly against the database. This is intentionally limitated to `SELECT` queries to avoid security issues. The SQL is provided in the `data.params` field of the YAML. Take in consideration that all other sections of the YAML (e.g., `data`, `output`) will keep working over the selected data.
**Warning:** Notice SQL is database-dependent (MariaDB, MySQL, PostgreSQL) so queries need to be adapted to the selected engine.

These generic operations provide maximum flexibility but require understanding of OJS internal DAOs and the database schema.

---

## **Templates**

When selecting the HTML format, you can specify which template to use. This is useful for presenting the content with a layout that best fits your needs.

The templates are written in Smarty 3 (the default template system in OJS 3.4) and are stored in the plugin’s templates/arts directory. Since OJS will soon migrate to the Blade template system, these templates will be adapted accordingly. Keep this in mind if you plan to create your own.

Three initial-basic templates were included:
- [`default.tpl`](#): Displays all data with minimal styling. Useful for debugging.
- [`fecyt.tpl`](#): Provides a more user-friendly layout for the FECYT report.
- [`transparencia.tpl`](#): Provides a more user-friendly layout for the transparency report.

---

## **Roadmap**

- [x] JSON output
- [x] HTML output
- [x] XML output
- [x] Filter by field
- [x] Basic interface.
- [x] Arbitrary SQL.
- [ ] Ensure existing OJS 3.4 actions (DAO) still work with OJS 3.5 (ORM)
- [ ] Add Swagger UI
- [ ] Improved interface to create the YAMLs.
- [ ] Integration with OJS API keys
- [ ] Allow PHP scripts
- [ ] Interface to add variables before running the plugin
- [ ] Add to PKP's gallery.

> [!NOTE]
> This project concludes on December 31, 2025, and no further development will be carried out until we new resources are obtained.
---

## **Notes**

- OJS version: 3.4 and 3.5
- Site-wide plugin: settings apply across all journals/presses
- License: GNU GPL v3. See LICENSE file for details
- Support: [GitHub Issues](https://github.com/MejorAbierta/ARTSPlugin/issues)
- Contact: [servicio.publicaciones(add)urjc.es](mailto:servicio.publicaciones@urjc.es)
