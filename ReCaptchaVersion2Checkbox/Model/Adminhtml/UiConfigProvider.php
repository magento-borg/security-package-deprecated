<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion2Checkbox\Model\Adminhtml;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaUi\Model\UiConfigProviderInterface;

/**
 * @inheritdoc
 */
class UiConfigProvider implements UiConfigProviderInterface
{
    private const XML_PATH_PUBLIC_KEY = 'recaptcha_backend/type_recaptcha/public_key';
    private const XML_PATH_SIZE = 'recaptcha_backend/type_recaptcha/size';
    private const XML_PATH_THEME = 'recaptcha_backend/type_recaptcha/theme';
    private const XML_PATH_LANGUAGE_CODE = 'recaptcha_backend/type_recaptcha/lang';

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
                'theme' => $this->getTheme(),
                'size' => $this->getSize(),
                'lang' => $this->getLanguageCode(),
            ],
            'invisible' => false,
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
     * Get size
     *
     * @return string
     */
    private function getSize(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SIZE
        );
    }

    /**
     * Get theme
     *
     * @return string
     */
    private function getTheme(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_THEME
        );
    }

    /**
     * Get language code
     *
     * @return string
     */
    private function getLanguageCode(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_LANGUAGE_CODE
        );
    }
}
