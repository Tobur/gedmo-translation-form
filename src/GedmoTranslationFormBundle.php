<?php

namespace GedmoTranslationFormBundle;

use GedmoTranslationFormBundle\DependencyInjection\GedmoTranslationFormExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class GedmoTranslationFormBundle extends Bundle
{
    /**
     * @return GedmoTranslationFormExtension
     */
    public function getContainerExtension()
    {
        return new GedmoTranslationFormExtension();
    }
}
