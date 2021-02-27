<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // $installer->getConnection()->dropColumn($installer->getTable('lof_affiliate_campaign'), 'group_id');
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            /**
             * Create table 'lof_affiliate_campaign_group'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_affiliate_campaign_group')
            )->addColumn(
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Campaign ID'
            )->addColumn(
                'group_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Group ID'
            );
            $installer->getConnection()->createTable($table);

            $eavTable = $installer->getTable('quote');

            $columns = [
                'affiliate_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'comment' => 'Affiliate Code'
                ],
                'campaign_code' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'comment' => 'Campaign Code'
                ],
                'affiliate_params' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Affiliate URI Params'
                ]
            ];
            $connection = $installer->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($eavTable, $name, $definition);
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $table = $installer->getTable('lof_affiliate_account');

            $installer->getConnection()->addColumn(
                $table,
                'bank_account_name',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Bank Account Holder Name'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'bank_account_number',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 60,
                    'nullable' => true,
                    'comment'  => 'Bank Account Number'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'swift_code',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 60,
                    'nullable' => true,
                    'comment'  => 'Bank Swift Code'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'bank_name',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Bank Name In Full'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'bank_branch_city',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 100,
                    'nullable' => true,
                    'comment'  => 'Bank Branch City'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'bank_branch_country_code',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Bank Branch Country'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'intermediary_bank_code',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Intermediary Bank Code'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'intermediary_bank_name',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Intermediary Bank Name'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'intermediary_bank_city',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Intermediary Bank City'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'intermediary_bank_country_code',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Intermediary Bank Country Code'
                ]
            );

            $withdraw_table = $installer->getTable('lof_affiliate_withdraw');

            $installer->getConnection()->addColumn(
                $withdraw_table,
                'paypal_email',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'Paypal Account Email'
                ]
            );

            $installer->getConnection()->addColumn(
                $withdraw_table,
                'banktransfer_data',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '64k',
                    'nullable' => true,
                    'comment'  => 'Bank Data Information'
                ]
            );

            $installer->getConnection()->addColumn(
                $withdraw_table,
                'other_payment_data',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '64k',
                    'nullable' => true,
                    'comment'  => 'Other Payment Data Information'
                ]
            );

            $installer->getConnection()->addColumn(
                $withdraw_table,
                'transaction_data',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => '64k',
                    'nullable' => true,
                    'comment'  => 'Payment transaction data'
                ]
            );

            $installer->getConnection()->addColumn(
                $withdraw_table,
                'attachment',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'   => 150,
                    'nullable' => true,
                    'comment'  => 'document attachment'
                ]
            );
            $installer->getConnection()->addColumn(
                $withdraw_table,
                'date_update',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Date Update'
                ]
            );
            $installer->getConnection()->addColumn(
                $withdraw_table,
                'date_paid',
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'length'   => 255,
                    'nullable' => true,
                    'comment'  => 'Date Paid'
                ]
            );
        }

        $installer->endSetup();
    }
}