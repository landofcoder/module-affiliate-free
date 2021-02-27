<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_commission'));

        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_campaign'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_store'));

        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_group'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_banner'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_account'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_referingcustomer'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_transaction'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_lead'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_ppc'));
        $setup->getConnection()->dropTable($setup->getTable('lof_affiliate_withdraw'));


        $eavTable = $installer->getTable('sales_order');

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


        /** 
         * table lof_affiliate_commission
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_commission')
        )
        ->addColumn(
            'commission_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'commission_id'
        ) 
        ->addColumn(
            'campaign_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'campaign_code'
        )
        ->addColumn(
            'affiliate_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'affiliate_code'
        )
        ->addColumn(
            'affiliate_params',
             \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )
        ->addColumn(
            'commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid'
        )
        ->addColumn(
            'order_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'order_total'
        )
        ->addColumn(
            'price_order_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'price_order_total'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'create_at'
        )
        ->setComment(
            'Commission Affiliate - Commission Table'
        );
        $installer->getConnection()->createTable($table);

        /** 
         * table lof_affiliate_campaign
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_campaign')
        )
        ->addColumn(
            'campaign_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'campaign_id'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'description'
        )
        ->addColumn(
            'display',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Display - Allow guest or only affiliate'
        )
        ->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'from_date'
        )
        ->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'to_date'
        )
        ->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'sort_order'
        )
        ->addColumn(
            'discount_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'discount_action'
        )
        ->addColumn(
            'discount_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'discount_amount'
        )
        ->addColumn(
            'apply_to_shipping',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'apply_to_shipping'
        )
        ->addColumn(
            'discount_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'discount_description'
        )
        ->addColumn(
            'conditions',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'conditions'
        )
        ->addColumn(
            'commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'commission'
        )
        ->addColumn(
            'tracking_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'tracking_code'
        )
        ->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'group_id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'store_id'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'status'
        )
        ->addColumn(
            'enable_ppl',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Enable Pay Per Lead'
        )
        ->addColumn(
            'signup_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission for signup new account'
        )
        ->addColumn(
            'subscribe_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission for Subscribe newsletter'
        )
        ->addColumn(
            'limit_account',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Limit quantity of signup new account'
        )
        ->addColumn(
            'limit_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Limit balance for pay per lead'
        )
        ->addColumn(
            'limit_account_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Limit account created from same IP address'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'create_at'
        )
        ->setComment(
            'Campaign - Campaign Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'lof_affiliate_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_store')
        )->addColumn(
            'campaign_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'campaign_id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('lof_affiliate_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('lof_affiliate_store', 'campaign_id', 'lof_affiliate_campaign', 'campaign_id'),
            'campaign_id',
            $installer->getTable('lof_affiliate_campaign'),
            'campaign_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lof_affiliate_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'CMS Page To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * [$table lof_affiliate_group]
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_group')
        )
        ->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Group ID'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Status'
        )
        ->addColumn(
            'commission_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'commission_action'
        )
        ->addColumn(
            'commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission'
        )
        ->addColumn(
            'enable_ppl',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Enable Commission For Each Success Order'
        )
        ->addColumn(
            'commission_ppl_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'commission_ppl_action'
        )
        ->addColumn(
            'commission_ppl',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission For PPL'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'create_at'
        )
        ->setComment(
            'Group - Group Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * table lof_affiliate_banner
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_banner')
        )
        ->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Banner ID'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )
        ->addColumn(
            'link',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Link'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type'
        )
        ->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'image'
        )
        ->addColumn(
            'rel_nofollow',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'rel_nofollow'
        )
        ->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'width'
        )
        ->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'height'
        )
        ->addColumn(
            'click_raw',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'clicks raw'
        )
        ->addColumn(
            'click_unique',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'clicks unique'
        )
        ->addColumn(
            'click_unique_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid For Each Unique Click'
        )
        ->addColumn(
            'click_raw_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid For Each Click'
        )
        ->addColumn(
            'expense',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Expense'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'status'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'create_at'
        )
        ->setComment(
            'Banner - Banner Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_account')
        )
        ->addColumn(
            'accountaffiliate_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Account Affiliate ID'
        )
        ->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'First Name'
        )
        ->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Last Name'
        )
        ->addColumn(
            'fullname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Full Name'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Affiliate Email'
        )
        ->addColumn(
            'tracking_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Tracking Code'
        )
        ->addColumn(
            'paypal_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Paypal Email'
        )
        ->addColumn(
            'skrill_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Skrill Email'
        )
        ->addColumn(
            'balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Balance'
        )
        ->addColumn(
            'commission_paid',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid'
        )
        ->addColumn(
            'refering_website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Refering Website'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer Id'
        )
        ->addColumn(
            'group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Group ID'
        )
        ->addColumn(
            'create_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Create Time'
        )
        ->setComment(
            'Affiliate - Account Table'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_referingcustomer')
        )
        ->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )
        ->addColumn(
            'refering_customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Refering Customer Email'
        )
        ->addColumn(
            'affiliate_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Affiliate Code'
        )
        ->setComment(
            'Affiliate - refering Customer Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_affiliate_transaction'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_transaction')
        )
        ->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Transaction Affiliate ID'
        )
        ->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Order Id'
        )
        ->addColumn(
            'commission_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Total'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Description'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created at'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Is Active'
        )
        ->addColumn(
            'order_total',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Order Total'
        )
        ->addColumn(
            'order_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Order Status'
        )
        ->addColumn(
            'transaction_stt',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Transaction Status'
        )
        ->addColumn(
            'affiliate_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Affiliate Code'
        )
        ->addColumn(
            'campaign_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Campaign Code'
        )

        ->addColumn(
            'account_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Account Id'
        )
        ->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Increment Id'
        )
        ->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Base Currency Code'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Customer Email'
        )
        ->addColumn(
            'email_aff',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Email Affiliate'
        )
        ->setComment(
            'Affiliate - Transaction Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_affiliate_lead'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_lead')
        )
        ->addColumn(
            'lead_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'PPL Affiliate ID'
        )
        ->addColumn(
            'account_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Account Id'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Email Customer'
        )
        ->addColumn(
            'customer_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Customer IP'
        )
        ->addColumn(
            'signup_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid For Signup Account'
        )
        ->addColumn(
            'subscribe_commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission Paid For Subscribe Newsletter'
        )
        ->addColumn(
            'signup_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Total Of Number Signup'
        )
        ->addColumn(
            'subscribe_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Total Of Number Signup'
        )
        ->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Base Currency Code'
        )
        ->setComment(
            'Affiliate - PPL Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_affiliate_ppc'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_ppc')
        )
        ->addColumn(
            'ppc_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'PPC Affiliate ID'
        )
        ->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Banner Id'
        )
        ->addColumn(
            'affiliate_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'affiliate_code'
        )
        ->addColumn(
            'customer_ip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            150,
            ['nullable' => false],
            'Customer IP'
        )
        ->addColumn(
            'is_unique',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Unique'
        )
        ->addColumn(
            'commission',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Commission'
        )
        ->addColumn(
            'base_currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Base Currency Code'
        )
        ->setComment(
            'Affiliate - PPC Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_affiliate_withdraw'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_affiliate_withdraw')
        )
        ->addColumn(
            'withdraw_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Withdraw Id'
        )
        ->addColumn(
            'withdraw_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, ('12,4'),
            ['nullable' => false, 'default' => '0.0000'],
            'Withdraw Amount'
        )
        ->addColumn(
            'date_request',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            255,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Date Request'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
        ->addColumn(
            'account_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Account Id'
        )
        ->addColumn(
            'affiliate_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Affiliate Email'
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer Id'
        )
        ->addColumn(
            'payment_method',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Payment Method'
        )
        ->addColumn(
            'currency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Currency Code'
        )
        ->setComment(
            'Affiliate - Transaction Table'
        );
        $installer->getConnection()->createTable($table);
    }
}