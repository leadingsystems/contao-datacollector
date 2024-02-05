<?php

namespace LeadingSystems\DataCollector;

use Contao\Controller;
use Contao\Database;
use Contao\FrontendTemplate;
use Contao\System;

class DataCollector {
	private $var_aliasOrId = null;
	private $arr_settings = null;

	public function __construct($var_dataCollectorAliasOrId) {
		if (!$var_dataCollectorAliasOrId) {
			return;
		}

		$this->var_aliasOrId = $var_dataCollectorAliasOrId;

		$this->getDataCollectorSettings();
	}

	private function getDataCollectorSettings() {
		$bln_getDataCollectorByAlias = preg_match('/[^0-9]/', $this->var_aliasOrId);
		$obj_dbres = Database::getInstance()
			->prepare("
				SELECT		*
				FROM		`tl_ls_data_collector`
				WHERE		`".($bln_getDataCollectorByAlias ? 'alias' : 'id')."` = ?
			")
			->limit(1)
			->execute($this->var_aliasOrId);

		if (!$obj_dbres->numRows) {
			return;
		}

		$this->arr_settings = $obj_dbres->first()->row();
	}

	public function output() {
		$obj_template = new FrontendTemplate('dataCollector_default');
		$obj_template->form = Controller::getForm($this->arr_settings['formId']);
		$obj_template->bln_dataHasBeenStored = $this->check_dataHasBeenStored();
		$obj_template->str_dataCollectorAlias = $this->arr_settings['alias'];
		return $obj_template->parse();
	}

	public function storeData($arr_submittedData) {
		if (!isset($_SESSION['ls_dataCollector']['collectors'][$this->arr_settings['alias']])) {
			$_SESSION['ls_dataCollector']['collectors'][$this->arr_settings['alias']] = array();
		}

		foreach($arr_submittedData as $str_key => $str_value) {
			$var_currentStoragePointer = &$_SESSION['ls_dataCollector']['collectors'][$this->arr_settings['alias']];

			$arr_keyParts = explode('--', $str_key);
			foreach ($arr_keyParts as $str_keyPart) {
				if (!isset($var_currentStoragePointer[$str_keyPart])) {
					$var_currentStoragePointer[$str_keyPart] = array();
				}

				$var_currentStoragePointer = &$var_currentStoragePointer[$str_keyPart];
			}

			$var_currentStoragePointer = $str_value;
		}

		if (isset($GLOBALS['LS_HOOKS']['ls_dataCollector']['storeData']) && is_array($GLOBALS['LS_HOOKS']['ls_dataCollector']['storeData'])) {
			foreach ($GLOBALS['LS_HOOKS']['ls_dataCollector']['storeData'] as $mccb) {
				$objMccb = System::importStatic($mccb[0]);
				$objMccb->{$mccb[1]}(
					$_SESSION['ls_dataCollector']['collectors'][$this->arr_settings['alias']],
					$arr_submittedData,
					$this->arr_settings
				);
			}
		}
	}

	private function check_dataHasBeenStored() {
		return isset($_SESSION['ls_dataCollector']['collectors'][$this->arr_settings['alias']]);
	}
}
