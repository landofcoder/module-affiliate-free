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

class PpcAffiliate extends \Magento\Framework\Model\AbstractModel
{

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_ppclHelper;

    protected $_resource;

    protected $_ppcFactory;

    protected $_resourceModel;

    /**
     * @var Session
     */
    protected $session;
    /**
     * Page cache tag
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\PpcAffiliate\CollectionFactory $ppcFactory,
        \Magento\Framework\UrlInterface $url,
        \Lof\Affiliate\Helper\Data $ppclHelper,
        Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resourceModel,
        \Lof\Affiliate\Model\ResourceModel\PpcAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\PpcAffiliate\Collection $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_url = $url;
        $this->_resource = $resource;
        $this->_ppclHelper = $ppclHelper;
        $this->_ppcFactory = $ppcFactory;
        $this->_resourceModel = $resourceModel;
        $this->session = $customerSession;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Lof\Affiliate\Model\ResourceModel\PpcAffiliate::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);

        return $this;
    }
}
