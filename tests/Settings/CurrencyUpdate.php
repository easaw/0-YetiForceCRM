<?php
/**
 * CurrencyUpdate test class.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 */

namespace Tests\Settings;

class CurrencyUpdate extends \Tests\Base
{
	/**
	 * Testing module model methods.
	 */
	public function testModuleModel()
	{
		$moduleModel = \Settings_CurrencyUpdate_Module_Model::getCleanInstance();
		$this->assertNotEmpty(\Settings_CurrencyUpdate_Module_Model::getCRMCurrencyName('PLN'), 'Expected currency name');
		$this->assertInternalType('integer', $moduleModel->getCurrencyNum(), 'Expected currency number as integer');
		$this->assertInternalType('boolean', $moduleModel->fetchCurrencyRates(date('Y-m-d')), 'Expected boolean result.');
		$this->assertNull($moduleModel->refreshBanks(), 'Method should return nothing');
		$this->assertInternalType('array', $moduleModel->getSupportedCurrencies(), 'getSupportedCurrencies should always return array');
		$this->assertInternalType('array', $moduleModel->getUnSupportedCurrencies(), 'getUnSupportedCurrencies should always return array');
		$this->assertInternalType('numeric', $moduleModel->getCRMConversionRate('PLN', 'USD'), 'getCRMConversionRate should always return number');
		$this->assertInternalType('numeric', $moduleModel->convertFromTo(12, 'PLN', 'USD'), 'convertFromTo should always return number');
		$this->assertInternalType('integer', $moduleModel->getActiveBankId(), 'Expected active bank id as integer');
		$this->assertTrue($moduleModel->setActiveBankById($moduleModel->getActiveBankId()), 'setActiveBankById should return true');
		$this->assertNotEmpty($moduleModel->getActiveBankName(), 'Active bank name should be not empty');
	}

	/**
	 * Testing external bank interfaces.
	 */
	public function testBanks()
	{
		$moduleModel = \Settings_CurrencyUpdate_Module_Model::getCleanInstance();
		foreach (['CBR', 'ECB', 'NBR', 'NBP'] as $bankCode) {
			$bankClass = '\Settings_CurrencyUpdate_' . $bankCode . '_BankModel';
			$bank = new $bankClass();
			$this->assertNotEmpty($bank->getName(), 'Bank name should be not empty');
			$this->assertNotEmpty($bank->getSource(), 'Bank source should be not empty');
			$this->assertInternalType('array', $bank->getSupportedCurrencies(), 'Expected array of currencies');
			$this->assertNotEmpty($bank->getMainCurrencyCode(), 'Main bank currency should be not empty');
			$this->assertNull($bank->getRates($moduleModel->getSupportedCurrencies(), date('Y-m-d')), 'Expected nothing/null');
		}
	}
}
