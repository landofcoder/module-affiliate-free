<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * install group affiliate
         */
        $data = [
            [
                'name' => 'Default', 
                'is_active' => 1,
                'commission_action' => 1,
                'commission' => 0, 
                'enable_ppl' => 1,
                'commission_action' => 1,
                'commission_ppl' => 0,
            ]
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lof_affiliate_group'), $row);
        }
    }
}
