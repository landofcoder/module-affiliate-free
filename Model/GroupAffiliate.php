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

namespace Lof\Affiliate\Model;

class GroupAffiliate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Group's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


    const PERCENTAGE = 1;
    const FIXAMOUNT = 2;


    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_accountlHelper;

    protected $_resource;

    protected $_groupCollection;
    /**
     * Page cache tag
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\GroupAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\GroupAffiliate\Collection $resourceCollection = null,
        \Lof\Affiliate\Model\ResourceModel\GroupAffiliate\CollectionFactory $groupCollection,
        \Magento\Framework\UrlInterface $url,
        \Lof\Affiliate\Helper\Data $accountlHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_url = $url;
        $this->_resource = $resource;
        $this->_accountlHelper = $accountlHelper;
        $this->_groupCollection = $groupCollection;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\Affiliate\Model\ResourceModel\GroupAffiliate::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'group_id="' . $this->getId() . '"';
        $groupContent = $this->getContent();

        if (empty($groupContent) || (!empty($groupContent) && (false == @strstr($groupContent, $needle)))) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that content does not reference the block itself.')
        );
    }

    /**
     * get available statuses
     * @return mixed|array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * get commission actions
     * @return mixed|array
     */
    public function getCommissionAction()
    {
        return [self::PERCENTAGE => __('Percent of current cart total'), self::FIXAMOUNT => __('Fixed Amount Commission For Whole Cart')];
    }

    /**
     * get group link
     *
     * @return \Lof\Affiliate\Model\ResourceModel\GroupAffiliate\Collection
     */
    public function getGroupLink()
    {
        $groupCollection = $this->_groupCollection->create()
            ->addFieldToFilter('is_active', '1');
        return $groupCollection;
    }

}
