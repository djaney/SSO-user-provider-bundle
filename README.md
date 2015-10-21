# Installation
1. Add to composer.json

        "require": {
            "arcanys/sso-auth-bundle": "dev-master"
        },
        "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:djaney/SSO-user-provider-bundle.git"
        }
        ]

1. Update Kernel

        new Arcanys\SSOAuthBundle\ArcanysSSOAuthBundle(),

1. Add configurations

        arcanys_sso_auth:
            user_provider:
                class: AppBundle\Entity\User
            sp:
                base_url: http://mywebsite.com/sp
            idp:
                entity_id: http://example.com/metadata.php
                single_signon_service: http://myidp.com/SSOService.php
                single_logout_service: http://myidp.com/SingleLogoutService.php
                cert: ~
1. Add Routes

        sso_endpoint:
            resource: "@ArcanysSSOAuthBundle/Resources/config/routing.yml"
            prefix:   /sp # service provider path
        logout:
            path: /logout
1. Add security configurations

        security:
            ...
            providers:
                sso_provider:
                    id: arcanys_sso_auth.user_provider
            ...
            firewalls:
                main:
                    anonymous: ~
                    pattern: ^(?!/sp/) #exclude service provider path
                    simple_preauth:
                        authenticator: arcanys_sso_auth.sso_authenticator
                    logout:
                        path:   /logout
                        success_handler: arcanys_sso_auth.authentication_handler

            access_control:
                - { path: ^/sp, roles: IS_AUTHENTICATED_ANONYMOUSLY  } # exclude service provider path
                - { path: ^/, roles: ROLE_USER } # require login on all pages
