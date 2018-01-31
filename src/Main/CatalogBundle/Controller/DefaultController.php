<?php

namespace Main\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainCatalogBundle:Default:index.html.twig');
    }
}
