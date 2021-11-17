<?php

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenDecoder
{
    private JWTEncoderInterface $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Extract the user from a request by decoding the jwt token.
     * @param Request $request
     * @return bool|array
     */
    public function getUserFromHeader(Request $request): bool|array
    {
        try
        {
            $authorizationHeader = $request->headers->get('Authorization');

            list(,$token) = explode(' ',$authorizationHeader);

            $user = $this->jwtEncoder->decode($token);
        }
        catch (JWTDecodeFailureException $e)
        {
            return $e->getMessage();
        }

        return $user;
    }
}