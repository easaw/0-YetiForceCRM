<?php
/**
 * Inventory test class
 * @package YetiForce.Test
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Arkadiusz Adach <a.adach@yetiforce.com>
 */
namespace Tests\Settings;

class Inventory extends \Tests\Base
{

	/**
	 * Inventory id
	 */
	private static $id;

	/**
	 * Save to database
	 * @param int $id
	 * @param string $type
	 * @param string $name
	 * @param float|int $value
	 * @param int $status
	 * @return int
	 */
	private function save($id, $type, $name, $value, $status)
	{
		if (empty($id)) {
			$recordModel = new \Settings_Inventory_Record_Model();
		} else {
			$recordModel = \Settings_Inventory_Record_Model::getInstanceById($id, $type);
		}

		if ($type === 'Discounts') {
			$recordModel->set('value', \CurrencyField::convertToDBFormat($recordModel->get('value')));
		}

		$recordModel->set('id', $id);
		$recordModel->set('name', $name);
		$recordModel->set('value', $value);
		$recordModel->set('status', $status);
		$recordModel->setType($type);
		return $recordModel->save();
	}

	/**
	 * Testing taxes creation
	 */
	public function testAddTaxes()
	{
		$type = 'Taxes';
		$name = 'test';
		$value = 3.14;
		$status = 0;
		static::$id = $this->save(0, $type, $name, $value, $status);
		$this->assertNotNull(static::$id, 'Id is null');

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing taxes edition
	 */
	public function testEditTaxes()
	{
		$type = 'Taxes';
		$name = 'test edit';
		$value = 1.16;
		$status = 1;
		$this->save(static::$id, $type, $name, $value, $status);

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing taxes deletion
	 */
	public function testDeleteTaxes()
	{
		$type = 'Taxes';
		$recordModel = \Settings_Inventory_Record_Model::getInstanceById(static::$id, $type);
		$recordModel->delete();

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$this->assertFalse((new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->exists(), 'The record was not removed from the database ID: ' . static::$id);
	}

	/**
	 * Testing discount creation
	 */
	public function testAddDiscount()
	{
		$type = 'Discounts';
		$name = 'test name';
		$value = 3.14;
		$status = 0;
		static::$id = $this->save('', $type, $name, $value, $status);
		$this->assertNotNull(static::$id, 'Id is null');

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing discount edition
	 */
	public function testEditDiscount()
	{
		$type = 'Discounts';
		$name = 'test edit';
		$value = 2.62;
		$status = 1;
		$this->save(static::$id, $type, $name, $value, $status);

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing discount deletion
	 */
	public function testDeleteDiscount()
	{
		$type = 'Discounts';
		$recordModel = \Settings_Inventory_Record_Model::getInstanceById(static::$id, $type);
		$recordModel->delete();

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$this->assertFalse((new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->exists(), 'The record was not removed from the database ID: ' . static::$id);
	}

	/**
	 * Testing credit limits creation
	 */
	public function testAddCreditLimits()
	{
		$type = 'CreditLimits';
		$name = 'test';
		$value = 500;
		$status = 0;
		static::$id = $this->save('', $type, $name, $value, $status);
		$this->assertNotNull(static::$id, 'Id is null');

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing credit limits edition
	 */
	public function testEditCreditLimits()
	{
		$type = 'CreditLimits';
		$name = 'test edit';
		$value = 1410;
		$status = 1;
		$this->save(static::$id, $type, $name, $value, $status);

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$row = (new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$id);
		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['value'], $value);
		$this->assertEquals($row['status'], $status);
	}

	/**
	 * Testing credit limits deletion
	 */
	public function testDeleteCreditLimits()
	{
		$type = 'CreditLimits';
		$recordModel = \Settings_Inventory_Record_Model::getInstanceById(static::$id, $type);
		$recordModel->delete();

		$tableName = \Settings_Inventory_Record_Model::getTableNameFromType($type);
		$this->assertFalse((new \App\Db\Query())->from($tableName)->where(['id' => static::$id])->exists(), 'The record was not removed from the database ID: ' . static::$id);
	}
}
