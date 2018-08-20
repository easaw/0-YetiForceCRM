<?php
/**
 * Zip test class.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Sławomir Kłos <s.klos@yetiforce.com>
 */

namespace Tests\App;

class Zip extends \Tests\Base
{
	/**
	 * Testing instance from file with no file name provided.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenNoFileName()
	{
		$this->expectException(\App\Exceptions\AppException::class);
		\App\Zip::openFile(false);
	}

	/**
	 * Testing instance from not existing file.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenFileNotExists()
	{
		$this->expectException(\App\Exceptions\AppException::class);
		\App\Zip::openFile('tests/data/NxFile.zip')->close();
	}

	/**
	 * Testing instance from linux generated zip file.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testInstanceOpenLinuxFile()
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip');
		$this->assertInstanceOf('\App\Zip', $instanceOpen, 'Expected zip object instance');
	}

	/**
	 * Testing linux file unzip.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testUnzipLinuxFile()
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip');
		$instanceOpen->unzip('tests/tmp/TestLinux/');
		$this->assertFileExists('tests/tmp/TestLinux/manifest.xml');
		$this->assertFileExists('tests/tmp/TestLinux/languages/pl_pl/TestLinux.json');
		\vtlib\Functions::recurseDelete('tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'TestLinux');
	}

	/**
	 * Testing linux file extract.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testExtractLinuxFile()
	{
		$instanceOpen = \App\Zip::openFile('tests/data/TestLinux.zip');
		$instanceOpen->extract('tests/tmp/TestLinux/');
		$this->assertFileExists('tests/tmp/TestLinux/manifest.xml');
		$this->assertFileExists('tests/tmp/TestLinux/languages/pl_pl/TestLinux.json');
		\vtlib\Functions::recurseDelete('tests' . \DIRECTORY_SEPARATOR . 'tmp' . \DIRECTORY_SEPARATOR . 'TestLinux');
	}

	/**
	 * Testing file creation in not existent directory.
	 *
	 * @throws \App\Exceptions\AppException
	 */
	public function testCreateFileBadDir()
	{
		$zip = \App\Zip::createFile('tests/data/NxDir/NxFile.zip');
		$zip->addFromString('filename.txt', '<minimal content>');
		$this->assertFalse($zip->close());
		$this->assertFileNotExists('tests/data/NxDir/NxFile.zip');
	}
}
