<?php

namespace YuriiZh\CustomerStatus\Block\Account;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Status Block Class
 */
class Status extends Template
{
    public const CUSTOMER_STATUS_ATTRIBUTE_CODE = 'status_message';

    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->currentCustomer = $currentCustomer;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve customer status message, empty if none
     *
     * @return string
     */
    public function getCurrentStatus(): string
    {
        $customer = $this->currentCustomer->getCustomer();
        $statusMessage = $customer->getCustomAttribute(self::CUSTOMER_STATUS_ATTRIBUTE_CODE);

        return $statusMessage ? $statusMessage->getValue() : '';
    }
}
