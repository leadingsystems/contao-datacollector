<?php

namespace LeadingSystems\DataCollector;

\Contao\ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 0, array(
	'ls_dataCollector' => array(
		'ls_data_collector' => array(
			'tables' => array('tl_ls_data_collector')
		)
	)
));

if (TL_MODE == 'FE') {
	$GLOBALS['TL_HOOKS']['processFormData'][] = array('LeadingSystems\DataCollector\LsController', 'processFormData');
	$GLOBALS['TL_HOOKS']['loadFormField'][] = array('LeadingSystems\DataCollector\LsController', 'loadFormField');
	$GLOBALS['TL_HOOKS']['replaceInsertTags'][] = array('LeadingSystems\DataCollector\CustomInsertTags', 'customInsertTags');
}

if (TL_MODE == 'BE') {
	$GLOBALS['TL_CSS'][] = 'bundles/leadingsystemsdatacollector/be/css/style.css';
}