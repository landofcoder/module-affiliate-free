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
namespace Lof\Affiliate\Model\Config\Source;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    protected $orderConfigFactory;
    protected $context;

    public function __construct(
        \Magento\Sales\Model\Order\ConfigFactory $orderConfigFactory,
        \Magento\Framework\Model\Context $context
    ) {
        $this->orderConfigFactory = $orderConfigFactory;
        $this->context            = $context;
    }

    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            $statuses = $this->orderConfigFactory->create()->getStatuses();
            foreach ($statuses as $id => $status) {
                $this->options[] = ['value' => $id, 'label' => $status];
            }
        }

        return $this->options;
    }
}
