<?php
namespace Main\NuberBundle\Service;

use Doctrine\ORM\EntityManager;

use Main\CatalogBundle\Entity\Order;
use Main\CatalogBundle\Entity\BasketItem;

class OrderManagerService{
    
    private $em;
    private $um;
    
    function __construct(EntityManager $em, $um){
        $this->em = $em;
        $this->um = $um;
    }
    
    public function sendAll(){
        //$list = $this->em->getRepository('MainCatalogBundle:Order')->findBy(array("synx"=>NULL));
        return count($list);   
    }
    
    protected function setUserData($data,$user){
        $user->setName($data->getName());
        $user->setPhone($data->getPhone());
        $user->setStreet($data->getStreet());
        $user->setHouse($data->getHouse());
        $user->setBuild($data->getBuild());
        $user->setApartment($data->getApartment());
        
        $this->em->persist($user);
        $this->em->flush();        
    }
    
    public function autoComplete(){
        $lastTime = (new \DateTime())->modify('-4 MINUTE');  
        $endTime = (new \DateTime())->modify('-2 MINUTE');  
        $repo_u = $this->em->getRepository('MainNuberBundle:User');             
        $user = $repo_u->createQueryBuilder('p')
                            ->select('p')                         
                            ->where('p.lastStep > :start')
                            ->andWhere('p.lastStep < :stop')
                            ->setParameter('start', $lastTime->format('Y-m-d H:i:s'))
                            ->setParameter('stop', $endTime->format('Y-m-d H:i:s'))
                            ->getQuery()
                            ->getResult();       
        
        if(count($user) > 0){
            $s = false;
            foreach($user as $u){
                if(count($u->getBasket()) > 0){
                    $this->set(null,$u,"БРОШЕНЫЙ ЗАКАЗ");
                    $s = true;
                }
            }    
            if($s){
                $this->um->set(array("order"=>true));
            }
        }
    }
    
    public function set($form,$user,$comment = null){
        
        $basketItem = $this->em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));
        $pickUp = false;
        $isCash = true;        
        if($form != null){ 
            $data = $form->get('user')->getData();
            $this->setUserData($data,$user);
            
            $comment = $form->get('comment')->getData();
            $pickUp = ($form->get('isPickUp')->getData() == 1)?true:false;
           
        }
       // echo count($basketItem);exit;
        $add = $addDis = "";
        $gifts = $this->em->getRepository('MainCatalogBundle:UserGift')->findBy(array('byReg'=>false));
        
        $basketSum = 0;
        foreach($basketItem as $b){
            $product = $this->em->getRepository('MainCatalogBundle:Product')->getProduct($this->em,$b->getProduct()->getId(),$user);
            $basketSum += $product->getPrice()*$b->getCount();
            
            if(count($b->getDiscount()) > 0){
                foreach($b->getDiscount() as $val){
                    $addDis .= "-".$val->getPercent()."% ";
                }
                $add .= $b->getProduct()->getName()." (Арт.: ".$b->getProduct()->getCode().") - ".$b->getCount()."шт - Скидка: ".$addDis." ;";
            }
        }
 
        $addComment = " | Следующие товары были куплены по скидке: ".$add."  |";
        if($add != ""){
            $comment = $comment.$addComment;    
        }
           
        $order = new Order();
        
        $order->setIsPickUp($pickUp);
        //$order->setIsCash(true);        
        $order->setUser($user);
        $order->setComment($comment);
        $order->setSumm($basketSum);
        $this->em->persist($user);
        $this->em->flush();

        foreach($gifts as $gf){
            if($gf->getPrice() <= $basketSum){
                $item = new BasketItem();
                $item->setProduct($gf->getProduct());
                $item->setCount(1);
                $item->setUser($user); 
                $item->setOrder($order);
                $item->setStatus(1);

                $this->em->persist($item);
                $this->em->flush();                  
             }     
        }
       
        foreach($basketItem as $item){
            $item->setStatus(1);
            $item->setOrder($order);
        
            $this->em->persist($user);
            $this->em->flush();        
        }
        
        return $order;
    }
    
} 