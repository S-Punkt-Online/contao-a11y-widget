<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['a11y_widget'] =
  '{title_legend},name,type;' .
  '{a11y_legend},a11y_title,a11y_fab_label,a11y_close_label;' .
  '{a11y_font_legend},a11y_enable_font_size,a11y_font_title,a11y_font_decrease_label,a11y_font_reset_label,a11y_font_increase_label;' .
  '{a11y_options_legend},a11y_enable_reader,a11y_reader_title,a11y_reader_text,a11y_enable_links,a11y_links_title,a11y_links_text,a11y_enable_sans,a11y_sans_title,a11y_sans_text;' .
  '{template_legend:hide},customTpl;' .
  '{expert_legend:hide},cssID';


// Textfelder – leer bedeutet: Standardtext in der Sprache der Seite (siehe languages/*/default.php)
$a11yTextFields = [
  'a11y_title' => 'title',
  'a11y_fab_label' => 'fab_label',
  'a11y_close_label' => 'close_label',
  'a11y_font_title' => 'font_title',
  'a11y_font_decrease_label' => 'font_decrease_label',
  'a11y_font_reset_label' => 'font_reset_label',
  'a11y_font_increase_label' => 'font_increase_label',
  'a11y_reader_title' => 'reader_title',
  'a11y_reader_text' => 'reader_text',
  'a11y_links_title' => 'links_title',
  'a11y_links_text' => 'links_text',
  'a11y_sans_title' => 'sans_title',
  'a11y_sans_text' => 'sans_text',
];

foreach ($a11yTextFields as $fieldName => $defaultKey) {
  $GLOBALS['TL_DCA']['tl_module']['fields'][$fieldName] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
      'maxlength' => 255,
      'placeholder' => $GLOBALS['TL_LANG']['MSC']['a11y_widget'][$defaultKey] ?? '',
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
    'default' => true,
    'eval' => [
      'tl_class' => 'w100 clr',
    ],
    'sql' => ['type' => 'boolean', 'default' => true],
  ];
}
