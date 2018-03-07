<?php

namespace LeadingSystems\DataCollector;

class CustomInsertTags {
	public function __construct() {
	}

	public function customInsertTags($strTag) {
		global $objPage;
		if (!preg_match('/^dataCollector([^:]*)(::(.*))?$/', $strTag, $matches)) {
			return false;
		}
		$tag = isset($matches[1]) ? $matches[1] : '';
		$params = isset($matches[3]) ? $matches[3] : '';

		switch ($tag) {
			case 'Output':
				$var_dataCollectorIdOrAlias = $params;
				$obj_dataCollector = new DataCollector($var_dataCollectorIdOrAlias);
				return \Controller::replaceInsertTags($obj_dataCollector->output(), false);
				break;
		}

		return false;
	}
}