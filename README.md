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

author($args, $request)

    Retrieves author data

category($args, $request)

    Retrieves category data

decision($args, $request)

    Retrieves decision data

institution($args, $request)

    Retrieves institution data

submissionFile($args, $request)

    Handles submission files and allows compressed downloads

representation()

    Retrieves representation data

reviewers($args, $request)

    Retrieves reviewer data

journalIdentity()

    Retrieves journal identity information

DAO($args, $request)

    Accesses Data Access Objects (DAOs)

about($args, $request)

    Retrieves "about" information for the journal

submissions($args, $request)

    Retrieves submission data

issues($args, $request)

    Retrieves issue/volume data

section($args, $request)

    Retrieves section data

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
