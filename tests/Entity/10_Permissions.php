<?php

/**
 * Permissions test class
 * @package YetiForce.Test
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
class Permissions extends \Tests\Base
{

	/**
	 * Testing record permissions
	 */
	public function testIsPermitted()
	{
		$this->assertTrue(\App\Privilege::isPermitted('Accounts', 'DetailView', ACCOUNT_ID));
	}

	/**
	 * Testing refreshing permission files
	 */
	public function testRecalculateSharingRules()
	{
		RecalculateSharingRules();
		$this->assertTrue(true);
	}

	/**
	 * Testing refreshing module meta files
	 */
	public function testCreateModuleMetaFile()
	{
		\vtlib\Deprecated::createModuleMetaFile();
		$this->assertTrue(true);
	}
}
