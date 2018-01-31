<?php
namespace Main\NuberBundle\Service;

use Doctrine\ORM\EntityManager;

use Main\CatalogBundle\Entity\Order;
use Main\NuberBundle\Entity\Updater;
use Main\IikoBundle\Entity\iiko;

class UpdateManagerService{
    
    private $em;
    
    function __construct(EntityManager $em){
        $this->em = $em;

    }
    
    public function set(array $update){
        $project = $this->em->getRepository('MainNuberBundle:Project')->find(1);
        $upd = $this->em->getRepository('MainNuberBundle:Updater')->findOneBy(array("status"=>0,"project"=>$project));
        if(count($update) > 0){
            if(!$upd){
                $upd = new Updater();
                $upd->setProject($project);
                $upd->setType(1);
                $upd->setSecret($upd->genHash());
                foreach($update as $key=>$val){
                    $upd->{"set".ucwords($key)}($val);    
                }
            } else {
                foreach($update as $key=>$val){
                    $upd->{"set".ucwords($key)}($val);    
                }  
            }
            $this->em->persist($upd);
            $this->em->flush(); 
        }    
    }
    
    public function setNuberUpdater($data){
        $updater = new Updater();
        $updater->setSecret($data->key); 
        $updater->setClientId($data->id); 
        $updater->setProduct($data->product); 
        $updater->setOrder($data->order); 
        $updater->setType(0);       
        $this->em->persist($updater);
        
        $this->em->flush();         
    }
    
    public function push(Order $order){
        $item_list = $this->em->getRepository('MainCatalogBundle:BasketItem')->findBy(array("order"=>$order,"status"=>1));
        
        $order->setSendError(true);
        $this->em->persist($order);
        $this->em->flush();
    
        $item = array();   
        foreach($item_list as $i){
            $item[] = array(
                "product_id" => $i->getProduct()->getNuberId(),
                "count"      => $i->getCount(),
                "created"    => $i->getCreatedAt(),
                'basket_id'  => $i->getId(),
                'parent_id'  => $i->getParent()                                                
            );    
        } 

        $orders =  array(
            "comment"   => $order->getComment(),
            "created"   => $order->getCreatedAt(),
            "city"      => 'Махачкала',
            'id'        => $order->getId(),
            "name"      => ($order->getUser()->getName() == null)?'Не указан':$order->getUser()->getName(),
            "phone"     => $order->getUser()->getPhone(),  
            "street"    => $order->getUser()->getStreet(),  
            "house"     => $order->getUser()->getHouse(),  
            "build"     => $order->getUser()->getBuild(), 
            "user_mail" => ($order->getUser()->getRegDate() != null)?$order->getUser()->getEmail():'не указан',  
            "apartment" => $order->getUser()->getApartment(),
            "isReg"     => ($order->getUser()->getIsReg())?1:0,
            "regDate"   => ($order->getUser()->getRegDate() != null)?$order->getUser()->getRegDate()->format('j.n.Y H:i'):null,
            "isPickUp"  => ($order->getIsPickUp())?1:0, 
            "item"      => $item,
           // "isCash"  => ($o->getIsCash())?1:0
        );
        $project = $this->em->getRepository('MainNuberBundle:Project')->find(1);
        
        $result = $this->curl_post(
                    "http://v3.nuber.ru/nuber/order/push",
                    json_encode(
                        array(
                                "order"   => $orders,
                                "project" => $project->getNuberId(),
                                )
                    ),
                    array(CURLOPT_HTTPHEADER=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8') )
                );

        if($result == 'ok'){
            $order->setSendError(false);
            $order->setSynx(new \DateTime());
            $this->em->persist($order);
            $this->em->flush();
            
            return true;
        } else {
            return false;
        }
    }


    public function send(){
        $project = $this->em->getRepository('MainNuberBundle:Project')->find(1);
        
        $sendUpd = $this->em->getRepository('MainNuberBundle:Updater')->findOneBy(array("status"=>false,"type"=>true));
       
        if($sendUpd){
            $result = $this->curl_post(
                "http://v3.nuber.ru/nuber/updater/new",
                json_encode(array(
                                "key"     => $sendUpd->getSecret(),
                                "id"      => $sendUpd->getid(),
                                "project" => $project->getNuberId(),
                                "product" => $sendUpd->getProduct(),
                                "order"   => $sendUpd->getOrder(),
                                "MailingMessage"   => false
                                )
                ),
                array(CURLOPT_HTTPHEADER=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8') )
            );
            $sendUpd->setStatus(true);
            $this->em->persist($sendUpd);
            $this->em->flush();
            
        }       
        
        return true;   
    }
    
    protected function mc_encrypt($encrypt, $mc_key) {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = trim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $mc_key, trim($encrypt), MCRYPT_MODE_ECB, $iv));
        $encode = base64_encode($passcrypt);
        
        return $encode;
    }
    
    protected function mc_decrypt($decrypt, $mc_key) {
       
        $decoded = base64_decode($decrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $mc_key, trim($decoded), MCRYPT_MODE_ECB, $iv));
        return $decrypted;
    }    
    
    protected function curl_post($url, $post = null, array $options = array()) {
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYHOST =>0,//unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
            CURLOPT_SSL_VERIFYPEER=>0, //unsafe, but the fastest solution for the error " SSL certificate problem, verify that the CA cert is OK"
            CURLOPT_SSLVERSION=>1,
            CURLOPT_POSTFIELDS => $post
        );
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch)){
            trigger_error(curl_error($ch));
        }        
       
        curl_close($ch);
        return $result;
    }
}