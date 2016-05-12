<?php
/*
 * HiPay fullservice SDK
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @copyright      Copyright (c) 2016 - HiPay
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 *
 */
namespace HiPay\FullserviceMagento\Model\Method;


use HiPay\FullserviceMagento\Model\CcMethod;
use Magento\Framework\Exception\LocalizedException;
use \HiPay\FullserviceMagento\Model\Gateway\Factory as GatewayManagerFactory;
/**
 * Class Cc Split Payment Method
 * @package HiPay\FullserviceMagento\Model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CcSplitMethod extends CcMethod {
	
	const HIPAY_METHOD_CODE               = 'hipay_ccsplit';
	
	/**
	 * @var string
	 */
	protected $_code = self::HIPAY_METHOD_CODE;	
	
	/**
	 * 
	 * @var \HiPay\FullserviceMagento\Model\PaymentProfileFactory $profilefactory
	 */
	protected $profileFactory;
	
	/**
	 * 
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
	 * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
	 * @param \Magento\Payment\Helper\Data $paymentData
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Payment\Model\Method\Logger $logger
	 * @param GatewayManagerFactory $gatewayManagerFactory
	 * @param \Magento\Framework\Url $urlBuilder
	 * @param \Magento\Framework\Module\ModuleListInterface $moduleList
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
	 * @param \HiPay\FullserviceMagento\Model\PaymentProfileFactory $profilefactory
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data
	 *  @SuppressWarnings(PHPMD.ExcessiveParameterList)
	 */
	public function __construct(
			\Magento\Framework\Model\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
			\Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
			\Magento\Payment\Helper\Data $paymentData,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Payment\Model\Method\Logger $logger,
			GatewayManagerFactory $gatewayManagerFactory,
			\Magento\Framework\Url $urlBuilder,
			\Magento\Framework\Module\ModuleListInterface $moduleList,
			\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
			\HiPay\FullserviceMagento\Model\PaymentProfileFactory $profileFactory,
			\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
			\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
			array $data = []
			) {
				parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, 
									$paymentData, $scopeConfig, $logger, $gatewayManagerFactory,
									$urlBuilder,$moduleList,$localeDate,$resource,$resourceCollection,$data);
				
			$this->profileFactory = $profileFactory;

	}
	
	
	protected function getAddtionalInformationKeys(){
		return array_merge(['profile_id'],$this->_additionalInformationKeys);
	}
	
	public function place(\Magento\Payment\Model\InfoInterface $payment){
		$profileId = $payment->getAdditionalInformation('profile_id');
		
		if(empty($profileId)){
			throw new LocalizedException(__('Payment Profile not found.'));
		}
		$profile = $this->profileFactory->create()->load($profileId);
		if(!$profile->getId()){
			throw new LocalizedException(__('Payment Profile not found.'));
		}
		
		$splitAmounts = $profile->splitAmount($payment->getOrder()->getBaseGrandTotal());
		if(!is_array($splitAmounts) || !count($splitAmounts)){
			throw new LocalizedException(__('Impossible to split the amount.'));
		}
		$firstSplit = current($splitAmounts);
		$payment->getOrder()->setForcedAmount((float)$firstSplit['amountToPay']);
		
		return parent::place($payment);
		
	}
	
}