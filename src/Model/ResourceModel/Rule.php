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
namespace HiPay\FullserviceMagento\Model\ResourceModel;


class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
	
	/**
	 * Initialize main table and table id field
	 *
	 * @return void
	 * @codeCoverageIgnore
	 */
	protected function _construct()
	{
		$this->_init('hipay_rule', 'rule_id');
	}
	
  
}