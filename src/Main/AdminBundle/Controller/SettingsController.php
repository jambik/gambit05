<?php

namespace Main\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SettingsController extends Controller  
{
    
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();

        return $this->render('MainAdminBundle:Setting:index.html.twig');        
    }
    
}