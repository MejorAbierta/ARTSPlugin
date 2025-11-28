# **Advanced Report Tool Suite (ARTS)**

ARTS (Advanced Report Tool Suite) is a plugin for OJS designed to allow non-developer users to generate advanced reports through flexible YAML configuration files and/or access OJS DAO methods directly.

### **Use Cases**

- When built-in OJS reporting plugins do not meet the requirements.
- For periodic automated extraction of specific data.
- To generate reports that evolve over time.
- To provide data to external applications.

### **Plugin Functional Overview**

The plugin offers two main functionalities:

1. **General-Purpose Report Generation**\
   Reads YAML configuration files and outputs reports in **JSON**, **HTML**, or **CSV/ZIP**. Each report defines which data to retrieve, how to process it, and how to present it. The plugin exposes an API endpoint for each configured report, returning the results according to the defined query.

2. **Direct Exposure of OJS DAO Methods**\
   The plugin automatically exposes all public methods of OJS 3.4+ Data Access Objects in **JSON** format. This allows unified data queries via the plugin API. Gradual transition to Eloquent ORM is ongoing in OJS 3.5.

> ⚠️ **Security Notice**
> 
> Access to reports and API endpoints requires an appropriate token or specific user privileges. The plugin currently uses a hardcoded token stored within the plugin directory. Ensure this token is handled securely to prevent unauthorized access.

### **Reports Configuration**

Each report accepts a `config` section to define its security level, output format, and template. The `data` section specifies a list of operations to execute, parameters, and fields to return.

**Example Reports:**

- A clone of the [FECYT plugin](https://github.com/MejorAbierta/fecytReportsPlugin): produces results similar to the FECYT plugin using DAO calls ([see config here](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/sellofecythtml.yaml)).
- A general **transparency report**: provides editorial status and metrics through DAO calls and optional SQL execution ([see config here](https://github.com/MejorAbierta/ARTSPlugin/blob/main/configs/transparencia_html.yaml)).

---

## **System Requirements**

- OJS **3.4** (later versions may work but are not fully tested).
- Compressed plugin archive in **.tar.gz** format.

---

## **Installation Instructions**

**Recommended Method (Plugin Gallery):**

- Log in with administrator privileges.
- Navigate to `Settings > Website > Plugins`.
- Select the Plugin Gallery, locate ARTS, and install it.
- Enable the plugin and access `/ARTS` to configure reports.

**Manual Installation:**

```
1. Download the latest release from GitHub (ensure version compatibility).
2. Extract files into plugins/generic/ARTS.
3. Run:
   php lib/pkp/tools/installPluginVersion.php plugins/generic/ARTS/version.xml
4. Enable the plugin and visit /ARTS to see your reports.
```

---

## **Simple YAML Configuration**

```
report:
  config:
    name: Report
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

| Operation | Description |
|-----------|-------------|
| about | Retrieves "about" information for the journal. |
| announcement | Retrieves announcement data. |
| author | Retrieves author data: `id`, `givenName`, `familyName`, `email`, `publicationId`, `userGroupId`, `country`, `affiliation`. |
| category | Retrieves category data: `id`, `title`, `description`, `parentId`, `contextId`, `sequence`, `path`, `image`, `sortOption`. |
| decision | Retrieves decision data: `id`, `dateDecided`, `decision`, `editorId`, `reviewRoundId`, `round`, `stageId`, `submissionId`. |
| galleys | Retrieves galley data: `id`, `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| institution | (TBD). |
| issues | Retrieves issue/volume data: `id`, `journalId`, `volume`, `number`, `year`, `published`, `datePublished`, `dateNotified`, `lastModified`, `accessStatus`, `openAccessDate`, `showVolume`, `showNumber`, `showYear`, `showTitle`, `styleFileName`, `originalStyleFileName`, `urlPath`, `doiId`, `description`, `title`. |
| journalIdentity | Retrieves journal identity information with fields such as `id`, `urlPath`, `enabled`, `primaryLocale`, `currentIssueId`, `acronym`, `authorGuidelines`, `contactEmail`, `editorialTeam`, `licenseUrl`, `onlineIssn`, `printIssn`, and OJS version. |
| publications | Retrieves publication data: `id`, `accessStatus`, `datePublished`, `lastModified`, `primaryContactId`, `sectionId`, `submissionId`, `status`, `urlPath`, `version`, `doiId`, `categoryIds`, `copyrightYear`, `issueId`, `abstract`, `title`, `locale`, `authors`, `keywords`, `subjects`, `disciplines`, `languages`, `supportingAgencies`, `galleys`. |
| representation | Retrieves representation data: `submissionFileId`, `isApproved`, `locale`, `label`, `publicationId`, `urlPath`, `urlRemote`, `doiId`. |
| reviews | (TBD). |
| reviewers | (TBD). |
| section | Retrieves section data: `id`, `contextId`, `reviewFormId`, `sequence`, `editorRestricted`, `metaIndexed`, `metaReviewed`, `abstractsNotRequired`, `hideTitle`, `hideAuthor`, `isInactive`, `wordCount`, `abbrev`, `policy`, `title`. |
| submissionFile | Returns submission file data: `assocId`, `assocType`, `createdAt`, `fileId`, `fileStage`, `genreId`, `sourceSubmissionFileId`, `submissionId`, `updatedAt`, `uploaderUserId`, `viewable`, `dateCreated`, `language`, `name`, `locale`, `path`, `mimetype`. |
| submissions | Retrieves submission data: `id`, `contextId`, `currentPublicationId`, `dateLastActivity`, `dateSubmitted`, `lastModified`, `locale`, `stageId`, `status`, `submissionProgress`, `publications`. |
| urls | (TBD). |
| userGroup | (TBD). |

---

## **Generic Operations: DAO and doQuery**

- **DAO**: Dynamically calls any public method on any OJS DAO class. Requires specifying `dao` (the DAO class name), `method` (method name), and optional `params` (array of arguments). The plugin uses reflection to invoke the DAO method and returns the result as JSON. Useful for advanced queries not exposed via standard operations. (TBD)

- **doQuery($args, $request)**: Executes an arbitrary SQL query directly against the database. The SQL is provided in the `data.params` field of the YAML. All other sections of the YAML (e.g., `data`, `output`) are ignored. \
  **Warning:** SQL is database-dependent (MariaDB, MySQL, PostgreSQL).

These generic operations provide maximum flexibility but require understanding of OJS internal DAOs and the database schema.

---

## **Notes**

- Site-wide plugin: settings apply across all journals/presses.
- License: GNU GPL v3. See LICENSE file for details.
- Support: [GitHub Issues](https://github.com/MejorAbierta/ARTSPlugin/issues)

