<?php
/**
 * Api integrations test class
 * @package YetiForce.Test
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
namespace Tests\Integrations;

class Api extends \Tests\Base
{

	/**
	 * Server address
	 * @var string
	 */
	private static $url;

	/**
	 * Api server id
	 * @var int
	 */
	private static $serverId;

	/**
	 * Api user id
	 * @var int
	 */
	private static $apiUserId;

	/**
	 * Request options
	 * @var array
	 */
	private static $requestOptions = [
		'auth' => ['portal', 'portal']
	];

	/**
	 * Request headers
	 * @var array
	 */
	private static $requestHeaders = [
		'Content-Type' => 'application/json',
		'X-ENCRYPTED' => 0,
	];

	/**
	 * Details about logged in user
	 * @var array
	 */
	private static $authUserParams;

	public function setUp()
	{
		parent::setUp();
		static::$url = \AppConfig::main('site_URL') . 'api/webservice/';
	}

	/**
	 * Testing add configuration
	 */
	public function testAddConfiguration()
	{
		$webserviceApps = \Settings_WebserviceApps_Record_Model::getCleanInstance();
		$webserviceApps->set('type', 'Portal');
		$webserviceApps->set('status', 1);
		$webserviceApps->set('name', 'portal');
		$webserviceApps->set('acceptable_url', 'http://portal2/');
		$webserviceApps->set('pass', 'portal');
		$webserviceApps->save();
		static::$serverId = $webserviceApps->getId();

		$row = (new \App\Db\Query())->from('w_#__servers')->where(['id' => static::$serverId])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$serverId);
		$this->assertEquals($row['type'], 'Portal');
		$this->assertEquals($row['status'], 1);
		$this->assertEquals($row['name'], 'portal');
		$this->assertEquals($row['pass'], 'portal');
		static::$requestHeaders['X-API-KEY'] = $row['api_key'];

		$webserviceUsers = \Settings_WebserviceUsers_Record_Model::getCleanInstance('Portal');
		$webserviceUsers->save([
			'server_id' => static::$serverId,
			'status' => '1',
			'user_name' => 'demo@yetiforce.com',
			'password_t' => 'demo',
			'type' => '1',
			'language' => 'pl_pl',
			'popupReferenceModule' => 'Contacts',
			'crmid' => 0,
			'crmid_display' => '',
			'user_id' => \App\User::getActiveAdminId(),
		]);
		static::$apiUserId = $webserviceUsers->getId();
		$row = (new \App\Db\Query())->from('w_#__portal_user')->where(['id' => static::$apiUserId])->one();
		$this->assertNotFalse($row, 'No record id: ' . static::$apiUserId);
		$this->assertEquals($row['server_id'], static::$serverId);
		$this->assertEquals($row['user_name'], 'demo@yetiforce.com');
		$this->assertEquals($row['password_t'], 'demo');
		$this->assertEquals($row['language'], 'pl_pl');
	}

	/**
	 * Testing login
	 */
	public function testLogIn()
	{
		$request = \Requests::post(static::$url . 'Users/Login', static::$requestHeaders, \App\Json::encode([
					'userName' => 'demo@yetiforce.com',
					'password' => 'demo'
				]), static::$requestOptions);
		$response = \App\Json::decode($request->body, 0);
		$this->assertEquals($response->status, 1, $response->error->message);
		static::$authUserParams = $response->result;
		static::$requestHeaders['X-TOKEN'] = static::$authUserParams->token;
	}

	private static $recordId;

	/**
	 * Testing add record
	 */
	public function testAddRecord()
	{
		$recordData = [
			'accountname' => 'Api YetiForce Sp. z o.o.',
			'addresslevel5a' => 'Warszawa',
			'addresslevel8a' => 'Marszałkowska',
			'buildingnumbera' => 111,
			'legal_form' => 'PLL_GENERAL_PARTNERSHIP',
		];
		$request = \Requests::post(static::$url . 'Accounts/Record/', static::$requestHeaders, \App\Json::encode($recordData), static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
		static::$recordId = $response['result']['id'];
	}

	/**
	 * Testing edit record
	 */
	public function testEditRecord()
	{
		$recordData = [
			'accountname' => 'Api YetiForce Sp. z o.o. New name',
			'buildingnumbera' => 222,
		];
		$request = \Requests::put(static::$url . 'Accounts/Record/' . static::$recordId, static::$requestHeaders, \App\Json::encode($recordData), static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
	}

	/**
	 * Testing record list
	 */
	public function testRecordList()
	{
		$request = \Requests::get(static::$url . 'Accounts/RecordsList', static::$requestHeaders, static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
	}

	/**
	 * Testing get fields
	 */
	public function testGetFields()
	{
		$request = \Requests::get(static::$url . 'Accounts/Fields', static::$requestHeaders, static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
		$this->assertTrue(!empty($response['result']['fields']));
		$this->assertTrue(!empty($response['result']['blocks']));
	}

	/**
	 * Testing get privileges
	 */
	public function testGetPrivileges()
	{
		$request = \Requests::get(static::$url . 'Accounts/Privileges', static::$requestHeaders, static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
		$this->assertTrue(!empty($response['result']['standardActions']));
	}

	/**
	 * Testing get modules
	 */
	public function testGetModules()
	{
		$request = \Requests::get(static::$url . 'Modules', static::$requestHeaders, static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
		$this->assertTrue(!empty($response['result']['Accounts']));
	}

	/**
	 * Testing get api methods
	 */
	public function testGetMethods()
	{
		$request = \Requests::get(static::$url . 'Methods', static::$requestHeaders, static::$requestOptions);
		$response = \App\Json::decode($request->body, 1);
		$this->assertEquals($response['status'], 1, $response['error']['message']);
		$this->assertTrue(!empty($response['result']['BaseAction']));
		$this->assertTrue(!empty($response['result']['BaseModule']));
		$this->assertTrue(!empty($response['result']['Users']));
	}

	/**
	 * Testing delete configuration
	 */
	public function testDeleteConfiguration()
	{
		\Settings_WebserviceUsers_Record_Model::getInstanceById(static::$apiUserId, 'Portal')->delete();
		\Settings_WebserviceApps_Record_Model::getInstanceById(static::$serverId)->delete();

		$this->assertFalse(( new \App\Db\Query())->from('w_#__servers')->where(['id' => static::$serverId])->exists(), 'Record in the database should not exist');
		$this->assertFalse(( new \App\Db\Query())->from('w_#__portal_user')->where(['id' => static::$apiUserId])->exists(), 'Record in the database should not exist');
	}
}
