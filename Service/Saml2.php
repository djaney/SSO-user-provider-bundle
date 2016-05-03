<?php namespace Arcanys\SSOAuthBundle\Service;

class Saml2 extends \OneLogin_Saml2_Auth{
    public function __construct($config){

        $settings = array (
            'sp' => array (
                'entityId' => $config['sp']['entity_id'],
                'assertionConsumerService' => array (
                    'url' => $config['sp']['acs'],
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ),
                'singleLogoutService' => array (
                    'url' => $config['sp']['sls'],
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:unspecified',
                // 'x509cert' => $config['sp']['cert'],
                // 'privateKey' > $config['sp']['private_key'],
            ),

            'idp' => array (
                'entityId' => $config['idp']['entity_id'],
                'singleSignOnService' => array (
                    'url' => $config['idp']['single_signon_service'],
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'singleLogoutService' => array (
                    'url' => $config['idp']['single_logout_service'],
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ),
                'x509cert' => $config['idp']['cert'],
            ),
        );
        parent::__construct($settings);
    }
    public function logout($returnTo = null, $parameters = array(), $nameId = null, $sessionIndex = null, $stay=false){
        parent::logout($returnTo, $parameters, $nameId, $sessionIndex, $stay);
    }
}
