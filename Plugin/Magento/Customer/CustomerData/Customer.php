<?php

declare(strict_types=1);

namespace YuriiZh\CustomerStatus\Plugin\Magento\Customer\CustomerData;

use Magento\Customer\CustomerData\Customer as CustomerData;
use Magento\Customer\Model\Session as CustomerSession;

/**
 *  Customer Data Plugin
 */
class Customer
{
    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * Customer constructor.
     * @param CustomerSession $customerSession
     */
    public function __construct(
        CustomerSession $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @param CustomerData $subject
     * @param $result
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(CustomerData $subject, $result)
    {
        if (empty($result)) {
            return $result;
        }
        $result['id'] = $this->customerSession->getCustomerId();
        return $result;
    }
}
