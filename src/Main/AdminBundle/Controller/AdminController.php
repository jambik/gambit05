<?php

namespace Main\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('MainCatalogBundle:Order')->findAll();
        $order_list = $em->getRepository('MainCatalogBundle:Order')->findBy(array(),array('id'=>'DESC'),10);
        $repo = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('MainCatalogBundle:Order');
                         
        $today = new \DateTime();
        
        $orderToday = $repo->createQueryBuilder('p')
                            ->select('p')                         
                            ->where('p.createdAt > :start')
                            ->andWhere('p.createdAt < :stop')
                            ->setParameter('start', $today->format('Y-m-d 00:00:00'))
                            ->setParameter('stop', $today->format('Y-m-d 23:59:59'))
                            ->getQuery()
                            ->getResult();
        $sum = $sum_today = 0;
        foreach($orders as $o){
            $sum += $o->getSumm(); 
        }
        foreach($orderToday as $o){
            $sum_today += $o->getSumm(); 
        }        
        
        $users = $em->getRepository('MainNuberBundle:User')->findAll();
        $reg_users = $em->getRepository('MainNuberBundle:User')->findBy(array('isReg'=>true));
        
        $repo_u = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('MainNuberBundle:User');
        $user_today = $repo_u->createQueryBuilder('p')
                            ->select('p')                         
                            ->where('p.createdAt > :start')
                            ->andWhere('p.createdAt < :stop')
                            ->setParameter('start', $today->format('Y-m-d 00:00:00'))
                            ->setParameter('stop', $today->format('Y-m-d 23:59:59'))
                            ->getQuery()
                            ->getResult();
        $lastTime = (new \DateTime())->modify('-3 MINUTE');  
                     
        $user_online = $repo_u->createQueryBuilder('p')
                            ->select('p')                         
                            ->where('p.lastStep > :start')
                            ->andWhere('p.lastStep < :stop')
                            ->setParameter('start', $lastTime->format('Y-m-d H:i:s'))
                            ->setParameter('stop', $today->format('Y-m-d H:i:s'))
                            ->getQuery()
                            ->getResult();                            
                  
        return $this->render('MainAdminBundle:Main:index.html.twig',array(
            'order_all' => count($orders),
            'order_today' => count($orderToday),
            'sum' =>$sum,
            'sum_today' => $sum_today,
            'all_users' => count($users),
            'reg_user' => count($reg_users),
            'user_today' => count($user_today),
            'user_online' => $user_online,
            'order_list' => $order_list,
        ));
    }
}
