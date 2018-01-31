<?php

namespace Main\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class GiftController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $gifts = $em->getRepository('MainCatalogBundle:UserGift')->findAll(array(), array('id' => 'DESC'));

        return $this->render('MainAdminBundle:Gift:index.html.twig', array(
            'gift' => $gifts,

        ));
    }
    
}