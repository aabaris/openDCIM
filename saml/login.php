<?php

// Set a variable so that misc.inc.php knows not to throw us into an infinite redirect loop
$loginPage = true;

require_once('../db.inc.php');

define("TOOLKIT_PATH", '../vendor/onelogin/php-saml/');
require_once(TOOLKIT_PATH . '_toolkit_loader.php');

require_once('settings.php');

/**
 * Saml authentication
 */

if (!isset($_SESSION['samlUserdata'])) {
	$settings = new OneLogin_Saml2_Settings($saml_settings);
	$authRequest = new OneLogin_Saml2_AuthnRequest($settings);
	$samlRequest = $authRequest->getRequest();

	// Track the authRequest ID so the response can be validated
	$_SESSION['saml_req_id'] = $authRequest->getID();
	
	$parameters = array('SAMLRequest' => $samlRequest);

	if(isset($_COOKIE['targeturl'])){
		$relayto = html_entity_decode($_COOKIE['targeturl']);
	}
	$parameters['RelayState'] = (isset($relayto)?OneLogin_Saml2_Utils::getSelfURLhost().$relayto:OneLogin_Saml2_Utils::getSelfURLhost());

    	$idpData = $settings->getIdPData();
	$ssoUrl = $idpData['singleSignOnService']['url'];
	$url = OneLogin_Saml2_Utils::redirect($ssoUrl, $parameters, true);

	header("Location: $url");
    }
?>
