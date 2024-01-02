<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\Affiliate\Api;

/**
 * Lof Affiliate CRUD interface.
 * @api
 * @since 100.0.2
 */
interface AccountRepositoryInterface
{
    /**
     * use refer code.
     *
     * @param mixed $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function useReferCode($data);

    /**
     * create transaction.
     *
     * @param mixed $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createTransaction($data);

    /**
     * complete order.
     *
     * @param mixed $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function completeOrder($data);

    /**
     * Get Customer Refer Code
     *
     * @param int  $customerId
     * @return \Lof\Affiliate\Api\Data\AccountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReferCode($customerId);

    /**
     * Retrieve Campaign.
     *
     * @param int $campaignId
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($campaignId);

    /**
     * Save Campaign.
     *
     * @param  \Lof\Affiliate\Api\Data\CampaignInterface $campaign
     * @return \Lof\Affiliate\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Lof\Affiliate\Api\Data\CampaignInterface $campaign);
}
