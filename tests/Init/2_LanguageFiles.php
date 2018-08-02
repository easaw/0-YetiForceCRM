<?php

/**
 * Language Files test class
 * @package YetiForce.Test
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class LanguageFiles extends \Tests\Base
{

	/**
	 * Testing language files
	 */
	public function testLoadFiles()
	{
		foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(ROOT_DIRECTORY . DIRECTORY_SEPARATOR . 'languages', \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
			if ($item->isFile()) {
				if (isset($languageStrings)) {
					unset($languageStrings);
				}
				if (isset($jsLanguageStrings)) {
					unset($jsLanguageStrings);
				}
				ob_start();
				include $item->getPathname();
				$this->assertTrue(empty(ob_get_contents()), $item->getPathname());
				ob_end_clean();
				$this->assertTrue(is_array($languageStrings) || is_array($jsLanguageStrings), 'File: ' . $item->getPathname() . ' | $languageStrings: ' . print_r(is_array($languageStrings), true) . ' | $jsLanguageStrings: ' . print_r(is_array($jsLanguageStrings), true));
			}
		}
	}

	/**
	 * Testing translation functions
	 */
	public function testTranslate()
	{
		\App\Language::setLanguage('pl_pl');
		$this->assertTrue(\App\Language::translate('LBL_MONTH') === 'miesiąc');
		$this->assertTrue(\App\Language::translateArgs('LBL_VALID_RECORDS', 'Vtiger', 'aaa', 'bbb') === 'aaa z bbb są poprawne dla wybranego szablonu.');
		$this->assertTrue(\App\Language::translatePluralized('PLU_SYSTEM_WARNINGS', 'Settings::Vtiger', 1) === 'Ostrzeżenie systemowe');
		$this->assertTrue(\App\Language::translatePluralized('PLU_SYSTEM_WARNINGS', 'Settings::Vtiger', 2) === 'Ostrzeżenia systemowe');
		$this->assertTrue(\App\Language::translatePluralized('PLU_SYSTEM_WARNINGS', 'Settings::Vtiger', 9) === 'Ostrzeżeń systemowych');
	}
}
