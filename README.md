# Advanced Report Tool Suite - ARTS

#### Prerequisites

- OJS version 3.4, later versions not tested yet
- A compressed file containing the plugin (.tar.gz)

#### Installation Steps

1. Download the Plugin from [releases](https://github.com/MejorAbierta/ARTSPlugin/releases)

2. Log in to your OJS installation as an administrator.

3. Navigate to the Plugin Management Page, click on the "Settings" icon (gear icon) in the top right corner of the screen and select "Plugins" from the dropdown menu.

4. Upload the Plugin, On the Plugin Management page, click on the "Upload a Plugin" button. Select the compressed file containing the plugin from your local machine. Click "Upload" to upload the file to your OJS installation.

5. Install the Plugin. Once the file has been uploaded, click on the "Install" button next to the plugin. OJS will automatically extract the contents of the compressed file and install the plugin.

6. Visit your list of plugins and enable the ARTS plugin.

7. Click on the "config" icon of the plugin or visit directly the entrypoint /ARTS


### Example YAML
``` yaml

report:
  config:
    name: Informe
    authorization: role
    format: json

  data:
    - id: 01
      title: Nombre 
      operation: userGroup
      params: 1
      output:
        fields: title

```

### Available api methods
announcement($args, $request)

    Retrieves announcement data

#### author

  Retrieves author data
  - id
  - givenName
  - familyName
  - email
  - publicationId
  - userGroupId
  - country
  - affiliation

#### category

  Retrieves category data
  - id
  - title
  - description
  - parentId
  - contextId
  - sequence
  - path
  - image
  - sortOption

#### decision

  Retrieves decision data
  - id
  - dateDecided
  - decision
  - editorId
  - reviewRoundId
  - round
  - stageId
  - submissionId

institution($args, $request)

    Retrieves institution data

#### submissionFile

  Return submission file data
  - assocId
  - assocType
  - createdAt
  - fileId
  - fileStage
  - genreId
  - sourceSubmissionFileId
  - submissionId
  - updatedAt
  - uploaderUserId
  - viewable
  - dateCreated
  - language
  - name
  - locale
  - path
  - mimetype
 
representation()

  Retrieves representation data
  - submissionFileId
  - isApproved
  - locale
  - label
  - publicationId
  - urlPath
  - urlRemote
  - doiId

reviewers($args, $request)

    Retrieves reviewer data

journalIdentity()

  Retrieves journal identity information
  - id
  - urlPath
  - enabled
  - primaryLocale
  - currentIssueId
  - acronym
  - authorGuidelines
  - authorInformation
  - beginSubmissionHelp
  - contactEmail
  - contactName
  - contributorsHelp
  - copySubmissionAckPrimaryContact
  - copySubmissionAckAddress
  - emailSignature
  - enableDois
  - doiSuffixType
  - registrationAgency
  - disableSubmissions
  - editorialStatsEmail
  - forTheEditorsHelp
  - itemsPerPage
  - keywords
  - librarianInformation
  - name
  - notifyAllAuthors
  - numPageLinks
  - numWeeksPerResponse
  - numWeeksPerReview
  - openAccessPolicy
  - privacyStatement
  - readerInformation
  - reviewHelp
  - submissionAcknowledgement
  - submissionChecklist
  - submitWithCategories
  - supportedFormLocales
  - supportedLocales
  - supportedSubmissionLocales
  - themePluginPath
  - uploadFilesHelp
  - enableGeoUsageStats
  - enableInstitutionUsageStats
  - isSushiApiPublic
  - abbreviation
  - clockssLicense
  - copyrightYearBasis
  - enabledDoiTypes
  - doiCreationTime
  - enableOai
  - lockssLicense
  - membershipFee
  - publicationFee
  - purchaseArticleFee
  - doiVersioning
  - description
  - about
  - editorialTeam
  - onlineIssn
  - printIssn
  - publisherInstitution
  - licenseUrl
  - copyrightHolderType
  - version(OJS)

DAO($args, $request)

    Accesses Data Access Objects (DAOs)

about($args, $request)

    Retrieves "about" information for the journal

#### submissions

  Retrieves submission data
  - id
  - contextId
  - currentPublicationId
  - dateLastActivity
  - dateSubmitted
  - lastModified
  - locale
  - stageId
  - status
  - submissionProgress
  - publications

  
#### publications

  - id
  - accessStatus
  - datePublished
  - lastModified
  - primaryContactId
  - sectionId
  - submissionId
  - status
  - urlPath
  - version
  - doiId
  - categoryIds
  - copyrightYear
  - issueId
  - abstract
  - title
  - locale
  - authors
  - keywords
  - subjects
  - disciplines
  - languages
  - supportingAgencies
  - galleys

#### galleys
  - id
  - submissionFileId
  - isApproved
  - locale
  - label
  - publicationId
  - urlPath
  - urlRemote
  - doiId

#### issues

  Retrieves issue/volume data
  - id
  - journalId
  - volume
  - number
  - year
  - published
  - datePublished
  - dateNotified
  - lastModified
  - accessStatus
  - openAccessDate
  - showVolume
  - showNumber
  - showYear
  - showTitle
  - styleFileName
  - originalStyleFileName
  - urlPath
  - doiId
  - description
  - title


#### section

  Retrieves section data
  - id
  - contextId
  - reviewFormId
  - sequence
  - editorRestricted
  - metaIndexed
  - metaReviewed
  - abstractsNotRequired
  - hideTitle
  - hideAuthor
  - isInactive
  - wordCount
  - abbrev
  - policy
  - title

urls($args, $request)

    Generates important site URLs

reviews($args, $request)

    Retrieves review data

eventlogs($args, $request)

    Retrieves event logs

userGroup($args, $request)

    Retrieves user group data

Private/Protected Methods
read_env_file()

    Reads .env configuration file

compressAndDownload($files, $zipFileName)

    Compresses and downloads files

downloadFile($filePath, $fileName)

    Downloads individual files

anonimizeData($data)

    Anonymizes data by removing sensitive fields

parseyaml($args)

    Parses YAML configuration files
