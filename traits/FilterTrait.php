<?php

namespace APP\plugins\generic\arts\traits;

trait FilterTrait
{
    function filter($fun, $param, $data)
    {
        $paramparts = explode(',', $param);

        if (count($paramparts) > 1) {
            $paramsFormated = [$param];
        } else {
            $paramsFormated = $paramparts;
        }
        switch ($fun) {
            case 'year':                                        //issues
                $data->filterByYears($paramsFormated);
                break;
            case 'published':                                   //issues
                $data->filterByPublished($param == "true");
                break;
            case 'hasdois':                                     //issues,submissions
                $data->filterByHasDois($param == "true");
                break;
            case 'volumes':                                     //issues 
                $data->filterByVolumes($paramsFormated);
                break;
            case 'titles':                                      //issues,section
                $data->filterByTitles($paramsFormated);
                break;
            case 'numbers':                                     //issues
                $data->filterByNumbers($paramsFormated);
                break;
            case 'status':                                      //submissions,user                                     
                $data->filterByStatus($paramsFormated);
                break;
            case 'doistatuses':                                 //issues,submissions
                $data->filterByDoiStatuses($paramsFormated);
                break;
            case 'issueids':                                    //issues,submissions
                $data->filterByIssueIds($paramsFormated);
                break;
            case 'urlpath':                                     //issues
                $data->filterByUrlPath($param);
                break;
            case 'categoryid':                                  //submissions
                $data->filterByCategoryIds($paramsFormated);
                break;
            case 'daysinactive':                                //submissions
                $data->filterByDaysInactive(intval($param));
                break;
            case 'incomplete':                                  //submissions
                $data->filterByIncomplete($param == "true");
                break;
            case 'overdue':                                     //submissions
                $data->filterByOverdue($param == "true");
                break;
            case 'sectionids':                                  //submissions
                $data->filterBySectionIds($paramsFormated);
                break;
            case 'stageids':                                    //submissions,decision
                $data->filterByStageIds($paramsFormated);
                break;
            case 'averagecompletion':                           //user
                $data->filterByAverageCompletion(intval($param));
                break;
            case 'dayssincelastassignment':                     //user
                $data->filterByDaysSinceLastAssignment(intval($param));
                break;
            case 'reviewerrating':                              //user
                $data->filterByReviewerRating(intval($param));
                break;
            case 'reviewsactive':                               //user
                $data->filterByReviewsActive(intval($param));
                break;
            case 'reviewscompleted':                            //user
                $data->filterByReviewsCompleted(intval($param));
                break;
            case 'roleids':                                     //user
                $data->filterByRoleIds($paramsFormated);
                break;
            case 'settings':                                    //user
                $data->filterBySettings($paramsFormated);
                break;
            case 'workflowstageids':                            //user
                $data->filterByWorkflowStageIds($paramsFormated);
                break;
            case 'ips':                                         //institutions
                $data->filterByIps($paramsFormated);
                break;
            case 'decisiontypes':                               //decision
                $data->filterByDecisionTypes($paramsFormated);
                break;
            case 'editorids':                                   //decision 
                $data->filterByEditorIds($paramsFormated);
                break;
            case 'reviewroundids':                              //decision
                $data->filterByReviewRoundIds($paramsFormated);
                break;
            case 'rounds':                                      //decision
                $data->filterByRounds($paramsFormated);
                break;
            case 'submissionids':                               //decision
                $data->filterBySubmissionIds($paramsFormated);
                break;
            case 'affiliation':                                 //author
                $data->filterByAffiliation($paramsFormated);
                break;
            case 'country':                                     //author
                $data->filterByCountry($paramsFormated);
                break;
            case 'includeinbrowse':                             //author
                $data->filterByIncludeInBrowse($param == "true");
                break;
            case 'name':                                        //author
                $data->filterByName($param);
                break;
            case 'publicationids':                              //author
                $data->filterByPublicationIds($paramsFormated);
                break;
            case 'active': //YYYY-MM-DD                         //announcement 
                $data->filterByActive($param);
                break;
            case 'typeids':                                     //announcement
                $data->filterByTypeIds($paramsFormated);
                break;
            case 'abbrevs':                                     //section
                $data->filterByAbbrevs($paramsFormated);
                break;
            case 'contextid':
                $data->filterByContextIds($paramsFormated);
                break;
            default:
                $this->customfilters[] = [$fun, $param];
                break;
        }
    }

    function filterBy($data, $filtername, $value)
    {
        if ($data == null) {
            return "No data";
        }

        if (is_string($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $item) {
            $item = (array) $item;

            if (isset($item['_data'][$filtername]['en'])) {
                if (str_contains($filtername, 'date')) {
                    if (substr(strtolower($item['_data'][$filtername]), 0, strlen($value)) == strtolower($value)) {
                        $result[] = $item;
                    }
                } else {
                    if (strtolower(json_encode($item['_data'][$filtername])) == strtolower($value)) {
                        $result[] = $item;
                    }
                }
            } else if (isset($item['_data'][$filtername])) {

                if (str_contains($filtername, 'date')) {
                    if (substr(strtolower($item['_data'][$filtername]), 0, strlen($value)) == strtolower($value)) {
                        $result[] = $item;
                    }
                } else {
                    if (strtolower(json_encode($item['_data'][$filtername])) == strtolower($value)) {
                        $result[] = $item;
                    }
                }
            } else if (isset($item[$filtername])) {
                if (strtolower(json_encode($item[$filtername])) == strtolower($value)) {
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    function filterByDate()
    {
    }

    function initFilter($args, $data)
    {
        $this->customfilters = [];
        try {
            $parts = explode(';', $args);
            if (count($parts) > 1) {
                foreach ($parts as $key => $value) {
                    $parts = explode('=', $value);
                    if (count($parts) > 1) {
                        $fun = $parts[0];
                        $param = $parts[1];

                        try {

                            $this->filter($fun, $param, $data);
                        } catch (\Throwable $th) {
                            $this->customfilters[] = [$fun, $param];
                        }
                    } else {
                        //$parts = explode('>', $args);
                    }
                }
            } else {
                $parts = explode('=', $args);
                if (count($parts) > 1) {
                    $fun = $parts[0];
                    $param = $parts[1];
                    try {
                        $this->filter($fun, $param, $data);
                    } catch (\Throwable $th) {
                        $this->customfilters[] = [$fun, $param];
                    }
                } else {
                    //$parts = explode('>', $args);
                }
            }
        } catch (\Throwable $th) {
            echo (json_encode($th->getMessage()));
            error_log("Error in filter: " . $th->getMessage());
        }
    }
}
