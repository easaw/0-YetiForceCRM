{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
* Contributor(s): YetiForce.com
********************************************************************************/
-->*}
{strip}
	<div class="typeRemoveModal" tabindex="-1">
		<div  class="modal fade">
			<div class="modal-dialog modal-lg ">
				<div class="modal-content">
					<div class="modal-header row no-margin">
						<div class="col-xs-12 paddingLRZero">
							<div class="col-xs-8 paddingLRZero">
								<h4>{App\Language::translate('LBL_TITLE_TYPE_DELETE', $MODULE)}</h4>
							</div>
							<div class="pull-right">
								<button class="btn btn-warning marginLeft10" type="button" data-dismiss="modal" aria-label="Close" aria-hidden="true">&times;</button>
							</div>
						</div>
					</div>
					<div class="modal-body row">
						<div class="col-xs-12">
							<div class="col-xs-12 paddingLRZero marginBottom10px">
								<div class="col-xs-4">
									<button class="btn btn-primary btn-sm typeSavingBtn" data-value="2">
										{App\Language::translate('LBL_DELETE_THIS_EVENT', $MODULE)}
									</button>
								</div>
								<div class="col-xs-8">
									{App\Language::translate('LBL_DELETE_THIS_EVENT_DESCRIPTION', $MODULE)}
								</div>
							</div>
							<div class="col-xs-12 paddingLRZero marginBottom10px">	
								<div class="col-xs-4">
									<button class="btn btn-primary btn-sm typeSavingBtn" data-value="3">
										{App\Language::translate('LBL_DELETE_FUTURE_EVENTS', $MODULE)}
									</button>
								</div>
								<div class="col-xs-8">
									{App\Language::translate('LBL_DELETE_FUTURE_EVENTS_DESCRIPTION', $MODULE)}
								</div>
							</div>
							<div class="col-xs-12 paddingLRZero marginBottom10px">	
								<div class="col-xs-4">
									<button class="btn btn-primary btn-sm typeSavingBtn" data-value="1">
										{App\Language::translate('LBL_DELETE_ALL_EVENTS', $MODULE)}
									</button>
								</div>
								<div class="col-xs-8">
									{App\Language::translate('LBL_DELETE_ALL_EVENTS_DESCRIPTION', $MODULE)}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="addEventRepeatUI">
		<div>
			<span>{App\Language::translate('LBL_REPEATEVENT', $MODULE_NAME)}&nbsp;{$RECURRING_INFORMATION['INTERVAL']} &nbsp;{App\Language::translate($RECURRING_INFORMATION['freqLabel'], $MODULE_NAME)}</span>
		</div>
		<div>
			<span>{$RECURRING_INFORMATION['repeat_str']}</span>
		</div>
		<div>
			{App\Language::translate('LBL_UNTIL', $MODULE)}&nbsp;
			{if isset($RECURRING_INFORMATION['COUNT'])} 
				{if $RECURRING_INFORMATION['COUNT'] eq 0} 
					{App\Language::translate('LBL_NEVER', $MODULE)}
				{else}
					{App\Language::translate('LBL_COUNT', $MODULE)}: &nbsp;{$RECURRING_INFORMATION['COUNT']}
				{/if}
			{else}
				{$RECURRING_INFORMATION['UNTIL']}
			{/if}
		</div>
	</div>
{/strip}
