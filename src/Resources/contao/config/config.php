<?php

namespace LeadingSystems\DataCollector;

use Contao\ArrayUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;

ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, array(
	'ls_dataCollector' => array(
		'ls_data_collector' => array(
			'tables' => array('tl_ls_data_collector')
		)
	)
));

if (System::getContainer()->get('contao.routing.scope_matcher')->isFrontendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
	$GLOBALS['TL_HOOKS']['processFormData'][] = array('LeadingSystems\DataCollector\LsController', 'processFormData');
	$GLOBALS['TL_HOOKS']['loadFormField'][] = array('LeadingSystems\DataCollector\LsController', 'loadFormField');
	$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('LeadingSystems\DataCollector\CustomInsertTags', 'customInsertTags');
}

if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest(System::getContainer()->get('request_stack')->getCurrentRequest() ?? Request::create(''))) {
	$GLOBALS['TL_CSS'][] = 'bundles/leadingsystemsdatacollector/be/css/style.css';
}