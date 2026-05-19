<?php

namespace Weba11y\ContaoA11yWidget\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Weba11y\ContaoA11yWidget\ContaoA11yWidgetBundle;

class Plugin implements BundlePluginInterface
{
  public function getBundles(ParserInterface $parser): array
  {
    return [
      BundleConfig::create(ContaoA11yWidgetBundle::class)
        ->setLoadAfter([ContaoCoreBundle::class]),
    ];
  }
}