<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TwoFactorAuth\Api\AdminTokenServiceInterface;
use Magento\Framework\Authorization\PolicyInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Webapi\Exception as WebapiException;
use Magento\Integration\Model\AdminTokenService;
use Magento\TwoFactorAuth\Api\Data\AdminTokenResponseInterface;
use Magento\TwoFactorAuth\Api\Data\AdminTokenResponseInterfaceFactory;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;

/**
 * Handles the 2fa version of the admin access token service
 */
class AdminAccessTokenService implements AdminTokenServiceInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var PolicyInterface
     */
    private $auth;

    /**
     * @var UserAuthenticator
     */
    private $userAuthenticator;

    /**
     * @var AdminTokenService
     */
    private $adminTokenService;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

    /**
     * @var AdminTokenResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * @param TfaInterface $tfa
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param PolicyInterface $auth
     * @param UserAuthenticator $userAuthenticator
     * @param AdminTokenService $adminTokenService
     * @param RequestThrottler $requestThrottler
     * @param AdminTokenResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        TfaInterface $tfa,
        UserConfigRequestManagerInterface $configRequestManager,
        PolicyInterface $auth,
        UserAuthenticator $userAuthenticator,
        AdminTokenService $adminTokenService,
        RequestThrottler $requestThrottler,
        AdminTokenResponseInterfaceFactory $responseFactory
    ) {
        $this->tfa = $tfa;
        $this->configRequestManager = $configRequestManager;
        $this->auth = $auth;
        $this->userAuthenticator = $userAuthenticator;
        $this->adminTokenService = $adminTokenService;
        $this->requestThrottler = $requestThrottler;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Create access token for admin given the admin credentials.
     *
     * @param string $username
     * @param string $password
     * @return \Magento\TwoFactorAuth\Api\Data\AdminTokenResponseInterface
     */
    public function createAdminAccessToken(string $username, string $password): AdminTokenResponseInterface
    {
        try {
            $user = $this->userAuthenticator->authenticateWithCredentials($username, $password);
        } catch (\InvalidArgumentException $e) {
            $this->requestThrottler->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_ADMIN);
            throw new AuthenticationException(
                __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                )
            );
        }
        $this->requestThrottler->resetAuthenticationFailuresCount($username, RequestThrottler::USER_TYPE_ADMIN);

        $userId = (int)$user->getId();

        if (!$this->configRequestManager->isConfigurationRequiredFor($userId)) {
            return $this->responseFactory->create(
                [
                    'data' => [
                        AdminTokenResponseInterface::USER_ID => $userId,
                        AdminTokenResponseInterface::MESSAGE =>
                            (string)__('Please use the 2fa provider-specific endpoints to obtain a token.'),
                        AdminTokenResponseInterface::ACTIVE_PROVIDERS => array_filter(
                            $this->tfa->getUserProviders($userId),
                            function ($provider) use ($userId) {
                                return $provider->isActive($userId) ? $provider : null;
                            }
                        ),
                    ]
                ]
            );
        } elseif (empty($this->tfa->getUserProviders($userId))) {
            // It is expected that available 2fa providers are selected via db or admin ui
            throw new WebapiException(
                __('Please ask an administrator with sufficient access to configure 2FA first')
            );
        }

        try {
            $this->configRequestManager->sendConfigRequestTo($user);
        } catch (AuthorizationException|NotificationExceptionInterface $exception) {
            throw new WebapiException(
                __('Failed to send the message. Please contact the administrator')
            );
        }

        return $this->responseFactory->create(
            [
                'data' => [
                    AdminTokenResponseInterface::USER_ID => $userId,
                    AdminTokenResponseInterface::MESSAGE =>
                        (string)__('You are required to configure personal Two-Factor Authorization in order to login. '
                            . 'Please check your email.'),
                    AdminTokenResponseInterface::ACTIVE_PROVIDERS => array_filter(
                        $this->tfa->getUserProviders($userId),
                        function ($provider) use ($userId) {
                            return $provider->isActive($userId) ? $provider : null;
                        }
                    )
                ]
            ]
        );
    }
}