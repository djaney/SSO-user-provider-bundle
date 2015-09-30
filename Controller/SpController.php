<?php

namespace Arcanys\SSOAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
class SpController extends Controller
{
    public function metadataAction()
    {
        $response = new Response( $this->get('arcanys_sso_auth.saml2_metadata_facade')->getMetadata() );
        $response->headers->set('Content-Type', 'xml');
        return $response;
    }

    public function acsAction()
    {
        $auth = $this->get('arcanys_sso_auth.saml2');
        $auth->processResponse();

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }

        if (!$auth->isAuthenticated()) {
            throw new AccessDeniedHttpException();
        }

        $_SESSION['samlUserdata'] = $auth->getAttributes();
        $_SESSION['samlNameId'] = $auth->getNameId();
        $_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
        if (isset($_POST['RelayState']) && \OneLogin_Saml2_Utils::getSelfURL() != $_POST['RelayState']) {
            $auth->redirectTo($_POST['RelayState']);
        }
        exit;
    }

    public function slsAction()
    {
        $auth->processSLO();
        $errors = $auth->getErrors();
        if (empty($errors)) {
            die('Tarung ug logout SpController');
        } else {
            throw new \Exception(implode(', ', $errors));
        }
    }
}
