<?php

namespace YuriiZh\CustomerStatus\Model\App\Action;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use YuriiZh\CustomerStatus\Block\Account\Status;

/**
 * Introduces Context status information for ActionInterface of Customer Action
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ContextPlugin
{
    /**
     * @var CurrentCustomer
     */
    private CurrentCustomer $currentCustomer;

    /**
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param HttpContext $httpContext
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        HttpContext $httpContext
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->httpContext = $httpContext;
    }

    /**
     * Set customer status message to HTTP context
     *
     * @param ActionInterface $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(ActionInterface $subject): void
    {
        if (!$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return;
        }

        $customer = $this->currentCustomer->getCustomer();
        $statusMessageAttribute = $customer->getCustomAttribute(Status::CUSTOMER_STATUS_ATTRIBUTE_CODE);
        $statusMessage = $statusMessageAttribute ? $statusMessageAttribute->getValue() : '';

        $this->httpContext->setValue(
            Status::CUSTOMER_STATUS_ATTRIBUTE_CODE,
            $statusMessage,
            false
        );
    }
}
