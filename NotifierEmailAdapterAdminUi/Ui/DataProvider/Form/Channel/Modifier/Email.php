<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEmailAdapterAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;
use Magento\NotifierEmailAdapter\Model\AdapterEngine\Email as EmailAdapter;

class Email implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        $meta['configuration'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('Email Configuration'),
                        'collapsible' => false,
                    ],
                ],
            ],
            'children' => [
                'from' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('From'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 10,
                                'dataScope' => 'general.configuration.from',
                                'validation' => [
                                    'required-entry' => true,
                                    'validate-email' => true,
                                ],
                            ],
                        ],
                    ],
                ],

                'from_name' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('From Name'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 20,
                                'dataScope' => 'general.configuration.from_name',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],

                'to' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Email'),
                                'notice' => __('Write one recipient per line if multiple recipients are required'),
                                'dataType' => 'text',
                                'formElement' => 'textarea',
                                'sortOrder' => 30,
                                'dataScope' => 'general.configuration.to',
                                'validation' => [
                                    'required-entry' => true,
                                    'magento-validate-emails' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterCode(): string
    {
        return EmailAdapter::ADAPTER_CODE;
    }
}
