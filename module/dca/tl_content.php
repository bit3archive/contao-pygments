<?php

/**
 * Table tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['pygments'] = '{type_legend},type,headline;{text_legend},pygmentsSyntax,pygmentsFormatter,pygmentsStyle,code;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['pygmentsSyntax'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['pygmentsSyntax'],
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => array('Bit3\Contao\Pygments\DataContainer\Callbacks', 'getLexerOptions'),
	'eval'             => array(
		'mandatory'          => true,
		'includeBlankOption' => true,
		'chosen'             => true,
		'tl_class'           => 'long',
	),
	'load_callback'    => array
	(
		array('Bit3\Contao\Pygments\DataContainer\Callbacks', 'setRteSyntax')
	),
	'sql'              => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['pygmentsFormatter'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['pygmentsFormatter'],
	'default'          => 'html',
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => array('Bit3\Contao\Pygments\DataContainer\Callbacks', 'getFormatterOptions'),
	'eval'             => array(
		'mandatory'          => true,
		'includeBlankOption' => true,
		'chosen'             => true,
		'tl_class'           => 'long',
	),
	'sql'              => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['pygmentsStyle'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_content']['pygmentsStyle'],
	'exclude'          => true,
	'inputType'        => 'select',
	'options_callback' => array('Bit3\Contao\Pygments\DataContainer\Callbacks', 'getStyleOptions'),
	'eval'             => array(
		'includeBlankOption' => true,
		'chosen'             => true,
		'tl_class'           => 'long',
	),
	'sql'              => "varchar(32) NOT NULL default ''"
);
