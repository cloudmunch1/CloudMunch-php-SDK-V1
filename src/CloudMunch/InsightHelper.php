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

require_once 'CloudmunchConstants.php';
require_once 'AppErrorLogHandler.php';

/**
 * This is a helper class for environments. User can manage environments in cloudmunch using this helper.
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
                            'insights'   => $insightID,
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
                            'insights'   => $insightID,
                            'datastores' => $dataStoreID,
                        );

        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

    /**
     * @param array  $queryOptions associative array with key as query key and query value as value
     * @param string $insightID
     *
     * @return json object of insight details
     */
    public function getInsights($queryOptions, $insightID = '')
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        $params =  array(
                            'insights' => $insightID,
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
                            'insights'        => $insightID,
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
                            'insights'        => $insightID,
                            'insight_reports' => $reportID,
                        );

        return $this->cmService->getCustomContextData($params, $queryOptions);
    }

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
                            'insights'   => $insightID,
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
                            'insights'   => $insightID,
                            'datastores' => $dataStoreID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
    }

    /**
     * @param string $insightID
     *
     * @return json object of insight details
     */
    public function updateInsight($insightID, $data)
    {
        // /insights/{insight_id}

        if (is_null($insightID) || empty($insightID) || is_null($data)) {
            $this->logHelper->log('DEBUG', 'Insight id and data is needed to update datastore details');

            return false;
        }

        $params =  array(
                            'insights' => $insightID,
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
                            'insights' => $insightID,
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
                            'insights'        => $insightID,
                            'insight_reports' => $reportID,
                        );

        return $this->cmService->updateCustomContextData($params, $data);
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

      //  echo "RESPONSE --------------> \n";
     //   var_dump ($response);

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
     * @param string $insightName
     *
     * @return String insight id
     */
    public function getInsightID($insightName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightName) || empty($insightName)) {
            $this->logHelper->log('DEBUG', 'Insight id and datastore name is needed to get datastore id');

            return false;
        }

        $queryOptions =  array(
                                    'filter' =>  array(
                                                        "name" => $insightName
                                                    )
                               );

        $response = $this->getInsights($queryOptions);

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
                                'insights'   => $insightID,
                                'datastores' => $dataStoreID,
                                'extracts'   => '',
                            );

            $data =  array('name' => $extractName);

            $response = $this->cmService->updateCustomContextData($params, $data);

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
                                'insights'   => $insightID,
                                'datastores' => '',
                            );
            $data =  array('name' => $dataStoretName);

            $response = $this->cmService->updateCustomContextData($params, $data);

            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }
    }

    /**
     * @param string $insightName
     *
     * @return string insight id
     */
    public function createInsight($insightName)
    {
        // /insights/{insight_id}/datastores/{datastore_id}

        if (is_null($insightName) || empty($insightName)) {
            $this->logHelper->log('DEBUG', 'Insight name is needed to create an insight');

            return false;
        }

        $insightID = null;
        $insightID = $this->getInsightID($insightName);

        if ($insightID) {
            return $insightID;
        } else {
            $this->logHelper->log('INFO', 'Attempting creation of insight with name '.$insightName.'...');

            $params =  array(
                                'insights' => '',
                            );

            $data =  array('name' => $insightName);

            $response = $this->cmService->updateCustomContextData($params, $data);

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
                                'insights' => $insightID,
                                'insight_reports' => $reportID,
                                'insight_cards' => '',
                            );
            $data =  array('name' => $cardName);

            $response = $this->cmService->updateCustomContextData($params, $data);
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
                                'insights' => $insightID,
                                'insight_reports' => '',
                            );
            $data =  array('name' => $reportName);

            $response = $this->cmService->updateCustomContextData($params, $data);
           // echo "RESPONSE IS :";
           // var_dump($response);
            if ($response) {
                return $response->id;
            } else {
                return false;
            }
        }

    }
}
?>
