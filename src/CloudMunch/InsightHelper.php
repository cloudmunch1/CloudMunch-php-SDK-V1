<?php

/*
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Rosmi Chandy rosmi@cloudmunch.com
 */

namespace CloudMunch;

use \DateTime;;
require_once 'CloudmunchConstants.php';
require_once 'AppErrorLogHandler.php';

/**
 * This is a helper class for environments. User can manage environments in cloudmunch using this helper.
 *
 *  @package CloudMunch
 *  @author Amith <amith@cloudmunch.com>
 */
class InsightHelper
{
    private $appContext = null;
    private $cmDataManager = null;
    private $logHelper = null;
    private $cmService = null;

    public function __construct($appContext, $logHandler)
    {
        $this->appContext = $appContext;
        $this->logHelper  = $logHandler;
        $this->cmService  = new CloudmunchService($appContext, $this->logHelper);
        $this->cmDataManager = new cmDataManager($this->logHelper, $appContext);
    }

    /**
     * @param string $type  type of resource
     *
     * @return array resources available with given type
     */
    public function getResources($type)
    {
        if($type) {
            $contextArray = array('resources' => '');
            $queryOptions = array('filter' => array('type' => $type), 'fields' => '*');
            return $this->cmService->getCustomContextData($contextArray, $queryOptions);
        } else {
            $this->logHelper->log('DEBUG', 'Resource type is not provided!');
            return false;
        }
    }

    /*******************************************************************************/
    /*******************************************************************************/
    /*************************** INSIGHT GET API UTILITIES *************************/
    /*******************************************************************************/
    /*******************************************************************************/
    
