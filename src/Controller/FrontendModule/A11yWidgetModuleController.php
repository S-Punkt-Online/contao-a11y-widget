<?php

namespace Weba11y\ContaoA11yWidget\Controller\FrontendModule;

use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(
  type: 'a11y_widget',
  category: 'miscellaneous',
  template: 'mod_a11y_widget'
)]
class A11yWidgetModuleController extends AbstractFrontendModuleController
{
  protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
  {
    $this->addAssets();

    $template->widgetId = 'a11y-' . $model->id;

    $template->a11yTitle = $model->a11y_title ?: 'Zugänglichkeit';
    $template->fabLabel = $model->a11y_fab_label ?: 'Zugänglichkeitseinstellungen öffnen';
    $template->closeLabel = $model->a11y_close_label ?: 'Panel schließen';

    $template->enableFontSize = (bool) $model->a11y_enable_font_size;
    $template->fontTitle = $model->a11y_font_title ?: 'Schriftgröße';
    $template->fontDecreaseLabel = $model->a11y_font_decrease_label ?: 'Schriftgröße verringern';
    $template->fontResetLabel = $model->a11y_font_reset_label ?: 'Schriftgröße auf 100 % zurücksetzen';
    $template->fontIncreaseLabel = $model->a11y_font_increase_label ?: 'Schriftgröße erhöhen';

    $template->enableReader = (bool) $model->a11y_enable_reader;
    $template->readerTitle = $model->a11y_reader_title ?: 'Modus ohne Ablenkungen';
    $template->readerText = $model->a11y_reader_text ?: 'Seite vereinfachen (Medien und Animationen ausblenden)';

    $template->enableLinks = (bool) $model->a11y_enable_links;
    $template->linksTitle = $model->a11y_links_title ?: 'Links hervorheben';
    $template->linksText = $model->a11y_links_text ?: 'Links unterstreichen und betonen';

    $template->enableSans = (bool) $model->a11y_enable_sans;
    $template->sansTitle = $model->a11y_sans_title ?: 'Einfache Schrift';
    $template->sansText = $model->a11y_sans_text ?: 'Schrift durch Sans-Serif ersetzen';

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