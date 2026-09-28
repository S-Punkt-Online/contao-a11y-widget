<?php

declare(strict_types=1);

namespace Weba11y\ContaoA11yWidget\Controller\FrontendModule;

use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsFrontendModule(
  type: 'a11y_widget',
  category: 'miscellaneous',
  template: 'mod_a11y_widget'
)]
class A11yWidgetModuleController extends AbstractFrontendModuleController
{
  /**
   * Template-Variable => [Modulfeld (null = nicht einstellbar), Übersetzungsschlüssel für den Standardtext]
   */
  private const TEXTS = [
    'a11yTitle' => ['a11y_title', 'title'],
    'fabLabel' => ['a11y_fab_label', 'fab_label'],
    'closeLabel' => ['a11y_close_label', 'close_label'],
    'resetLabel' => ['a11y_reset_label', 'reset_label'],
    'resetStatus' => [null, 'reset_status'],
    'fontTitle' => ['a11y_font_title', 'font_title'],
    'fontDecreaseLabel' => ['a11y_font_decrease_label', 'font_decrease_label'],
    'fontResetLabel' => ['a11y_font_reset_label', 'font_reset_label'],
    'fontIncreaseLabel' => ['a11y_font_increase_label', 'font_increase_label'],
    'readerTitle' => ['a11y_reader_title', 'reader_title'],
    'readerText' => ['a11y_reader_text', 'reader_text'],
    'linksTitle' => ['a11y_links_title', 'links_title'],
    'linksText' => ['a11y_links_text', 'links_text'],
    'sansTitle' => ['a11y_sans_title', 'sans_title'],
    'sansText' => ['a11y_sans_text', 'sans_text'],
    'spacingTitle' => ['a11y_spacing_title', 'spacing_title'],
    'spacingText' => ['a11y_spacing_text', 'spacing_text'],
    'contrastTitle' => ['a11y_contrast_title', 'contrast_title'],
    'contrastText' => ['a11y_contrast_text', 'contrast_text'],
  ];

  public function __construct(private readonly TranslatorInterface $translator)
  {
  }

  protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
  {
    $this->addAssets();

    $template->widgetId = 'a11y-' . $model->id;

    foreach (self::TEXTS as $variable => [$field, $key]) {
      $template->$variable = ($field ? $model->$field : '') ?: $this->translator->trans('MSC.a11y_widget.' . $key, [], 'contao_default');
    }

    $template->enableFontSize = (bool) $model->a11y_enable_font_size;
    $template->enableReader = (bool) $model->a11y_enable_reader;
    $template->enableLinks = (bool) $model->a11y_enable_links;
    $template->enableSans = (bool) $model->a11y_enable_sans;
    $template->enableSpacing = (bool) $model->a11y_enable_spacing;
    $template->enableContrast = (bool) $model->a11y_enable_contrast;

    $template->position = 'bottom-left' === $model->a11y_position ? 'left' : 'right';
    $template->colorScheme = \in_array($model->a11y_color_scheme, ['dark', 'auto'], true) ? $model->a11y_color_scheme : 'light';
    $template->accentStyle = $this->getAccentStyle((string) $model->a11y_color);

    return $template->getResponse();
  }

  /**
   * Eigene Akzentfarbe als CSS-Variablen; die Schriftfarbe darauf wird nach Kontrast gewählt
   */
  private function getAccentStyle(string $color): string
  {
    $color = ltrim(trim($color), '#');

    if (preg_match('/^[0-9a-f]{3}$/i', $color)) {
      $color = preg_replace('/(.)/', '$1$1', $color);
    }

    if (!preg_match('/^[0-9a-f]{6}$/i', $color)) {
      return '';
    }

    $luminance = 0.0;

    foreach ([0.2126, 0.7152, 0.0722] as $i => $weight) {
      $channel = hexdec(substr($color, $i * 2, 2)) / 255;
      $luminance += $weight * ($channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4);
    }

    // Kontrast zu Weiß vs. zu #1a1a1a (Luminanz ≈ 0.0103)
    $contrastWhite = 1.05 / ($luminance + 0.05);
    $contrastDark = ($luminance + 0.05) / 0.0603;

    return sprintf(
      '--a11y-custom-accent: #%s; --a11y-custom-accent-contrast: %s;',
      strtolower($color),
      $contrastWhite >= $contrastDark ? '#fff' : '#1a1a1a'
    );
  }

  private function addAssets(): void
  {
    $css = 'bundles/contaoa11ywidget/css/a11y-widget.css|static';
    $js = 'bundles/contaoa11ywidget/js/a11y-widget.js|static';

    if (!\in_array($css, $GLOBALS['TL_CSS'] ?? [], true)) {
      $GLOBALS['TL_CSS'][] = $css;
    }

    if (!\in_array($js, $GLOBALS['TL_JAVASCRIPT'] ?? [], true)) {
      $GLOBALS['TL_JAVASCRIPT'][] = $js;
    }
  }
}
