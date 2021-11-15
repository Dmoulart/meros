<?php

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class JWTAuthenticator extends JWTTokenAuthenticator
{
    private FirewallMap $firewallMap;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        TokenExtractorInterface $tokenExtractor,
        TokenStorageInterface $tokenStorage,
        FirewallMap $firewallMap
    ) {
        parent::__construct($jwtManager, $dispatcher, $tokenExtractor,$tokenStorage);

        $this->firewallMap = $firewallMap;
    }

    public function getCredentials(Request $request)
    {
        try {
            return parent::getCredentials($request);
        } catch (AuthenticationException $e) {
            $firewall = $this->firewallMap->getFirewallConfig($request);
            // if anonymous is allowed, do not throw error
            if ($firewall->allowsAnonymous()) {
                return;
            }
            throw $e;
        }
    }
}