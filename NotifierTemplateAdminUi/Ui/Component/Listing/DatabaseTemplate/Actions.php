<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateAdminUi\Ui\Component\Listing\DatabaseTemplate;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

class Actions extends Column
{
    /**
     * @inheritdoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $id = $item['template_id'];

                $item[$name]['edit'] = [
                    'href' => $this->getContext()->getUrl('magento_notifier_template/databasetemplate/edit', [
                        'template_id' => $id
                    ]),
                    'label' => __('Edit')
                ];

                $item[$name]['delete'] = [
                    'href' => $this->getContext()->getUrl('magento_notifier_template/databasetemplate/delete', [
                        'template_id' => $id
                    ]),
                    'label' => __('Delete')
                ];
            }
        }

        return $dataSource;
    }
}
