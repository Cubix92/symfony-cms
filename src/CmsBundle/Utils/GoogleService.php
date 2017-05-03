<?php

namespace CmsBundle\Utils;

class GoogleService
{
    /**
     * @param $clientSecrets
     * @param $accessToken
     * @param bool $redirectUri
     *
     * @return \Google_Client
     */
    public function prepareClient($clientSecrets) {
        $client = new \Google_Client();
        try {
            $client->setAuthConfig($clientSecrets);
        } catch (\Exception $e) {
            return false;
        }

        $client->addScope(\Google_Service_Analytics::ANALYTICS_READONLY);

        return $client;
    }

    /**
     * @param $client
     *
     * @return mixed
     */
    public function fetchData($client)
    {
        $analytics = new \Google_Service_Analytics($client);

        try {
            $profileId = $this->getFirstProfileId($analytics);
        } catch(\Exception $e) {
            return false;
        }

        $results = array_merge(
            $this->fetchBoxChartData($analytics, $profileId),
            $this->fetchBarChartData($analytics, $profileId)
        );

        if($results) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * @param $analytics
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function getFirstProfileId($analytics) {
        try {
            $accounts = $analytics->management_accounts->listManagementAccounts();
        } catch(\Exception $e) {
            $json = json_decode($e->getMessage());
            throw new \Exception($json->error->message);
        }

        if (count($accounts->getItems()) > 0) {
            $items = $accounts->getItems();
            $firstAccountId = $items[0]->getId();

            $properties = $analytics->management_webproperties
                ->listManagementWebproperties($firstAccountId);

            if (count($properties->getItems()) > 0) {
                $items = $properties->getItems();
                $firstPropertyId = $items[0]->getId();

                $profiles = $analytics->management_profiles
                    ->listManagementProfiles($firstAccountId, $firstPropertyId);

                if (count($profiles->getItems()) > 0) {
                    $items = $profiles->getItems();

                    return $items[0]->getId();

                } else {
                    throw new \Exception('Nie znaleziono żadnych widoków dla wybranego użytkownika.');
                }
            } else {
                throw new \Exception('Nie znaleziono żadnych parametrów dla wybranego użytkownika.');
            }
        } else {
            throw new \Exception('Wybrany użytkownik nie posiada żadnego konta.');
        }
    }

    /**
     * @param $analytics
     * @param $profileId
     *
     * @return mixed
     */
    private function fetchBoxChartData($analytics, $profileId) {
        $analyticsResults = $analytics->data_ga->get(
            'ga:' . $profileId,
            '7daysAgo',
            'today',
            'ga:bounceRate,ga:avgSessionDuration,ga:newUsers,ga:sessionsPerUser'
        );

        $boxData = $analyticsResults->getRows();

        $prepareData = array(
            'bounceRate' => $boxData[0][0],
            'avgSessionDuration' => $boxData[0][1],
            'newUsers' => $boxData[0][2],
            'sessionsPerUser' => $boxData[0][3]
        );

        return $prepareData;
    }

    /**
     * @param $analytics
     * @param $profileId
     *
     * @return mixed
     */
    private function fetchBarChartData($analytics, $profileId) {
        $analyticsResults = $analytics->data_ga->get(
            'ga:' . $profileId,
            '7daysAgo',
            'today',
            'ga:sessions,ga:users',
            array('dimensions' => 'ga:date')
        );

        $barData = $analyticsResults->getRows();
        $prepareData = array();

        if (count($barData) > 0) {

            foreach($barData as $row) {
                $prepareData['usersAndSessions'][$row[0]] = array(
                    'sessions' => $row[1],
                    'users' => $row[2],
                );
            }

        }

        return $prepareData;
    }

}