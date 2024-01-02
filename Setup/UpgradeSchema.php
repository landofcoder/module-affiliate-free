<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.comlicense-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
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

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $installer->getTable('lof_affiliate_campaign');
            $referTable = $installer->getTable('lof_affiliate_referingcustomer');

            $installer->getConnection()->addColumn(
                $table,
                'conditions_serialized',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 3500,
                    'nullable' => true,
                    'comment' => 'conditions serialized'
                ]
            );

            $installer->getConnection()->addColumn(
                $referTable,
                'campaign_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'campaign code'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $table = $installer->getTable('lof_affiliate_account');
            $installer->getConnection()->addColumn(
                $table,
                'is_active',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '1',
                    'comment' => 'Is Active Affiliate Account'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'state',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 100,
                    'nullable' => false,
                    'default' => 'active',
                    'comment' => 'State of affiliate account: pending, active, reviewing, declided'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $table = $installer->getTable('lof_affiliate_account');
            $installer->getConnection()->addColumn(
                $table,
                'campaign_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Default Campaign Code For Affiliate Account'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $tableAffiliateAccount = $installer->getTable('lof_affiliate_account');
            $tableAffiliateReferCustomer = $installer->getTable('lof_affiliate_referingcustomer');
            $tableAffiliateTransaction = $installer->getTable('lof_affiliate_transaction');
            $installer->getConnection()->addColumn(
                $tableAffiliateAccount,
                'times_used_refer_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Times Used For Refer Code'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableAffiliateReferCustomer,
                'expiry_date_commission',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Expiry Date Commission'
                ]
            );
            $installer->getConnection()->addColumn(
                $tableAffiliateAccount,
                'default_payment_method',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Default Payment Method'
                ]
            );
            $installer->getConnection()->addColumn(
                $tableAffiliateAccount,
                'auto_payment_balance_reaches',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Auto Payment Balance Reaches'
                ]
            );
            $installer->getConnection()->addColumn(
                $tableAffiliateAccount,
                'withdrawal_auto',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Withdrawal Auto'
                ]
            );
            $installer->getConnection()->addColumn(
                $tableAffiliateAccount,
                'reserve_level',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Reserve Level (To Be Kept In Account)'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableAffiliateTransaction,
                'customer_ip',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Customer Ip'
                ]
            );

            $installer->getConnection()->addColumn(
                $tableAffiliateTransaction,
                'reason',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 500,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Reason'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $table = $installer->getTable('lof_affiliate_campaign');
            $installer->getConnection()->addColumn(
                $table,
                'referred_customer_discount',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'comment' => 'Referred Customer Discount for Subsequent Purchase'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $table = $installer->getTable('lof_affiliate_commission');
            $installer->getConnection()->addColumn(
                $table,
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 4,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'store id'
                ]
            );
        }
        $installer->endSetup();
    }
}