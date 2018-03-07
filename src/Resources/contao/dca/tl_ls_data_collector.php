<?php

namespace LeadingSystems\DataCollector;

$GLOBALS['TL_DCA']['tl_ls_data_collector'] = array(
	'config' => array(
		'dataContainer' => 'Table'
	),
	
	'list' => array(
		'sorting' => array(
			'mode' => 1,
			'flag' => 1,
			'fields' => array('title'),
			'disableGrouping' => true,
			'panelLayout' => 'sort,search,limit'			
		),
		
		'label' => array(
			'fields' => array('title', 'alias'),
			'format' => '<strong>%s</strong> <span style="font-style: italic;">(Alias: %s)</span>'
		),
		
		'global_operations' => array(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		
		'operations' => array(
			'edit' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array(
				'label'               => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		
		)	
	),
	
	'palettes' => array(
		'default' => '{title_legend},title,alias;formId;'
	),
	
	'fields' => array(
		'title' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['title'],
			'exclude' => true,
			'inputType' => 'text',
			'eval' => array('mandatory' => true, 'tl_class' => 'w50', 'maxlength'=>255),
			'sorting' => true,
			'flag' => 11,
			'search' => true
		),
		
		'alias' => array (
			'label'                   => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['alias'],
			'exclude' => true,
			'inputType'               => 'text',
			'eval'                    => array('rgxp'=>'alnum', 'doNotCopy'=>true, 'spaceToUnderscore'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'save_callback' => array (
				array('LeadingSystems\DataCollector\ls_data_collector', 'generateAlias')
			),
			'sorting' => true,
			'flag' => 11,
			'search' => true
		),

		'formId' => array(
			'label' => &$GLOBALS['TL_LANG']['tl_ls_data_collector']['formId'],
			'exclude' => true,
			'inputType' => 'select',
			'foreignKey' => 'tl_form.title',
			'filter' => true
		)
	)
);

class ls_data_collector extends \Backend {
	public function __construct() {
		parent::__construct();
	}

	public function generateAlias($varValue, \DataContainer $dc) {
		$autoAlias = false;

		// Generate an alias if there is none
		if ($varValue == '') {
			$autoAlias = true;
			$varValue = \StringUtil::generateAlias($dc->activeRecord->title);
		}
		$objAlias = \Database::getInstance()
			->prepare("
				SELECT	id
				FROM	tl_ls_data_collector
				WHERE	id = ?
					OR	alias = ?
			")
			->execute($dc->id, $varValue);

		// Check whether the alias exists
		if ($objAlias->numRows > 1) {
			if (!$autoAlias) {
				throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
			}
			$varValue .= '-' . $dc->id;
		}

		return $varValue;
	}
}