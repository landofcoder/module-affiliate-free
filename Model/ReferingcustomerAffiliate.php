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

use Magento\Customer\Model\Session;

class ReferingcustomerAffiliate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_resource;

    protected $_referingcustomerFactory;

    protected $_resourceModel;

    /**
     * @var Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate\CollectionFactory $accountFactory,
        \Magento\Framework\UrlInterface $url,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceModel,
        \Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate\Collection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_url = $url;
        $this->_resource = $resource;
        $this->_accountFactory = $accountFactory;
        $this->_resourceModel = $resourceModel;
        $this->session = $customerSession;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\Affiliate\Model\ResourceModel\ReferingcustomerAffiliate::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'id="' . $this->getId() . '"';
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
