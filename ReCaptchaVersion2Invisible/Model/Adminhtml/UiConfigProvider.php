<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion2Invisible\Model\Adminhtml;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaUi\Model\UiConfigProviderInterface;

/**
 * @inheritdoc
 */
class UiConfigProvider implements UiConfigProviderInterface
{
    private const XML_PATH_PUBLIC_KEY = 'recaptcha_backend/type_invisible/public_key';
    private const XML_PATH_POSITION = 'recaptcha_backend/type_invisible/position';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        $config = [
            'rendering' => [
                'sitekey' => $this->getPublicKey(),
                'badge' => $this->getInvisibleBadgePosition(),
                'size' => 'invisible'
            ],
            'invisible' => true,
        ];
        return $config;
    }

    /**
     * Get Google API Website Key
     *
     * @return string
     */
    private function getPublicKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PUBLIC_KEY));
    }

    /**
     * Get Invisible Badge Position
     *
     * @return string
     */
    private function getInvisibleBadgePosition(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POSITION
        );
    }
}
