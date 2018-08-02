<?php
/**
 * EmailParser test class.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 */

namespace Tests\App;

class EmailParser extends \Tests\Base
{
	/**
	 * Test record instance.
	 *
	 * @var \App\EmailParser
	 */
	private static $parserRecord;
	/**
	 * Test clean instance.
	 *
	 * @var \App\EmailParser
	 */
	private static $parserClean;
	/**
	 * Test clean instance with module.
	 *
	 * @var \App\EmailParser
	 */
	private static $parserCleanModule;

	/**
	 * Testing instances creation.
	 */
	public function testInstancesCreation()
	{
		static::$parserClean = \App\EmailParser::getInstance();
		$this->assertInstanceOf('\App\EmailParser', static::$parserClean, 'Expected clean instance without module of \App\EmailParser');

		static::$parserCleanModule = \App\EmailParser::getInstance('Leads');
		$this->assertInstanceOf('\App\EmailParser', static::$parserCleanModule, 'Expected clean instance with module Leads of \App\EmailParser');

		$this->assertInstanceOf('\App\EmailParser', \App\EmailParser::getInstanceById(\Tests\Entity\C_RecordActions::createLeadRecord()->getId(), 'Leads'), 'Expected instance from lead id and module string of \App\TextParser');

		static::$parserRecord = \App\EmailParser::getInstanceByModel(\Tests\Entity\C_RecordActions::createLeadRecord());
		$this->assertInstanceOf('\App\EmailParser', static::$parserRecord, 'Expected instance from record model of \App\EmailParser');
	}

	/**
	 * Tests empty content condition.
	 */
	public function testEmptyContent()
	{
		$this->assertSame('', static::$parserClean
			->setContent('')
			->parse()
			->getContent(), 'Clean instance: empty content should return empty result');
	}

	/**
	 * Testing get content function.
	 */
	public function testGetContent()
	{
		$this->assertSame(['test0@yetiforce.com', 'test1@yetiforce.com' => 'Test One ', 'test2@yetiforce.com'], static::$parserClean
			->setContent('test0@yetiforce.com,Test One &lt;test1@yetiforce.com&gt;,test2@yetiforce.com,-,')
			->parse()
			->getContent(true), 'Clean instance: content should be equal');
	}

	/**
	 * Testing use value function.
	 */
	public function testUseValue()
	{
		$this->assertSame(['test0@yetiforce.com', 'test1@yetiforce.com' => 'Test One ', 'test2@yetiforce.com'], \App\EmailParser::getInstanceByModel(\Tests\Entity\C_RecordActions::createLeadRecord())
			->setContent('test0@yetiforce.com,Test One &lt;test1@yetiforce.com&gt;,test2@yetiforce.com,-,,$(record : email)$')
			->parse()
			->getContent(true), 'content should be equal');

		\Tests\Entity\C_RecordActions::createLeadRecord()->set('email', 'test3@yetiforce.com');
		\Tests\Entity\C_RecordActions::createLeadRecord()->save();
		$this->assertSame(['test0@yetiforce.com', 'test1@yetiforce.com' => 'Test One ', 'test2@yetiforce.com'], \App\EmailParser::getInstanceByModel(\Tests\Entity\C_RecordActions::createLeadRecord())
			->setContent('test0@yetiforce.com,Test One &lt;test1@yetiforce.com&gt;,test2@yetiforce.com,-,,$(record : email)$')
			->parse()
			->getContent(true), 'content should be equal');

		\Tests\Entity\C_RecordActions::createLeadRecord()->set('noapprovalemails', '1');
		\Tests\Entity\C_RecordActions::createLeadRecord()->save();
		$this->assertSame(['test0@yetiforce.com', 'test1@yetiforce.com' => 'Test One ', 'test2@yetiforce.com', 'test3@yetiforce.com'], \App\EmailParser::getInstanceByModel(\Tests\Entity\C_RecordActions::createLeadRecord())
			->setContent('test0@yetiforce.com,Test One &lt;test1@yetiforce.com&gt;,test2@yetiforce.com,-,,$(record : email)$')
			->parse()
			->getContent(true), 'content should be equal');
		$tmpInstance = \App\EmailParser::getInstanceByModel(\Tests\Entity\C_RecordActions::createLeadRecord());
		$tmpInstance->emailoptout = false;
		$this->assertSame(['test0@yetiforce.com', 'test1@yetiforce.com' => 'Test One ', 'test2@yetiforce.com', 'test3@yetiforce.com'], $tmpInstance->setContent('test0@yetiforce.com,Test One &lt;test1@yetiforce.com&gt;,test2@yetiforce.com,-,,$(record : email)$')
			->parse()
			->getContent(true), 'content should be equal');
		\Tests\Entity\C_RecordActions::createLeadRecord(false);
	}
}
