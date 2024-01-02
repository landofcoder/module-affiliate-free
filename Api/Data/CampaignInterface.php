<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Api\Data;

/**
 * Lof Campaign interface.
 * @api
 * @since 100.0.2
 */
interface CampaignInterface
{
    const CAMPAIGN_ID = 'campaign_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const DISPLAY = 'display';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const DISCOUNT_ACTION = 'discount_action';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const COMMISSION = 'commission';
    const TRACKING_CODE = 'tracking_code';
    const GROUP_ID = 'group_id';
    const STORE_ID = 'store_id';
    const SIGNUP_COMMISSION = 'signup_commission';
    const SUBCRIBE_COMMISSION = 'subscribe_commission';
    const LIMIT_ACCOUNT = 'limit_account';
    const LIMIT_BALANCE = 'limit_balance';
    const CREATE_AT = 'create_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set firstname
     *
     * @param string $name
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setName($name);

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     *
     * @param string $description
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setDescription($description);

    /**
     * Get display
     *
     * @return string
     */
    public function getDisplay();

    /**
     * Set display
     *
     * @param string $display
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setDisplay($display);

    /**
     * Get from_date
     *
     * @return string
     */
    public function getFromDate();

    /**
     * Set from_date
     *
     * @param string $from_date
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setFromDate($from_date);

    /**
     * Get to_date
     *
     * @return string
     */
    public function getToDate();

    /**
     * Set to_date
     *
     * @param string $to_date
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setToDate($to_date);

    /**
     * Get discount_action
     *
     * @return string
     */
    public function getDiscountAction();

    /**
     * Set discount_action
     *
     * @param string $discount_action
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setDiscountAction($discount_action);

    /**
     * Get discount_amount
     *
     * @return string
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     *
     * @param string $discount_amount
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setDiscountAmount($discount_amount);

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission();

    /**
     * Set commission
     *
     * @param string $commission
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setCommission($commission);

    /**
     * Get tracking_code
     *
     * @return string
     */
    public function getTrackingCode();

    /**
     * Set tracking_code
     *
     * @param string $tracking_code
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setTrackingCode($tracking_code);

    /**
     * Get group_id
     *
     * @return int
     */
    public function getGroupId();

    /**
     * Set group_id
     *
     * @param int $group_id
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setGroupId($group_id);

    /**
     * Get signup_commission
     *
     * @return string
     */
    public function getSignupCommission();

    /**
     * Set signup_commission
     *
     * @param string $signup_commission
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setSignupCommission($signup_commission);

    /**
     * Get limit_account
     *
     * @return string
     */
    public function getLimitAccount();

    /**
     * Set limit_account
     *
     * @param string $limit_account
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setLimitAccount($limit_account);

    /**
     * Get limit_balance
     *
     * @return string
     */
    public function getLimitBalance();

    /**
     * Set limit_balance
     *
     * @param string $limit_balance
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     */
    public function setLimitBalance($limit_balance);
}
