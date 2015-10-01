<?php namespace Arcanys\SSOAuthBundle\Security\User;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
class AuthenticationHandler implements LogoutSuccessHandlerInterface
{
    protected $saml2;
    public function __construct($saml2){
        $this->saml2 = $saml2;
    }
    public function onLogoutSuccess(Request $request)
    {
        // $this->get('security.context')->setToken(null);
        $request->getSession()->invalidate();
        // $this->saml2->logout();
        $referer = $request->headers->get('referer','/');
        return new RedirectResponse($referer);
    }
}
