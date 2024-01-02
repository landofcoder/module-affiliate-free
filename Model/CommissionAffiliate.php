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

class CommissionAffiliate extends \Magento\Framework\Model\AbstractModel
{
    protected $_url;
    protected $_helper;
    protected $_resource;
    protected $_resourceModel;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Lof\Affiliate\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resourceModel,
        \Lof\Affiliate\Model\ResourceModel\CommissionAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\CommissionAffiliate\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_resource = $resource;
        $this->_url = $url;
        $this->_helper = $helper;
        $this->_resourceModel = $resourceModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\Affiliate\Model\ResourceModel\CommissionAffiliate::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'commission_id="' . $this->getId() . '"';
        $content = $this->getContent();
        if (empty($content) || (!empty($content) && false == @strstr($content, $needle))) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
        );
    }

    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }


}
