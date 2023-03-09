<?php
/**
 * WHMCS Sitejet by Plesk Provisioning Module
 * KA Client
 * (C) 2022 Plesk International GmbH
**/

namespace WHMCS\Module\Server\PleskSitejet;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'curlhelper.php';

use WHMCS\Module\Server\PleskSitejet\CURLHelper as CURLHelper;

class KAApiClient {

    const API_URL_BASE = 'https://api.central.plesk.com/30/keys';
    const JSON_CONTENT_TYPE = 'application/json';

    const SJ_BUSINESS_SKU = 'SJ-PLSK-BIZ-1M';
    const SJ_BUSINESS_SITES_SKU = 'SJ-PLSK-BIZ-SITE-1M';
    const SJ_AGENCY_SKU = 'SJ-PLSK-ACY-1M';
    const SJ_AGENCY_SITES_SKU = 'SJ-PLSK-ACY-SITE-1M';

    protected $results = array();
    private $username;
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function isAgency($keyId)
    {
        $license = $this->retrieve($keyId);
        return ($license['items'][0]['item'] == self::SJ_AGENCY_SKU) || ($license['items'][0]['item'] == self::SJ_AGENCY_SITES_SKU) ;
    }

    public function create($websites, $agency)
    {
        $postData = json_encode(array(
            'items' => array(
                array(
                    'item' => $this->getBaseSKU($agency)
                ),
                array(
                    'item' => $this->getWebsitesSKU($agency),
                    'quantity' => $websites
                ),
            ),
        ));

        $ch =  CURLHelper::preparePOST(self::API_URL_BASE.'?return-key-state=yes&retailer=whmcs-ka', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'sitejet',
                'create',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function retrieve($keyId)
    {
        $ch =  CURLHelper::prepareGET(self::API_URL_BASE.'/'.$keyId, $this->username, $this->password, self::JSON_CONTENT_TYPE);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'sitejet',
                'update',
                $keyId,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function update($websites, $agency, $keyId)
    {
        $postData = json_encode(array(
            'items' => array(
                array(
                    'item' => $this->getBaseSKU($agency)
                ),
                array(
                    'item' => $this->getWebsitesSKU($agency),
                    'quantity' => $websites
                ),
            ),
        ));

        $ch =  CURLHelper::preparePUT(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'sitejet',
                'update',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function suspend($suspended, $keyId)
    {
        $postData = json_encode(array('suspended' => $suspended));

        $ch =  CURLHelper::preparePUT(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password, self::JSON_CONTENT_TYPE, $postData);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'sitejet',
                'suspend',
                $postData,
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    public function delete($keyId)
    {
        $ch =  CURLHelper::prepareDELETE(self::API_URL_BASE.'/'.$keyId.'?return-key-state=yes', $this->username, $this->password);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('Connection Error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);

        $this->results = $this->processResponse($response);

        if (defined("WHMCS")) {
            logModuleCall(
                'sitejet',
                'delete',
                '',
                $response,
                $this->results,
                array(
                )
            );
        }

        if ($this->results === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Bad response received from API');
        }

        return $this->results;
    }

    private function getBaseSKU($agency)
    {
        if($agency){
            return self::SJ_AGENCY_SKU;
        } else {
            return self::SJ_BUSINESS_SKU;
        }
    }

    private function getWebsitesSKU($agency)
    {
        if($agency){
            return self::SJ_AGENCY_SITES_SKU;
        } else {
            return self::SJ_BUSINESS_SITES_SKU;
        }
    }

    public function processResponse($response)
    {
        return json_decode($response, true);
    }

}
