<?php

namespace Arcanys\SSOAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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

    public function acsAction(Request $req)
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
        $session = $this->get('session');

        $session->getFlashBag()->set('arcanys_sso_auth.user_data', $auth->getAttributes());
        $session->getFlashBag()->set('arcanys_sso_auth.name_id', $auth->getNameId());
        $session->getFlashBag()->set('arcanys_sso_auth.session_index', $auth->getSessionIndex());

        if ($req->request->get('RelayState') && \OneLogin_Saml2_Utils::getSelfURL() != $req->request->get('RelayState')) {
            // $auth->redirectTo($req->request->get('RelayState'));
            return $this->redirect($req->request->get('RelayState'));
        }
    }

    public function slsAction(Request $req)
    {
        $auth = $this->get('arcanys_sso_auth.saml2');
        $auth->processSLO();
        $errors = $auth->getErrors();
        if (empty($errors)) {
            $this->get('security.token_storage')->setToken(null);
            $req->getSession()->invalidate();
            return $this->redirect('/');
        } else {
            throw new \Exception(implode(', ', $errors));
        }
    }
}
