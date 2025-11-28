<?php

namespace APP\plugins\generic\arts;

use PKP\handler\APIHandler;


use PKP\security\Role;
use PKP\security\authorization\ContextAccessPolicy;


require_once __DIR__ . '/Utils.php';
require_once __DIR__ . '/ArtsReportGenerator.php';

foreach (glob(__DIR__ . '/traits/*.php') as $filename) {
    require_once $filename;
}


class ArtsHandler extends APIHandler
{

    use \APP\plugins\generic\arts\traits\UserTrait;
    use \APP\plugins\generic\arts\traits\AnnouncementTrait;
    use \APP\plugins\generic\arts\traits\SubmissionTrait;
    use \APP\plugins\generic\arts\traits\ContextTrait;
    use \APP\plugins\generic\arts\traits\RepositoryTrait;
    use \APP\plugins\generic\arts\traits\FilterTrait;
    use \APP\plugins\generic\arts\traits\UtilityTrait;
    use \APP\plugins\generic\arts\traits\ReportTrait;
    use \APP\plugins\generic\arts\traits\AnnouncementTrait;
    use \APP\plugins\generic\arts\traits\IssuesTrait;
    use \APP\plugins\generic\arts\traits\SectionTrait;

    public const DATA_OPERATIONS = [
        'author',
        'about',
        'reviewers',
        'journalIdentity',
        'announcement',
        'category',
        'citation',
        'decision',
        'institution',
        'submissionFile',
        'userGroup',
        'representation',
        'submissions',
        'publications',
        'issues',
        'urls',
        'reviews',
        'eventlogs',
        'DAO',
        'user',
        'page',
        'galley',
        'section'
    ];

    var $bannedFields = [
        'password',
        'apiKey',
        'email',
        // 'familyName',
        // 'givenName',
        'accessStatus'
    ];
    var $customfilters = [];

    private $plugin;


    public function __construct($plugin)
    {

        $this->plugin = $plugin;

        //PKPSiteAccessPolicy::SITE_ACCESS_ALL_ROLES
        $this->_handlerPath = 'ARTS';

        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {

            $this->addRoleAssignment(
                [
                    Role::ROLE_ID_MANAGER,
                    Role::ROLE_ID_SITE_ADMIN,

                ],
                array_merge(self::DATA_OPERATIONS, [
                    'parseyaml',
                    'report',
                    'doQuery'
                ])
            );
        }
    }
    public function authorize($request, &$args, $roleAssignments)
    {
        $headers = getallheaders();
        $this->read_env_file();
        $api_token = getenv('API_TOKEN');

        if (isset($headers['Authorization'])) {
            if ($headers['Authorization'] != "Bearer $api_token") {
                return "403";
            }
        } else {
            $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
        }

        return parent::authorize($request, $args, $roleAssignments);
    }
}
