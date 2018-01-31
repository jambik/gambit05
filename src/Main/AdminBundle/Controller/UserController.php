<?php

namespace Main\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('MainNuberBundle:User')->findAll(array(), array('id' => 'DESC'));

        return $this->render('MainAdminBundle:User:index.html.twig', array(
            'users' => $users,

        ));
    }
    
}