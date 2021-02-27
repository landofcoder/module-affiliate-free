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
 * @package    Ves_Affiliate
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lof\Affiliate\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
 
class UrlCampaignParam implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
	        ['value' => '1', 'label' => __('Identify Code')],
	        // ['value' => '2', 'label' => __('Campaign ID')],
        ];
    }
}