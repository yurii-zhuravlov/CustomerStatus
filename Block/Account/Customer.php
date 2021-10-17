<?php

namespace YuriiZh\CustomerStatus\Block\Account;

use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Customer Block Class
 */
class Customer extends Template
{
    /**
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @param TemplateContext $context
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        HttpContext $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(CustomerContext::CONTEXT_AUTH);
    }

    public function getStatusMessage(): string
    {
        return $this->httpContext->getValue(Status::CUSTOMER_STATUS_ATTRIBUTE_CODE) ?? '';
    }
}
