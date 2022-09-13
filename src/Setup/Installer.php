<?php

declare(strict_types=1);

namespace StwElleThemeChanges\Setup;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use StwElleThemeChanges\Setup\Helper;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Installer
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Installer constructor.
     *
     * @param Connection                $connection
     */

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(Connection $connection, ContainerInterface $container)
    {
        $this->connection = $connection;
        $this->helper = new Helper($connection);
        $this->container = $container;
    }

    public function createCustomerCustomFields()
    {
        $this->customFieldsForSalesChannel();
    }

    public function install()
    { 
        $this->createCustomerCustomFields();

        $elleCustomMenuIcons = $this->fetchCustomFieldSetId('elle_custom_menu_icons');
        if (!$elleCustomMenuIcons) {
            $repo = $this->container->get('custom_field_set.repository');
            $attributeSet = $this->helper->prepareElleCategoryCustomMenuIcons();
            $repo->create([$attributeSet], Context::createDefaultContext());
        }

        return false;

        
    }

    private function fetchCustomFieldSetId($technicalName)
    {
        $customFieldSetRepo = $this->container->get('custom_field_set.repository');

        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('custom_field_set.name', $technicalName));

        $result = $customFieldSetRepo->search($criteria, Context::createDefaultContext());

        $customFieldSetDetails = $result->first();

        return ($customFieldSetDetails) ? $customFieldSetDetails->getId() : null;
    }

    private function customFieldsForSalesChannel(): void
    {
        $customFieldSetId = $this->fetchCustomFieldSetId('elle_custom_footerSettings');
        if (!$customFieldSetId) {
            $repo = $this->container->get('custom_field_set.repository');
            $id = Uuid::randomHex();
            $attributeSet = [
                'id' => $id,
                'name' => 'elle_custom_footerSettings',
                'config' => ['label' => [
                    'en-GB' => 'Footer Snippets',
                    'de-DE' => 'Fußzeilenausschnitte',
                ]],
                'customFields' => [
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'elle_custom_footer_snippet_1',
                        'type' => 'text',
                        'config' => [
                            'label' => [
                                'en-GB' => 'footer snippet one',
                                'de-DE' => 'Fußzeilenausschnitt eins',
                            ],
                            'componentName' => 'sw-textarea-field',
                            'customFieldType' => 'textarea',
                            'customFieldPosition' => 1,
                        ],
                    ],
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'elle_custom_footer_snippet_2',
                        'type' => 'text',
                        'config' => [
                            'label' => [
                                'en-GB' => 'footer snippet two',
                                'de-DE' => 'Fußzeilenausschnitt zwei',
                            ],
                            'componentName' => 'sw-textarea-field',
                            'customFieldType' => 'textarea',
                            'customFieldPosition' => 2,
                        ],
                    ],
                    [
                        'id' => Uuid::randomHex(),
                        'name' => 'elle_custom_footer_snippet_3',
                        'type' => 'text',
                        'config' => [
                            'label' => [
                                'en-GB' => 'footer snippet three',
                                'de-DE' => 'Fußzeilenausschnitt drei',
                            ],
                            'componentName' => 'sw-textarea-field',
                            'customFieldType' => 'textarea',
                            'customFieldPosition' => 3,
                        ],
                    ],
                ],
                'relations' => [
                    [
                        'entityName' => 'sales_channel',
                    ],
                ],
            ];
            $result = $repo->create([$attributeSet], Context::createDefaultContext());
        }
    }

}
