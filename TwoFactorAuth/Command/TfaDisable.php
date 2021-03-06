<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Command;

use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;

/**
 * Disable 2FA commandline
 */
class TfaDisable extends Command
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * @param ConfigInterface $config
     * @param Manager $cacheManager
     */
    public function __construct(
        ConfigInterface $config,
        Manager $cacheManager
    ) {
        parent::__construct();
        $this->config = $config;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('security:tfa:disable');
        $this->setDescription('Globally disable two factor auth');

        parent::configure();
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->config->saveConfig(
            TfaInterface::XML_PATH_ENABLED,
            '0',
            'default',
            0
        );

        $this->cacheManager->flush(['config']);
    }
}
