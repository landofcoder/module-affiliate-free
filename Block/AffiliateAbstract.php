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

namespace Lof\Affiliate\Block;

use Magento\Customer\Model\Session;
class AffiliateAbstract extends \Magento\Framework\View\Element\Template
{
    protected $session;
    protected $_helper;
    protected $_helperImage;
    protected $_accountAffiliateFactory;
    protected $_groupAffiliateFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        \Lof\Affiliate\Helper\Data $helper,
        \Lof\Affiliate\Helper\Image $helperImage,
        \Lof\Affiliate\Model\AccountAffiliateFactory $accountAffiliateFactory,
        \Lof\Affiliate\Model\GroupAffiliateFactory $groupAffiliateFactory,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->_helper = $helper;
        $this->_helperImage = $helperImage;
        $this->_accountAffiliateFactory = $accountAffiliateFactory;
        $this->_groupAffiliateFactory = $groupAffiliateFactory;
        parent::__construct($context, $data);

    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelper()
    {
        return $this->_helper;
    }

    public function getAffiliateAccount(){
        if(!isset($this->_affiliate_account)){
            $customerId = $this->session->getCustomerId();
            $model = $this->_accountAffiliateFactory->create();
            $this->_affiliate_account = $model->load($customerId, 'customer_id');
        }
        return $this->_affiliate_account;
    }

    public function checkExistAccount()
    {
        if(!isset($this->_is_exist_affiliate_account)){
            $accountAffiliate = $this->getAffiliateAccount();
            if (!$accountAffiliate->getId()) {
                $this->_is_exist_affiliate_account = false;
            } else {
                if($accountAffiliate->getIsActive()) {
                    $this->_is_exist_affiliate_account = true;
                }else{
                    $this->_is_exist_affiliate_account = false;
                }
            }
        }
        return $this->_is_exist_affiliate_account;
    }

    public function getAffiliateGroup()
    {
        if(!isset($this->_affiliate_group)){
            $accountAffiliate = $this->getAffiliateAccount();
            $groupModel = $this->_groupAffiliateFactory->create();
            if ($accountAffiliate->getId() && ($group_id = $accountAffiliate->getGroupId())) {
                $groupModel->load((int)$group_id);
            } else {
                $default_group_id = $this->getAffiliateHelper()->getConfig("general_settings/default_affilate_group", null, 1);
                $groupModel->load((int)$default_group_id);
            }
            $this->_affiliate_group = $groupModel;
        }
        return $this->_affiliate_group;
    }

    public function getAffiliateInfo()
    {
        if(!isset($this->_group_data)){
            $groupModel = $this->getAffiliateGroup();
            if($groupModel->getId()){
                $this->_group_data = $groupModel->getData();
            }else{
                $this->_group_data = [];
            }
        }
        return $this->_group_data;
    }

    /**
     * Get Default Helper
     *
     * @param
     * @return helper
     */
    public function getAffiliateHelperImage()
    {
        return $this->_helperImage;
    }
}
