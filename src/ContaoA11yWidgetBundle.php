<?php

namespace Weba11y\ContaoA11yWidget;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoA11yWidgetBundle extends Bundle
{
  public function getPath(): string
  {
    return \dirname(__DIR__);
  }
}