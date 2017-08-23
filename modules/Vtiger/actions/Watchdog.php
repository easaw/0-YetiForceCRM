<?php

/**
 * Watchdog Action Class
 * @package YetiForce.Action
 * @copyright YetiForce Sp. z o.o.
 * @license YetiForce Public License 2.0 (licenses/License.html or yetiforce.com)
 * @author Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 * @author Radosław Skrzypczak <r.skrzypczak@yetiforce.com>
 */
class Vtiger_Watchdog_Action extends Vtiger_Action_Controller
{

	/**
	 * Function to check permission
	 * @param \App\Request $request
	 * @throws \App\Exceptions\NoPermittedToRecord
	 */
	public function checkPermission(\App\Request $request)
	{
		$moduleName = $request->getModule();
		$recordId = $request->getInteger('record');
		if (empty($recordId)) {
			if (!App\Privilege::isPermitted($moduleName, 'WatchingModule')) {
				throw new \App\Exceptions\NoPermittedToRecord('LBL_NO_PERMISSIONS_FOR_THE_RECORD');
			}
		} else {
			if (!App\Privilege::isPermitted($moduleName, 'DetailView', $recordId) || !App\Privilege::isPermitted($moduleName, 'WatchingRecords')) {
				throw new \App\Exceptions\NoPermittedToRecord('LBL_NO_PERMISSIONS_FOR_THE_RECORD');
			}
		}
		if ($request->has('user')) {
			$userList = array_keys(\App\Fields\Owner::getInstance()->getAccessibleUsers());
			if (!in_array($request->get('user'), $userList)) {
				throw new \App\Exceptions\NoPermittedToRecord('LBL_NO_PERMISSIONS_FOR_THE_RECORD');
			}
		}
	}

	public function process(\App\Request $request)
	{
		$moduleName = $request->getModule();
		$record = $request->getInteger('record');
		$state = $request->get('state');
		$user = false;
		if ($request->has('user')) {
			$user = $request->get('user');
		}
		if (empty($record)) {
			$watchdog = Vtiger_Watchdog_Model::getInstance($moduleName, $user);
			$watchdog->changeModuleState($state);
		} else {
			$watchdog = Vtiger_Watchdog_Model::getInstanceById($record, $moduleName, $user);
			$watchdog->changeRecordState($state);
		}
		Vtiger_Watchdog_Model::reloadCache();
		$response = new Vtiger_Response();
		$response->setResult($state);
		$response->emit();
	}
}
