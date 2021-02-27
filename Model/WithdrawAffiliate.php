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
namespace Lof\Affiliate\Model;

class WithdrawAffiliate extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Blog's Statuses
     */
    // const STATUS_COMPLETE = 1;
    // const STATUS_PENDING = 0;
    // const STATUS_CANCEL = 2;


    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_resource;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceHelper;
    /**
     * Page cache tag
     */
    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Framework\Registry                               $registry                          
     * @param \Ves\Blog\Model\ResourceModel\Blog|null                      $resource                 
     * @param \Ves\Blog\Model\ResourceModel\Blog\Collection|null           $resourceCollection       
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory            
     * @param \Magento\Framework\UrlInterface                           $url                      
     * @param \Ves\Blog\Helper\Data                                    $brandHelper              
     * @param array                                                     $data                     
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate $resource = null,
        \Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate\Collection $resourceCollection = null,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
        ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Affiliate\Model\ResourceModel\WithdrawAffiliate');
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'withdraw_id="' . $this->getId() . '"';
        if (false == strstr($this->getContent(), $needle)) {
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

