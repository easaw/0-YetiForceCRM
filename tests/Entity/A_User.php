<?php

/**
 * AddUser test class.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

namespace Tests\Entity;

class A_User extends \Tests\Base
{
	/**
	 * User id.
	 */
	private static $id;
	/**
	 * Demo user record model cache.
	 *
	 * @var \Users_Record_Model
	 */
	private static $record;

	/**
	 * Create/return users module record model with demo user.
	 *
	 * @return \Users_Record_Model
	 */
	public static function createUsersRecord()
	{
		if (static::$record) {
			return static::$record;
		}
		$user = \Vtiger_Record_Model::getCleanInstance('Users');
		$user->set('user_name', 'demo');
		$user->set('email1', 'demo@yetiforce.com');
		$user->set('first_name', 'Demo');
		$user->set('last_name', 'YetiForce');
		$user->set('user_password', 'demo');
		$user->set('confirm_password', 'demo');
		$user->set('roleid', 'H2');
		$user->set('is_admin', 'on');
		$user->save();
		static::$record = $user;
		return $user;
	}

	/**
	 * Testing user creation.
	 */
	public function testLoadBaseUser()
	{
		$db = \App\Db::getInstance();
		$db->createCommand()->update('vtiger_password', ['val' => 4], ['type' => 'min_length'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'big_letters'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'small_letters'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'numbers'])->execute();
		$db->createCommand()->update('vtiger_password', ['val' => 'false'], ['type' => 'special'])->execute();

		\App\User::setCurrentUserId(static::createUsersRecord()->getId());
		$this->assertInternalType('int', static::createUsersRecord()->getId());
	}

	/**
	 * Testing user creation.
	 */
	public function testAddUser()
	{
		$user = \Vtiger_Record_Model::getCleanInstance('Users');
		$user->set('user_name', 'testuser');
		$user->set('email1', 'testuser@yetiforce.com');
		$user->set('first_name', 'Test');
		$user->set('last_name', 'YetiForce');
		$user->set('user_password', 'testuser');
		$user->set('confirm_password', 'testuser');
		$user->set('roleid', 'H2');
		$user->save();
		static::$id = $user->getId();
		$this->assertInternalType('int', static::$id);
		$row = (new \App\Db\Query())->from('vtiger_users')->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertSame($row['user_name'], 'testuser');
		$this->assertSame($row['email1'], 'testuser@yetiforce.com');
		$this->assertSame($row['first_name'], 'Test');
		$this->assertSame($row['last_name'], 'YetiForce');
		$this->assertSame((new \App\Db\Query())->select('roleid')->from('vtiger_user2role')->where(['userid' => static::$id])->scalar(), 'H2');
	}

	/**
	 * Testing user edition.
	 */
	public function testEditUser()
	{
		$user = \Vtiger_Record_Model::getInstanceById(static::$id, 'Users');
		$this->assertNotFalse($user, 'No user');
		$user->set('user_name', 'testuseredit');
		$user->set('first_name', 'Test edit');
		$user->set('last_name', 'YetiForce edit');
		$user->set('email1', 'testuser-edit@yetiforce.com');
		$user->set('roleid', 'H1');
		$user->save();
		$row = (new \App\Db\Query())->from('vtiger_users')->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertSame($row['user_name'], 'testuseredit');
		$this->assertSame($row['email1'], 'testuser-edit@yetiforce.com');
		$this->assertSame($row['first_name'], 'Test edit');
		$this->assertSame($row['last_name'], 'YetiForce edit');
		$this->assertSame((new \App\Db\Query())->select('roleid')->from('vtiger_user2role')->where(['userid' => static::$id])->scalar(), 'H1');
	}

	/**
	 * Testing user deletion.
	 */
	public function testDeleteUser()
	{
		$currentUserModel = \Users_Record_Model::getCurrentUserModel();
		$this->assertNotFalse($currentUserModel, 'No current user');
		\Users_Record_Model::deleteUserPermanently(static::$id, $currentUserModel->getId());
		$this->assertFalse((new \App\Db\Query())->from('vtiger_users')->where(['id' => static::$id])->exists(), 'The record was not removed from the database ID: ' . static::$id);
	}

	/**
	 * Testing locks creation.
	 */
	public function testLocksUser()
	{
		$param = [['user' => 'H6', 'locks' => ['copy', 'paste']]];
		$moduleModel = \Settings_Users_Module_Model::getInstance();
		$this->assertNotNull($moduleModel, 'Object is null');
		$moduleModel->saveLocks($param);

		$this->assertFileExists('user_privileges/locks.php');
		$locks = $moduleModel->getLocks();
		$locksRaw = ['H6' => ['copy', 'paste']];

		$this->assertCount(0, \array_diff_assoc($locksRaw, $locks), 'Unexpected value in lock array');
		$this->assertCount(0, \array_diff_assoc($locksRaw['H6'], $locks['H6']), 'Unexpected value in lock array');
	}

	/**
	 * Testing locks deletion.
	 */
	public function testDelteLocksUser()
	{
		$param = '';
		$moduleModel = \Settings_Users_Module_Model::getInstance();
		$this->assertNotNull($moduleModel, 'Object is null');
		$moduleModel->saveLocks($param);

		$this->assertFileExists('user_privileges/locks.php');
		$locks = $moduleModel->getLocks();
		$this->assertCount(0, $locks, 'Unexpected value in lock array');
	}
}
