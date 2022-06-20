<?php
/**
 * WHMCS Sitejet by Plesk Provisioning Module
 * Version 1.1
 * (C) 2022 Plesk International GmbH
**/

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'kaclient.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'langhelper.php';

use WHMCS\Module\Server\PleskSitejet\KAApiClient;
use WHMCS\Module\Server\PleskSitejet\LangHelper;

const PARAM_USERNAME = 'configoption1';
const PARAM_PASSWORD = 'configoption2';

function sitejet_MetaData()
{
    return array(
        'DisplayName' => 'Sitejet by Plesk',
        'APIVersion' => '1.1',
        'RequiresServer' => false,
        'ServiceSingleSignOnLabel' => false,
    );
}

function sitejet_ConfigOptions()
{
    return array(
        // configoption1
        'Plesk KA Username' => array(
            'Type' => 'text',
            'Size' => '128',
            'Default' => '',
            'Description' => '',
        ),
        // configoption2
        'Plesk KA Password' => array(
            'Type' => 'password',
            'Size' => '64',
            'Default' => '',
            'Description' => '',
        ),
    );
}

function sitejet_ClientArea(array $params)
{
    $keyId = $params['customfields']['keyId'];
    $uid = $params['customfields']['activationInfoUid'];
    $activated = $params['customfields']['activationInfoActivated'];
    $activationLink = $params['customfields']['activationLink'];
    $terminated = $params['model']->serviceProperties->get('terminated');
    $suspended = $params['model']->serviceProperties->get('suspended');
    $agency = (strpos($params['configoptions']['Package'], 'Agency') !== false);
    $websites = $params['configoptions']['Additional websites'];

    try 
    {
        if (!$activated) {
            $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
            $result = $api->retrieve($keyId);
            $activationLink = $result['keyIdentifiers']['activationLink'];
            $activated = $result['activationInfo']['activated'];
            $uid = $result['activationInfo']['uid'];
            $terminated = $result['terminated'];
            $suspended = $result['suspended'];
            $params['model']->serviceProperties->save(['activationLink' => $activationLink,
                                                       'activationInfoUid' => $uid,
                                                       'activationInfoActivated' => $activated,
                                                       'terminated' => $terminated,
                                                       'suspended' => $suspended
                                                      ]);
        }          
        
        $langHelper = new LangHelper($_SESSION['Language']);

        $package = $langHelper->getLangValue('label_package_business', 'Business');
        if ($agency) {
            $package = $langHelper->getLangValue('label_package_agency', 'Agency');
        }
        
        $returnHtml = '<div class="tab-content"><div class="row"><div class="col-sm-1"><strong>' . $langHelper->getLangValue('label_features', 'Features') . '</strong></div></div><div class="row"><div class="col-sm-1">' . $langHelper->getLangValue('label_package', 'Package') . '</div><div class="col-sm-4 text-right">' . $package . '</div></div><div class="row"><div class="col-sm-1">' . $langHelper->getLangValue('label_additional_websites', 'Additional websites') . '</div><div class="col-sm-4 text-right">' . $websites . '</div></div>';
        if ($activated) {
            $returnHtml = $returnHtml . '<div class="row"><div class="col-sm-3 text-left">' . $langHelper->getLangValue('button_license_activated', 'License activated') . '</div></div>';
        }
        $returnHtml = $returnHtml . '</div><br/>';

        if (!$terminated && !$suspended) {
            if (!$activated) {
                $returnHtml = $returnHtml . '<div class="tab-content"><a class="btn btn-block btn-info" href="' . $activationLink . '" target="_blank">' . $langHelper->getLangValue('button_activate_license', 'Activate license') . '</a></div><br/>';
            } 
            $returnHtml = $returnHtml . '<div class="tab-content"><a class="btn btn-block btn-default" href="' . $langHelper->getLangValue('dashboard_url', 'https://partner.sitehub.io/') . '" target="_blank">' . $langHelper->getLangValue('button_dashboard', 'Sitejet Dashboard') . '</a></div><br/>';
        }

        return $returnHtml;

    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    
    return $returnHtml;
}

function sitejet_CreateAccount(array $params)
{
    try {
        $username = $params['configoption1'];
        $password = $params['configoption2'];
        $agency = (strpos($params['configoptions']['Package'], 'Agency') !== false);
        $websites = $params['configoptions']['Additional websites'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->create($websites, $agency);

        sitejet_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function sitejet_SuspendAccount(array $params)
{
    try {
        $username = $params['configoption1'];
        $password = $params['configoption2'];
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->suspend('true', $keyId);

        sitejet_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function sitejet_UnsuspendAccount(array $params)
{
    try {
        $username = $params['configoption1'];
        $password = $params['configoption2'];
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->suspend('false', $keyId);

        sitejet_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';}

function sitejet_TerminateAccount(array $params)
{
    try {
        $username = $params['configoption1'];
        $password = $params['configoption2'];
        $keyId = $params['customfields']['keyId'];

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->delete($keyId);

        sitejet_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function sitejet_ChangePackage(array $params)
{
    try {
        $username = $params['configoption1'];
        $password = $params['configoption2'];
        $keyId = $params['customfields']['keyId'];
        $agency = (strpos($params['configoptions']['Package'], 'Agency') !== false);
        $websites = $params['configoptions']['Additional websites'];

        if (!isset($keyId)) {
            throw new Exception("No keyId found, up/downgrade failed!");
        }

        $api = new KAApiClient($params[PARAM_USERNAME], $params[PARAM_PASSWORD]);
        $result = $api->update($websites, $agency, $keyId);

        sitejet_UpdateModel($params, $result);
    } catch (Exception $e) {
        logModuleCall(
            'sitejet',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function sitejet_UpdateModel($params, $result) {
    $params['model']->serviceProperties->save(['ownerId' => $result['ownerId'],
                                               'keyId' => $result['keyIdentifiers']['keyId'],
                                               'keyNumber' => $result['keyIdentifiers']['keyNumber'],
                                               'activationCode' => $result['keyIdentifiers']['activationCode'],
                                               'activationLink' => $result['keyIdentifiers']['activationLink'],
                                               'activationInfoUid' => $result['activationInfo']['uid'],
                                               'activationInfoActivated' => $result['activationInfo']['activated'],
                                               'status' => $result['status'],
                                               'terminated' => $result['terminated'],
                                               'suspended' => $result['suspended']
                                            ]);
}