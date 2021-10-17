<?php

namespace YuriiZh\CustomerStatus\Controller\Customer;

use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;
use Psr\Log\LoggerInterface;
use YuriiZh\CustomerStatus\Block\Account\Status;

/**
 * Customer Status Index Controller
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Index extends Action
{
    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @var Url
     */
    private Url $customerUrl;

    /**
     * @var CustomerResourceModel
     */
    private CustomerResourceModel $customerResourceModel;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param CustomerResourceModel $customerResourceModel
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Url $customerUrl,
        CustomerResourceModel $customerResourceModel,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->customerResourceModel = $customerResourceModel;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->customerUrl->getLoginUrl();
        if (!$this->customerSession->authenticate($loginUrl)) {
            $this->getActionFlag()->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        if ($request && $request->isPost()) {
            $data = $request->getPostValue();
            if (isset($data[Status::CUSTOMER_STATUS_ATTRIBUTE_CODE])) {
                $statusMessage = $data[Status::CUSTOMER_STATUS_ATTRIBUTE_CODE];
                $customer = $this->customerSession->getCustomer();
                $customer->setData(Status::CUSTOMER_STATUS_ATTRIBUTE_CODE, $statusMessage);
                try {
                    $this->customerResourceModel->saveAttribute($customer, Status::CUSTOMER_STATUS_ATTRIBUTE_CODE);
                    $this->messageManager->addSuccessMessage(__('Status was updated.'));
                } catch (\Exception $e) {
                    $this->logger->error($e);
                    $this->messageManager->addErrorMessage(__('We cannot save this status.'));
                }
                $this->_redirect('*/*/*');
                return;
            }
        }

        $this->_view->loadLayout();
        $this->_view->loadLayoutUpdates();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Status'));
        $this->_view->renderLayout();
    }
}
