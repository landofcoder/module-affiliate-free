<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Api\Data;

/**
 * Lof Affiliate interface.
 * @api
 * @since 100.0.2
 */
interface AccountInterface
{
    const ACCOUNTAFFILIATE_ID = 'accountaffiliate_id';
    const FIRST_NAME = 'firstname';
    const LAST_NAME = 'lastname';
    const FULL_NAME = 'fullname';
    const EMAIL = 'email';
    const TRACKING_CODE = 'tracking_code';
    const DEFAULT_PAYMENT_METHOD = 'default_payment_method';
    const PAYPAL_EMAIL = 'paypal_email';
    const SKRILL_EMAIL = 'skrill_email';
    const BALANCE = 'balance';
    const CUSTOMER_ID = 'customer_id';
    const GROUP_ID = 'group_id';
    const CAMPAIGN_CODE = 'campaign_code';
    const REFERING_WEBSITE = 'refering_website';
    const COMMISSION_PAID = 'commission_paid';
    const BANK_ACCOUNT_NAME = 'bank_account_name';
    const BANK_ACCOUNT_NUMBER = 'bank_account_number';
    const BANK_NAME = 'bank_name';
    const SWIFT_CODE = 'swift_code';
    const BANK_BRANCH_CITY = 'bank_branch_city';
    const BANK_BRANCH_COUNTRY_CODE = 'bank_branch_country_code';
    const INTERMEDIARY_BANK_CODE = 'intermediary_bank_code';
    const INTERMEDIARY_BANK_NAME = 'intermediary_bank_name';
    const INTERMEDIARY_BANK_CITY = 'intermediary_bank_city';
    const INTERMEDIARY_BANK_COUNTRY_CODE = 'intermediary_bank_country_code';

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
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setId($id);

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setFirstName($firstname);

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setLastName($lastname);

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullName();

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setFullName($fullname);

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setEmail($email);

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
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setTrackingCode($tracking_code);

    /**
     * Get paypal_email
     *
     * @return string
     */
    public function getPaypalEmail();

    /**
     * Set paypal_email
     *
     * @param string $paypal_email
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setPaypalEmail($paypal_email);

    /**
     * Get skrill_email
     *
     * @return string
     */
    public function getSkrillEmail();

    /**
     * Set skrill_email
     *
     * @param string $skrill_email
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setSkrillEmail($skrill_email);

    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance();

    /**
     * Set balance
     *
     * @param string $balance
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBalance($balance);

    /**
     * Get customer_id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param int $customer_id
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get group_id
     *
     * @return int
     */
    public function getGroupAffiliateId();

    /**
     * Set group_id
     *
     * @param int $group_id
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setGroupId($group_id);

    /**
     * Get campaign_code
     *
     * @return string
     */
    public function getCampaignCode();

    /**
     * Set campaign_code
     *
     * @param string $campaign_code
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setCampaignCode($campaign_code);

    /**
     * Get refering_website
     *
     * @return string
     */
    public function getReferingWebsite();

    /**
     * Set refering_website
     *
     * @param string $refering_website
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setReferingWebsite($refering_website);

    /**
     * Get commission_paid
     *
     * @return float|int
     */
    public function getCommissionPaid();

    /**
     * Set commission_paid
     *
     * @param float|int $commission_paid
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setCommissionPaid($commission_paid);

    /**
     * Get bank_account_name
     *
     * @return string
     */
    public function getBankAccountName();

    /**
     * Set bank_account_name
     *
     * @param string $bank_account_name
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBankAccountName($bank_account_name);

    /**
     * Get bank_account_number
     *
     * @return string
     */
    public function getBankAccountNumber();

    /**
     * Set bank_account_number
     *
     * @param string $bank_account_number
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBankAccountNumber($bank_account_number);

    /**
     * Get bank_name
     *
     * @return string
     */
    public function getBankName();

    /**
     * Set bank_name
     *
     * @param string $bank_name
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBankName($bank_name);

    /**
     * Get swift_code
     *
     * @return string
     */
    public function getSwiftCode();

    /**
     * Set swift_code
     *
     * @param string $swift_code
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setSwiftCode($swift_code);

    /**
     * Get bank_branch_city
     *
     * @return string
     */
    public function getBankBranchCity();

    /**
     * Set bank_branch_city
     *
     * @param string $bank_branch_city
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBankBranchCity($bank_branch_city);

    /**
     * Get bank_branch_country_code
     *
     * @return string
     */
    public function getBankBranchCountryCode();

    /**
     * Set bank_branch_country_code
     *
     * @param string $bank_branch_country_code
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setBankBranchCountryCode($bank_branch_country_code);

    /**
     * Get intermediary_bank_code
     *
     * @return string
     */
    public function getIntermediaryBankCode();

    /**
     * Set intermediary_bank_code
     *
     * @param string $intermediary_bank_code
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setIntermediaryBankCode($intermediary_bank_code);

    /**
     * Get intermediary_bank_name
     *
     * @return string
     */
    public function getIntermediaryBankName();

    /**
     * Set intermediary_bank_name
     *
     * @param string $intermediary_bank_name
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setIntermediaryBankName($intermediary_bank_name);

    /**
     * Get intermediary_bank_city
     *
     * @return string
     */
    public function getIntermediaryBankCity();

    /**
     * Set intermediary_bank_city
     *
     * @param string $intermediary_bank_city
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setIntermediaryBankCity($intermediary_bank_city);

    /**
     * Get intermediary_bank_country_code
     *
     * @return string
     */
    public function getIntermediaryBankCountryCode();

    /**
     * Set intermediary_bank_country_code
     *
     * @param string $intermediary_bank_country_code
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setIntermediaryBankCountryCode($intermediary_bank_country_code);

    /**
     * Get default_payment_method
     *
     * @return string
     */
    public function getDefaultPaymentMethod();

    /**
     * Set default_payment_method
     *
     * @param string $default_payment_method
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     */
    public function setDefaultPaymentMethod($default_payment_method);
}
