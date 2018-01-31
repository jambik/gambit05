<?php

namespace Main\NuberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request; 
use Main\NuberBundle\Entity\User;
use Main\CatalogBundle\Entity\BasketItem;
use Main\CatalogBundle\Entity\UserGift;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('MainNuberBundle:Default:index.html.twig');
    }
    
    public function RegisterAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $phone  = $request->request->get('phone'); 
        $name  = $request->request->get('name'); 
        $mail  = $request->request->get('mail'); 
        
        $curUser = $this->getUser();  
        $user = $em->getRepository('MainNuberBundle:User')->findOneBy(array("phone"=>$phone));
        $gift = array('id'=>0);
        if($user){
            $end = 0; 
            $this->container->get('fos_user.security.login_manager')->logInUser('main', $user); 
            $curUser = $this->getUser();  
        } else {
            if($curUser == null){
                $user = new User();
                $t = time();
                $user->setUserName("client-".$t);
                
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);     
                $password = $encoder->encodePassword("client-".$t, $user->getSalt());       
                $user->setPassword($password);
         
                $user->setIp($_SERVER["REMOTE_ADDR"]); 
                $user->setEmail("client-".$t."@mail.ru");
                $em->persist($user);
                $em->flush(); 
                
                $curUser = $user; 

            }
            if( !$curUser->getisReg()){
                $giftByReg = $em->getRepository('MainCatalogBundle:UserGift')->findOneBy(array('byReg'=>true));
                if($giftByReg){
                    $add = new BasketItem();
                    $add->setUser($curUser);
                    $add->setProduct($giftByReg->getProduct()); 
                    $add->setCount(1);
                    $add->setIsGift(true);
                    $add->setBaseId(0);
                    
                    $em->persist($add);
                    $em->flush();
                    
                    $gift = array(
                            'price'=>$giftByReg->getProduct()->getPrice(),
                            'name'=>$giftByReg->getProduct()->getName(),
                            'id'=>$giftByReg->getProduct()->getId(),
                            'href'=>'',
                            'group'=>$giftByReg->getProduct()->getParentGroup()->getName(),
                            'groupId'=>$giftByReg->getProduct()->getParentGroup()->getId()
                    );
                }    
            }
            
            $curUser->setPhone($phone);
            $curUser->setName($name);
            $curUser->setEmail($mail);
            $curUser->setIsReg(true);
            $curUser->setRegDate(new \DateTime());
            $em->persist($curUser);
            $em->flush();
            
            $end = 1; 
        }
    
        $response = new Response(json_encode(array("data"=>$end,"gift"=>$gift,'user'=>$curUser->getId())));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
    
    
    public function phoneAuthAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $phone  = $request->request->get('phone'); 
        $curUser = $this->getUser();
        
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $em->getRepository('MainNuberBundle:User')->findOneBy(array("phone"=>$phone));
        if($user){
            $this->container->get('fos_user.security.login_manager')->logInUser('main', $user);
            if($user->getId() != 11){
                
                $list = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$curUser));
                
                foreach($list as $l){
                    $l->setUser($user);
                    $em->persist($l);
                    $em->flush();    
                }
                
                
                $em->remove($curUser);    
            }
            $tpl_name = ($user->getName() == null)?'Привет':$user->getName();      
            $tpl = '<div class="container">
            <div class="col-sm-4">
                <h2><span>'.$tpl_name.'</span>, мы рады видеть вас снова!</h2>
                <h3>Предлагаем вашему вниманию наши акции</h3>
            </div>
            <div class="col-sm-4">
                <a href="item.html">
                    <img class="promo-drink" src="img/promo-drink.png">
                </a>
            </div>
            <div class="col-sm-4">
                <a href="item.html">
                    <img class="promo-pizza" src="img/promo-pizza.png">
                </a>
            </div>
        </div>';   
        }else{
            $curUser->setPhone($phone);
            $em->persist($curUser);
            $tpl = "";
        }
        $em->flush();
        
        $curUser = $this->getUser();
        
        $response = new Response(json_encode($curUser->getId()));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
}


