<?php
namespace LeadingSystems\DataCollector;

use Contao\Database;
use Contao\System;
use Contao\Widget;

class LsController {
	private $arr_formCollectorMapping = array();

	/**
	 * Current object instance (Singleton)
	 */
	protected static $objInstance;

	/**
	 * Prevent direct instantiation (Singleton)
	 */
	protected function __construct() {
		$this->getFormCollectorMapping();
	}

	/**
	 * Prevent cloning of the object (Singleton)
	 */
	private function __clone() {}

	/**
	 * Return the current object instance (Singleton)
	 */
	public static function getInstance() {
		if (!is_object(self::$objInstance))	{
			self::$objInstance = new self();
		}
		return self::$objInstance;
	}

	private function getFormCollectorMapping() {
		$obj_dbres_dataCollectors = Database::getInstance()
			->prepare("
				SELECT	`id`,
						`alias`,
						`formId`
				FROM	`tl_ls_data_collector`
			")
			->execute();

		while ($obj_dbres_dataCollectors->next()) {
			$this->arr_formCollectorMapping[$obj_dbres_dataCollectors->formId][$obj_dbres_dataCollectors->alias] = $obj_dbres_dataCollectors->id;
		}
	}

	public function processFormData($arr_submittedValues, $arr_formSettings, $arr_files, $arr_labels, $obj_form) {
		if (!isset($this->arr_formCollectorMapping[$obj_form->id]) || !is_array($this->arr_formCollectorMapping[$obj_form->id])) {
			return;
		}

		foreach ($this->arr_formCollectorMapping[$obj_form->id] as $int_dataCollectorId) {
			$obj_dataCollector = new DataCollector($int_dataCollectorId);
			$obj_dataCollector->storeData($arr_submittedValues);
		}
	}

	public function loadFormField(Widget $obj_widget, $str_formId, $arr_data, $obj_form) {
		if (!isset($this->arr_formCollectorMapping[$obj_form->id])) {
			return $obj_widget;
		}

        $session = System::getContainer()->get('dataCollector.session')->getSession();
        $session_dataCollector =  $session->get('lsDataCollector', []);

		/*
		 * A form could have multiple data collectors assigned but since all data collectors depending on the same
		 * form would have the same form data stored, we only have to consider the data stored in the first data collector
		 * when we prefill the form field.
		 */
		$var_currentStoragePointer = &$session_dataCollector['collectors'][key($this->arr_formCollectorMapping[$obj_form->id])];

		$arr_keyParts = explode('--', $obj_widget->name);
		foreach ($arr_keyParts as $str_keyPart) {
			if (!isset($var_currentStoragePointer[$str_keyPart])) {
				/*
				 * If we can't find a storage pointer for the key part, this means that there is no data stored
				 * that this form field could be pre-filled with and therefore we return the widget object unaltered.
				 */
				return $obj_widget;
			}

			$var_currentStoragePointer = &$var_currentStoragePointer[$str_keyPart];
		}

		if ($var_currentStoragePointer && !is_array($var_currentStoragePointer)) {
			$obj_widget->value = $var_currentStoragePointer;
		}

		$obj_widget->validate();

		return $obj_widget;
	}
}