    /**
     * @param string $insightID
     * @param string $dataStoreID
     * @param array  $queryOptions associative array with key as query key and query value as value
     * @param string $extractID
     *
     * @return json object of extract details
     */
    public function getInsightDataStoreExtracts($insightID, $dataStoreID, $queryOptions, $extractID = '')
    {
        // /insights/{insight_id}/datastores/{datastore_id}/extracts/{extract_id}
        if (is_null($insightID) || empty($insightID) || is_null($dataStoreID) || empty($dataStoreID)) {
            $this->logHelper->log('DEBUG', 'Insight id and datastore id is needed to gets its extract details');

            return false;
        }

        $params =  array(
                            'resources'   => $insightID,
                            'datastores' => $dataStoreID,
                            'extracts'   => $extractID,
                        );
      //  var_dump($queryOptions);
        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

    /**
     * @param string $insightID
     * @param array  $queryOptions associative array with key as query key and query value as value
     * @param string $dataStoreID
     *
     * @return json object of datastore details
     */
    public function getInsightDataStores($insightID, $queryOptions, $dataStoreID = '')
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID)) {
            $this->logHelper->log('DEBUG', 'Insight id is needed to gets its datastore details');

            return false;
        }

        $params =  array(
                            'resources'   => $insightID,
                            'datastores' => $dataStoreID,
                        );

        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

    /**
     * @param string $insightID
     * @param string $reportID
     * @param array  $queryOptions associative array with key as query key and query value as value
     * @param string $cardID
     *
     * @return json object of report card details
     */
    public function getInsightReportCards($insightID, $reportID, $queryOptions, $cardID = '')
    {
        // /insights/{insight_id}/insight_reports/{insight_report_id}/insight_cards/{insight_card_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportID) || empty($reportID)) {
            $this->logHelper->log('DEBUG', 'Insight id and report id is needed to gets its report card details');

            return false;
        }

        $params =  array(
                            'resources'        => $insightID,
                            'insight_reports' => $reportID,
                            'insight_cards'   => $cardID,
                        );

        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

    /**
     * @param string $insightID
     * @param array  $queryOptions associative array with key as query key and query value as value
     * @param string $reportID
     *
     * @return json object of report details
     */
    public function getInsightReports($insightID, $queryOptions, $reportID = '')
    {
        // /insights/{insight_id}/insight_reports/{datastore_id}

        if (is_null($insightID) || empty($insightID)) {
            $this->logHelper->log('DEBUG', 'Insight id is needed to gets its report details');

            return false;
        }

        $params =  array(
                            'resources'        => $insightID,
                            'insight_reports' => $reportID,
                        );

        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

    /**
     * @param string $insightID
     * @param string $dataStoreID
     * @param string $extractName
     *
     * @return string extract id
     */
    public function getInsightDataStoreExtractID($insightID, $dataStoreID, $extractName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($dataStoreID) || empty($dataStoreID) || is_null($extractName) || empty($extractName)) {
            $this->logHelper->log('DEBUG', 'Insight id, datastore id and extract name is needed to get extract id');

            return false;
        }

        $queryOptions =  array(
                                    'filter' => array(
                                                        'name' => $extractName
                                                    ) 
                               );
        $response = $this->getInsightDataStoreExtracts($insightID, $dataStoreID, $queryOptions);

        if ($response) {
            return $response[0]->id;
        } else {
            return false;
        }
    }

    /**
     * @param string $insightID
     * @param string $dataStoreName
     *
     * @return string datastore id
     */
    public function getInsightDataStoreID($insightID, $dataStoreName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($dataStoreName) || empty($dataStoreName)) {
            $this->logHelper->log('DEBUG', 'Insight id and datastore name is needed to get datastore id');

            return false;
        }

        $queryOptions =  array(
                                    'filter' => array(
                                                        "name" => $dataStoreName
                                                    )
                               );

        $response = $this->getInsightDataStores($insightID, $queryOptions);

        if ($response) {
            return $response[0]->id;
        } else {
            return false;
        }
    }

    /**
     * @param string $insightID
     * @param string $reportID
     * @param string $cardName
     *
     * @return string card id
     */
    public function getInsightReportCardID($insightID, $reportID, $cardName)
    {
        // /insights/{insight_id}/insight_reports/{insight_report_id}/insight_cards/{insight_card_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportID) || empty($reportID) || is_null($cardName) || empty($cardName)) {
            $this->logHelper->log('DEBUG', 'Insight id, report id and card name is needed to get report card id');

            return false;
        }

        $queryOptions =  array(
                                    'filter' => array(
                                                        "name" => $cardName
                                                    )
                               );

        $response = $this->getInsightReportCards($insightID, $reportID, $queryOptions);

        if ($response) {
            return $response[0]->id;
        } else {
            return false;
        }
    }

    /**
     * @param string $insightID
     * @param string $reportName
     *
     * @return string report id
     */
    public function getInsightReportID($insightID, $reportName)
    {
        // /insights/{insight_id}/insight_reports/{insight_report_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportName) || empty($reportName)) {
            $this->logHelper->log('DEBUG', 'Insight id and report name is needed to get report id');

            return false;
        }

        $queryOptions =  array(
                                    'filter' => array(
                                                        "name" => $reportName
                                                    )
                               );

        $response = $this->getInsightReports($insightID, $queryOptions);

        if ($response) {
            return $response[0]->id;
        } else {
            return false;
        }
    }

    /*******************************************************************************/
    /*******************************************************************************/
    /************************* INSIGHT PATCH API UTILITIES *************************/
    /*******************************************************************************/
    /*******************************************************************************/

    /**
     * @param string $insightID
     * @param string $dataStoreID
     *
     * @return json object of extract details
     */
    public function updateInsightDataStoreExtract($insightID, $dataStoreID, $extractID, $data)
    {
        // /insights/{insight_id}/datastores/{datastore_id}/extract/{extract_id}
        if (is_null($insightID) || empty($insightID) || is_null($dataStoreID) || empty($dataStoreID) || is_null($extractID) || empty($extractID) || is_null($data)) {
            $this->logHelper->log('DEBUG', 'Insight id, datastore id, extract id and data is needed to update extract details');

            return false;
        }

        $params =  array(
                            'resources'   => $insightID,
                            'datastores' => $dataStoreID,
                            'extracts'   => $extractID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
    }

    /**
     * @param string $insightID
     * @param string $dataStoreID
     *
     * @return json object of datastore details
     */
    public function updateInsightDataStore($insightID, $dataStoreID, $data)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($dataStoreID) || empty($dataStoreID) || is_null($data)) {
            $this->logHelper->log('DEBUG', 'Insight id, datastore id and data is needed to update datastore details');

            return false;
        }

        $params =  array(
                            'resources'   => $insightID,
                            'datastores' => $dataStoreID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
    }

    /**
     * @param string $insightID
     * @param string $reportID
     * @param string $cardID
     *
     * @return json object of extract details
     */
    public function updateInsightReportCard($insightID, $reportID, $cardID, $data)
    {
        // /insights/{insight_id}/insight_reports/{insight_report_id}/insight_cards/{insight_card_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportID) || empty($reportID) || is_null($cardID) || empty($cardID) || is_null($data)) {
            $this->logHelper->log('DEBUG', 'Insight id, report id, card id and data is needed to update report card details');

            return false;
        }

        $params =  array(
                            'resources' => $insightID,
                            'insight_reports' => $reportID,
                            'insight_cards' => $cardID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
    }

    /**
     * @param string $insightID
     * @param string $reportID
     *
     * @return json object of report details
     */
    public function updateInsightReport($insightID, $reportID, $data)
    {
        // /insights/{insight_id}/insight_reports/{insight_report_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportID) || empty($reportID) || is_null($data)) {
            $this->logHelper->log('DEBUG', 'Insight id, report id and data is needed to update report card details');

            return false;
        }

        $params =  array(
                            'resources'        => $insightID,
                            'insight_reports' => $reportID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
    }

    /*******************************************************************************/
    /*******************************************************************************/
    /************************* INSIGHT POST API UTILITIES **************************/
    /*******************************************************************************/
    /*******************************************************************************/

    /**
     * @param string $insightID
     * @param string $dataStoreID
     * @param string $extractName
     *
     * @return string extract id
     */
    public function createInsightDataStoreExtract($insightID, $dataStoreID, $extractName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}
      //  echo "\ncreateInsightDataStoreExtract : $insightID, $dataStoreID, $extractName";
        if (is_null($insightID) || empty($insightID) || is_null($dataStoreID) || empty($dataStoreID) || is_null($extractName) || empty($extractName)) {
            $this->logHelper->log('DEBUG', 'Insight id, datastore id and extract name is needed to create an extract');

            return false;
        }

        $extractID = null;
        $extractID = $this->getInsightDataStoreExtractID($insightID, $dataStoreID, $extractName);

        if ($extractID) {
            return $extractID;
        } else {
            $this->logHelper->log('INFO', 'Attempting creation of extract with name '.$extractName.'...');

            $params =  array(
                                'resources'   => $insightID,
                                'datastores' => $dataStoreID,
                                'extracts'   => '',
                            );

            $data =  array('name' => $extractName);

            $response = $this->cmService->updateCustomContextData($params, $data, "POST");

            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $insightID
     * @param string $dataStoreName
     *
     * @return string dataStore id
     */
    public function createInsightDataStore($insightID, $dataStoretName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($dataStoretName) || empty($dataStoretName)) {
            $this->logHelper->log('DEBUG', 'Insight id and datastore name is needed to create a datastore');

            return false;
        }

        $dataStoreID = null;
        $dataStoreID = $this->getInsightDataStoreID($insightID, $dataStoretName);
        
        if ($dataStoreID) {
            return $dataStoreID;
        } else {
            $this->logHelper->log('INFO', 'Attempting creation of datastore with name '.$dataStoretName.'...');

            $params = array(
                                'resources'   => $insightID,
                                'datastores' => '',
                            );
            $data =  array('name' => $dataStoretName);

            $response = $this->cmService->updateCustomContextData($params, $data, "POST");

            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $insightID
     * @param string $reportID
     * @param string $cardName
     *
     * @return string report card id
     */
    public function createInsightReportCard($insightID, $reportID, $cardName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportID) || empty($reportID) || is_null($cardName) || empty($cardName)) {
            $this->logHelper->log('DEBUG', 'Insight id, report id and report card name is needed to create a report card');

            return false;
        }

        $cardID = null;

        $cardID = $this->getInsightReportCardID($insightID, $reportID, $cardName);

        if ($cardID) {
            return $cardID;
        } else {
            $this->logHelper->log('INFO', 'Attempting creation of report card with name '.$cardName.'...');

            $params =  array(
                                'resources' => $insightID,
                                'insight_reports' => $reportID,
                                'insight_cards' => '',
                            );
            $data =  array('name' => $cardName);

            $response = $this->cmService->updateCustomContextData($params, $data, "POST");
         //   echo "RESPONSE AFTER CARD CREATION ....."; print_r($response);

            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $insightID
     * @param string $reportName
     *
     * @return string report id
     */
    public function createInsightReport($insightID, $reportName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightID) || empty($insightID) || is_null($reportName) || empty($reportName)) {
            $this->logHelper->log('DEBUG', 'Insight id and report name is needed to create a report');

            return false;
        }

        $reportID = null;
        $reportID = $this->getInsightReportId($insightID, $reportName);

        if ($reportID) {
            return $reportID;
        } else {
            $this->logHelper->log('INFO', 'Attempting creation of report with name '.$reportName.'...');

            $params =  array(
                                'resources' => $insightID,
                                'insight_reports' => '',
                            );
            $data =  array('name' => $reportName);

            $response = $this->cmService->updateCustomContextData($params, $data, "POST");
           // echo "RESPONSE IS :";
           // var_dump($response);
            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }
    }

    /*******************************************************************************/
    /*******************************************************************************/
    /*************************** INSIGHT SPRINT UTILITIES **************************/
    /*******************************************************************************/
    /*******************************************************************************/
    
    public function sprint_getDateRangeForAllSprints() {
        $sprintsDetailsArray = $this->sprint_getSprintDetailsFromJiraCMDB();
        $dateRangeForSprints = array();
        if ($sprintsDetailsArray) {
            $sprints = array_reverse($this->sprint_getSprintsWithDates($sprintsDetailsArray));
            $dateRangeForSprints = array();
            $sprintCount = 0;
            foreach ($sprints as $k => $val) {
                $sprintID = $k;
                $filterStrForDateRange = $this->sprint_giveAFilterStringForASprint($sprints, $sprintID);
                $sprintName = "S".(string)$sprintCount;
                $dateRangeForSprints[$sprintName] = $filterStrForDateRange;
                $sprintCount++;
            }
            return $dateRangeForSprints;
        }
    }

    public function sprint_getSprintsWithDates($sprintDetailsArray) {
        $sprintHash = array();
        foreach ($sprintDetailsArray as $arrElemHash) {
            $sprintData = $arrElemHash->data->sprints;

            if ($sprintData->startDate != "None" && $sprintData->endData != "None") {
                $sprintHashData = array();
                $startDate = DateTime::createFromFormat('d/M/y H:i a', $sprintData->startDate);
                $endDate   = DateTime::createFromFormat('d/M/y H:i a', $sprintData->endDate);
                $sprintHashData["id"]           = $sprintData->sprint_id;
                $sprintHashData["name"]         = $sprintData->sprint_name;
                $sprintHashData["status"]       = $sprintData->sprint_status;
                $sprintHashData["startDate"]    = $startDate->format("Y-m-d");
                $sprintHashData["endDate"]      = $endDate->format("Y-m-d");;
                $sprintHashData["completeDate"] = $sprintData->completeDate;

                $sprintHash[$sprintData->sprint_id] = $sprintHashData;
            }
        }
        return $sprintHash;
    }

    public function sprint_giveAFilterStringForASprint($sprintHash, $sprintID) {
        $resultRange = $this->sprint_giveADateRangeOfASprint($sprintHash, $sprintID);
        $timeArray = $resultRange[$sprintID];

        if ($timeArray && is_array($timeArray) && count($timeArray) > 0){
            $timeString = "";
            foreach ($timeArray as $key => $value) {
                $timeString = ($timeString !== "") ? $timeString.",".$value : $value;
            }
            return 'IN ('.$timeString.')';
        } else {
            return;            
        }
    }

    public function sprint_giveADateRangeOfASprint($sprintHash, $sprintID) {
        $sprintHashData = $sprintHash;
        if (array_key_exists($sprintID, $sprintHashData)) {
            $sprintDatesHash = $sprintHashData[$sprintID];
            $sprintStartDate = $sprintDatesHash["startDate"];
            $sprintEndDate   = $sprintDatesHash["endDate"];
            $startDate       = new DateTime($sprintStartDate);
            $endDate         = new DateTime($sprintEndDate);
            $totalDaysDiff   = $startDate->diff($endDate)->format("%a");
            $range           = $this->identifyDatesForDurationUnit("day", $totalDaysDiff, $sprintEndDate);
            if ($range === "INVALID"){
                return;
            }
            $sprintDateRange = $range;
            $resultHash      = array();
            $resultHash[$sprintID] = $sprintDateRange;

            return $resultHash;
        } else {
            return;
        }
    }


    public function sprint_getSprintDetailsFromJiraCMDB(){
        list($jiraResourceID, $jiraProjectName, $rapidBoardID, $mvpVersion) = $this->sprint_getJiraProjectNameFromResource("jira");

        if ($jiraResourceID && $jiraProjectName && $rapidBoardID && $mvpVersion) {
            $dataStoreForJiraSprints = "jira_sprints";
            $jiraSprintsDataStore    = $rapidBoardID."_".$mvpVersion."_".$dataStoreForJiraSprints;
            $jiraSprintsDataExtract  = "*";

            return $this->sprint_getJiraSprintsData($jiraResourceID, $jiraSprintsDataStore, $jiraSprintsDataExtract);
        } else {
            return;
        }
    }

    public function sprint_getJiraSprintsData($insightOrResourceID, $dataStoreName, $extractName) {
        $dataStoreID = $this->getInsightDataStoreID($insightOrResourceID, $dataStoreName);

        $paramHash = array();
        $paramHash["fields"] = "data";
        $paramHash["filter"] = array( 'name' => $extractName );

        return $this->getInsightDataStoreExtracts($insightOrResourceID,$dataStoreID,$paramHash,'');
    }

    public function sprint_getJiraProjectNameFromResource($jiraResourceType) {
        $jiraResourceData = $this->getResources($jiraResourceType);
        if ($jiraResourceData && count($jiraResourceData) > 0) {
            $jiraProjectName = $jiraResourceData[0]->key_fields->jiraProject;
            $jiraResourceID  = $jiraResourceData[0]->id;
            $rapidBoardID    = $jiraResourceData[0]->key_fields->rapidBoardId;
            $mvpVersion      = $jiraResourceData[0]->key_fields->mvpVersion;
            return array($jiraResourceID, $jiraProjectName, $rapidBoardID, $mvpVersion);
        } else {
            return;
        }
    }

    /**
     *   Return the dates range based on the projection unit and count
     *
     *   @param string projectionUnit  : Projection Unit in Days, Months or Weeks
     *   @param string projectionCount : Projection Count of last data expected
     *
     *   @return array of dates
     */
    public function identifyDatesForDurationUnit($projectionUnit, $projectionCount, $curr_date = null)
    {
        $iCount       = 0;
        $duration_arr = [];
        $curr_date    = is_null($curr_date) ? date("Y-m-d") : $curr_date;
        $oneDay = ' -1 day';

        switch ($projectionUnit) {
            case "day":
                while ($iCount < $projectionCount) {
                    $duration_arr[$iCount] = $curr_date;
                    $curr_date = date('Y-m-d', strtotime($oneDay, strtotime($curr_date)));
                    ++$iCount;
                }
                break;
            case "week":
                $projectionCount = $projectionCount * 7;
                while ($iCount < $projectionCount) {
                    $duration_arr[$iCount] = $curr_date;
                    $curr_date = date('Y-m-d', strtotime($oneDay, strtotime($curr_date)));
                    ++$iCount;
                }
                break;
            case "month":
                $projectionCount = $projectionCount * 30;
                while ($iCount < $projectionCount) {
                    $duration_arr[$iCount] = $curr_date;
                    $curr_date = date('Y-m-d', strtotime($oneDay, strtotime($curr_date)));
                    ++$iCount;
                }
                break;
            case "sprint":
                $duration_arr = $this->sprint_getDateRangeForAllSprints();
                break;
            default : break;
        }
        if (is_array($duration_arr) && $projectionUnit === "sprint") {
            return $duration_arr;        
        } elseif (is_array($duration_arr)) {
            return array_reverse($duration_arr);
        } else {
            return "INVALID";
        }
    }

}
?>
