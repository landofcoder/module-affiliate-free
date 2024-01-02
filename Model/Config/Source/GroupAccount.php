<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Affiliate\Model\Config\Source;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Lof\Affiliate\Model\ResourceModel\GroupAffiliate\CollectionFactory;

class GroupAccount implements \Magento\Framework\Option\ArrayInterface
{

    protected $_groupCollection=null;
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceFormatter;

    public function __construct(
        CollectionFactory $groupCollection,
        PriceCurrencyInterface $priceFormatter
    ) {
        $this->_groupCollection = $groupCollection;
        $this->_priceFormatter = $priceFormatter;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = array();
        $collection = $this->_groupCollection->create();
        $collection->addFieldToFilter("is_active", 1);
        if($collection->count()){
            foreach ($collection as $_group) {
                $tmp = [];
                $commission = $_group->getCommission();
                $commission_action = $_group->getData("commission_action");
                if ($commission_action == 1) {
                    $commission = $commission . '%';
                } else {
                    $commission = $this->_priceFormatter->getCurrencySymbol() . $commission;
                }
                $tmp['label'] = $_group->getName(). ' ('.$commission . ')';
                $tmp['value'] =  $_group->getId();
                $data[] = $tmp;
            }
        }
        return $data;
    }
}