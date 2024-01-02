<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Affiliate
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Affiliate\Model\Data;

use Lof\Affiliate\Api\Data\CampaignInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Rule
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class CampaignAffiliate extends AbstractExtensibleObject implements CampaignInterface
{
    
    public function getId()
    {
        return $this->_get(self::CAMPAIGN_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::CAMPAIGN_ID, $id);
    }

    public function getName()
    {
        return $this->_get(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function getDisplay()
    {
        return $this->_get(self::DISPLAY);
    }

    public function setDisplay($display)
    {
        return $this->setData(self::DISPLAY, $display);
    }

    public function getFromDate()
    {
        return $this->_get(self::FROM_DATE);
    }

    public function setFromDate($from_date)
    {
        return $this->setData(self::FROM_DATE, $from_date);
    }

    public function getToDate()
    {
        return $this->_get(self::TO_DATE);
    }

    public function setToDate($to_date)
    {
        return $this->setData(self::TO_DATE, $to_date);
    }

    public function getDiscountAction()
    {
        return $this->_get(self::DISCOUNT_ACTION);
    }

    public function setDiscountAction($discount_action)
    {
        return $this->setData(self::DISCOUNT_ACTION, $discount_action);
    }

    public function getDiscountAmount()
    {
        return $this->_get(self::DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount($discount_amount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discount_amount);
    }

    public function getCommission()
    {
        return $this->_get(self::COMMISSION);
    }

    public function setCommission($commission)
    {
        return $this->setData(self::COMMISSION, $commission);
    }

    public function getTrackingCode()
    {
        return $this->_get(self::TRACKING_CODE);
    }

    public function setTrackingCode($tracking_code)
    {
        return $this->setData(self::TRACKING_CODE, $tracking_code);
    }

    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    public function setGroupId($group_id)
    {
        return $this->setData(self::GROUP_ID, $group_id);
    }

    public function getSignupCommission()
    {
        return $this->_get(self::SIGNUP_COMMISSION);
    }

    public function setSignupCommission($signup_commission)
    {
        return $this->setData(self::SIGNUP_COMMISSION, $signup_commission);
    }

    public function getLimitAccount()
    {
        return $this->_get(self::LIMIT_ACCOUNT);
    }

    public function setLimitAccount($limit_account)
    {
        return $this->setData(self::LIMIT_ACCOUNT, $limit_account);
    }

    public function getLimitBalance()
    {
        return $this->_get(self::LIMIT_BALANCE);
    }

    public function setLimitBalance($limit_balance)
    {
        return $this->setData(self::LIMIT_BALANCE, $limit_balance);
    }
}
