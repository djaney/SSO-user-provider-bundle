<?php namespace Arcanys\SSOAuthBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Arcanys\SSOAuthBundle\Service\Saml2;
class SSOAuthenticator implements SimplePreAuthenticatorInterface
{
    private $saml2;
    private $session;
    public function __construct(Saml2 $saml2,$session){
        $this->saml2 = $saml2;
        $this->session = $session;
    }
    public function createToken(Request $request, $providerKey)
    {
        // TODO so sometthing with this later
        $secret = 'kawatan ug baboy';
        return new PreAuthenticatedToken(
            'anon.',
            $secret,
            $providerKey
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {


        $secret = $token->getCredentials();
        $userData = $this->session->getFlashBag()->get('arcanys_sso_auth.user_data');
        if($userData){
            // TODO create mapping config in the future
            $username = reset($userData['uid']);
            $email = reset($userData['email']);
            $firstname = reset($userData['firstname']);
            $lastname = reset($userData['lastname']);
            $roles = $userData['rights'];
            if(!$roles) $roles = ['ROLE_USER'];
        }else{
            $this->saml2->login();
            exit;
        }


        if (!$username) {
            throw new AuthenticationException("Failed to authenticate from SSO");
        }

        $user = $userProvider->loadUserByUsername([
            'username'=>$username,
            'email'=>$email,
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            'roles'=>$roles,
        ]);

        return new PreAuthenticatedToken(
            $user,
            $secret,
            $providerKey,
            $user->getRoles($roles)
        );
    }


}
