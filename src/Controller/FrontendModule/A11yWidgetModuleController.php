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
   * Template-Variable => [Modulfeld, Übersetzungsschlüssel für den Standardtext]
   */
  private const TEXTS = [
    'a11yTitle' => ['a11y_title', 'title'],
    'fabLabel' => ['a11y_fab_label', 'fab_label'],
    'closeLabel' => ['a11y_close_label', 'close_label'],
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
  ];

  public function __construct(private readonly TranslatorInterface $translator)
  {
  }

  protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
  {
    $this->addAssets();

    $template->widgetId = 'a11y-' . $model->id;

    foreach (self::TEXTS as $variable => [$field, $key]) {
      $template->$variable = $model->$field ?: $this->translator->trans('MSC.a11y_widget.' . $key, [], 'contao_default');
    }

    $template->enableFontSize = (bool) $model->a11y_enable_font_size;
    $template->enableReader = (bool) $model->a11y_enable_reader;
    $template->enableLinks = (bool) $model->a11y_enable_links;
    $template->enableSans = (bool) $model->a11y_enable_sans;

    return $template->getResponse();
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
