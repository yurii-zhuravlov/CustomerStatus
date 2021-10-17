<?php

declare(strict_types=1);

namespace YuriiZh\CustomerStatus\Controller\Ajax;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use YuriiZh\CustomerStatus\Block\Account\Status;

/**
 * Get Status Ajax Controller
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GetStatus implements CsrfAwareActionInterface
{
    public const GET_STATUS_PATH = 'yuriizh_customerstatus/Ajax/GetStatus';

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var Escaper
     */
    private Escaper $escaper;

    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var RedirectInterface
     */
    private RedirectInterface $redirect;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param Escaper $escaper
     * @param JsonFactory $resultJsonFactory
     * @param LoggerInterface $logger
     * @param RedirectInterface $redirect
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Escaper $escaper,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        RedirectInterface $redirect,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->customerRepository = $customerRepository;
        $this->escaper = $escaper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->redirect = $redirect;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        if (!$request || !$request->isAjax()) {
            return $this->redirect('noroute');
        }

        $result = $this->resultJsonFactory->create();
        $data = [];
        $data[Status::CUSTOMER_STATUS_ATTRIBUTE_CODE] = '';

        $customerId = $request->getParam('id', false);
        if ($customerId && is_numeric($customerId)) {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $statusMessageAttribute = $customer->getCustomAttribute(Status::CUSTOMER_STATUS_ATTRIBUTE_CODE);
                if ($statusMessageAttribute && $statusMessageAttribute->getValue()) {
                    $data[Status::CUSTOMER_STATUS_ATTRIBUTE_CODE]
                        = $this->escaper->escapeHtml($statusMessageAttribute->getValue());
                }
            } catch (NoSuchEntityException|LocalizedException|\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $result->setData($data);

        return $result;
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Set redirect into response
     *
     * @param string $path
     * @param array $arguments
     * @return ResponseInterface
     */
    private function redirect($path, $arguments = []): ResponseInterface
    {
        $this->redirect->redirect($this->getResponse(), $path, $arguments);
        return $this->getResponse();
    }

    /**
     * @return RequestInterface
     */
    private function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    private function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
