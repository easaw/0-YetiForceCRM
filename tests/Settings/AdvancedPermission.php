<?php
/**
 * AdvancedPermission test class
 * @package YetiForce.Test
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Arkadiusz Adach <a.adach@yetiforce.com>
 */
namespace Tests\Settings;

class AdvancedPermission extends \Tests\Base
{

	/**
	 * Advanced permission id
	 */
	private static $id;

	/**
	 * Testing advanced permission creation
	 */
	public function testAddAdvancedPermission()
	{
		$members = ['Users:1', 'Groups:3', 'Roles:H6', 'RoleAndSubordinates:H34'];

		$recordModel = new \Settings_AdvancedPermission_Record_Model();
		$recordModel->set('name', 'test');
		$recordModel->set('tabid', 4);
		$recordModel->set('action', 0);
		$recordModel->set('status', 0);
		$recordModel->set('members', $members);
		$recordModel->set('priority', 0);
		$recordModel->set('conditions', []);
		$recordModel->save();
		static::$id = $recordModel->getId();

		$row = (new \App\Db\Query())->from('a_#__adv_permission')->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals('test', $row['name']);
		$this->assertEquals(4, $row['tabid']);
		$this->assertEquals(0, $row['action']);
		$this->assertEquals(0, $row['status']);
		$this->assertEquals(0, $row['priority']);
		$this->assertEquals(\App\Json::encode($members), $row['members']);
		$this->assertEquals(\App\Json::encode([]), $row['conditions']);
	}

	/**
	 * Testing advanced permission edition
	 */
	public function testEditAdvancedPermission()
	{
		$members = ['Users:1'];
		$conditions = [
			[
				'fieldname' => 'salutationtype',
				'operation' => 'is',
				'value' => 'Mr.',
				'valuetype' => 'rawtext',
				'joincondition' => '',
				'groupjoin' => 'and',
				'groupid' => 0,
			],
			[
				'fieldname' => 'firstname',
				'operation' => 'contains',
				'value' => 'ek',
				'valuetype' => 'rawtext',
				'joincondition' => '',
				'groupjoin' => NULL,
				'groupid' => 1,
			],
		];

		$recordModel = \Settings_AdvancedPermission_Record_Model::getInstance(static::$id);
		$this->assertNotFalse($recordModel, 'No record id: ' . static::$id);

		$recordModel->set('name', 'test edit');
		$recordModel->set('tabid', 4);
		$recordModel->set('action', 0);
		$recordModel->set('status', 1);
		$recordModel->set('members', $members);
		$recordModel->set('priority', 0);
		$recordModel->set('conditions', $conditions);
		$recordModel->save();

		$row = (new \App\Db\Query())->from('a_#__adv_permission')->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals('test edit', $row['name']);
		$this->assertEquals(4, $row['tabid']);
		$this->assertEquals(0, $row['action']);
		$this->assertEquals(1, $row['status']);
		$this->assertEquals(0, $row['priority']);
		$this->assertEquals(\App\Json::encode($members), $row['members']);
		$this->assertEquals(\App\Json::encode($conditions), $row['conditions']);
	}

	/**
	 * Testing advanced permission deletion
	 */
	public function testDelteAdvancedPermission()
	{
		$recordModel = \Settings_AdvancedPermission_Record_Model::getInstance(static::$id);
		$recordModel->delete();

		$this->assertFalse((new \App\Db\Query())->from('a_#__adv_permission')->where(['id' => static::$id])->exists(), 'The record was not removed from the database ID: ' . static::$id);
	}
}
