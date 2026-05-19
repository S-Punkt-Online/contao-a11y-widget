<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['a11y_widget'] =
  '{title_legend},name,type;' .
  '{a11y_legend},a11y_title,a11y_fab_label,a11y_close_label;' .
  '{template_legend:hide},customTpl;' .
  '{expert_legend:hide},cssID';


// Panel-Titel
$GLOBALS['TL_DCA']['tl_module']['fields']['a11y_title'] = [
  'exclude' => true,
  'inputType' => 'text',
  'eval' => [
    'tl_class' => 'w50'
  ],
  'sql' => "varchar(255) NOT NULL default ''"
];


// FAB aria-label
$GLOBALS['TL_DCA']['tl_module']['fields']['a11y_fab_label'] = [
  'exclude' => true,
  'inputType' => 'text',
  'eval' => [
    'tl_class' => 'w50'
  ],
  'sql' => "varchar(255) NOT NULL default ''"
];


// Close aria-label
$GLOBALS['TL_DCA']['tl_module']['fields']['a11y_close_label'] = [
  'exclude' => true,
  'inputType' => 'text',
  'eval' => [
    'tl_class' => 'w50'
  ],
  'sql' => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['palettes']['a11y_widget'] =
  '{title_legend},name,type;' .
  '{a11y_legend},a11y_title,a11y_fab_label,a11y_close_label;' .
  '{a11y_font_legend},a11y_enable_font_size,a11y_font_title,a11y_font_decrease_label,a11y_font_reset_label,a11y_font_increase_label;' .
  '{a11y_options_legend},a11y_enable_reader,a11y_reader_title,a11y_reader_text,a11y_enable_links,a11y_links_title,a11y_links_text,a11y_enable_sans,a11y_sans_title,a11y_sans_text;' .
  '{template_legend:hide},customTpl;' .
  '{expert_legend:hide},cssID';

$a11yTextFields = [
  'a11y_font_title',
  'a11y_font_decrease_label',
  'a11y_font_reset_label',
  'a11y_font_increase_label',
  'a11y_reader_title',
  'a11y_reader_text',
  'a11y_links_title',
  'a11y_links_text',
  'a11y_sans_title',
  'a11y_sans_text',
];

foreach ($a11yTextFields as $fieldName) {
  $GLOBALS['TL_DCA']['tl_module']['fields'][$fieldName] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
      'tl_class' => 'w50',
    ],
    'sql' => "varchar(255) NOT NULL default ''",
  ];
}



$a11yCheckboxFields = [
  'a11y_enable_font_size',
  'a11y_enable_links',
  'a11y_enable_sans',
  'a11y_enable_reader',
];

foreach ($a11yCheckboxFields as $fieldName) {
  $GLOBALS['TL_DCA']['tl_module']['fields'][$fieldName] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
      'tl_class' => 'w100 clr',
    ],
    'sql' => "char(1) NOT NULL default '1'",
  ];
}