<?php namespace Arcanys\SSOAuthBundle\Service;

class Saml2MetadataFacade extends \OneLogin_Saml2_Settings{
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

        );
        parent::__construct($settings, true);
    }
    public function getMetadata(){
        $metadata = $this->getSPMetadata();
        $errors = $this->validateMetadata($metadata);
        if (empty($errors)) {
            return $metadata;
        } else {
            throw new \OneLogin_Saml2_Error(
                'Invalid SP metadata: '.implode(', ', $errors),
                OneLogin_Saml2_Error::METADATA_SP_INVALID
            );
        }
    }
}
