<?php
namespace Main\NuberBundle\Service;

use Doctrine\ORM\EntityManager;



class CollectorManagerService{
    
    private $em;
    private $bm;

    
    function __construct(EntityManager $em,$bm)
    {
        $this->em = $em;
        $this->bm = $bm;

    }
    
    public function getData(){
        
        $updaters = $this->em->getRepository('MainNuberBundle:Updater')->findOneBy(array("status"=>false,"type"=>false));
        
        if($updaters){
            $project = $this->em->getRepository('MainNuberBundle:Project')->find(1);
            $qb = $this->em->createQueryBuilder()
                ->select('u')
                ->from('MainNuberBundle:User', 'u')
                ->where('u.project = :id');

            $user = $qb->getQuery()
                ->setParameter('id', 1)
                ->getOneOrNullResult(); 
               
            $key_ = $this->mc_encrypt($user."~".$user->getPassword(),$updaters->getSecret());    
          
            $key = $this->mc_encrypt($updaters->getClientId()."~".$key_,$project->getNuberSecret());
            
            $result = $this->curl_post(
                            "http://v3.nuber.ru/nuber/updater/getdata",
                            json_encode(array("key"=>$key,"id"=>$project->getNuberId())),
                            array(CURLOPT_HTTPHEADER=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8') )
                        );
                
            $answer =  json_decode($result); // var_dump($result);exit;  
            if($answer->message->final == false){
                if($answer->message->page != null){
                    $page = $this->setUpdatePageData($answer->message->page,$em);
                    if($page){
                        $updaters->setStatus(true);
                        $updaters->setUploaddate(new \DateTime()); 
                        $em->persist($updaters);
                        $em->flush();                   
                    }
                }
                if($answer->message->base != null){
                    $this->bm->setBase($answer->message->base); 
                    
                   // $base = $this->setUpdateBaseData($answer->message->base,$em);
                }
                $a = "update suc";
            } else {
                $updaters->setStatus(true); // Забрали данные
                $updaters->setUploadDate(new \DateTime());
                $this->em->persist($updaters);
                $this->em->flush();                
                $a = "update end";
            }
            
            
        }       
        return true;
    }
    
    protected function mc_encrypt($text, $key)
    {
        $td = mcrypt_module_open ("tripledes", '', 'cfb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);
        if (mcrypt_generic_init ($td, $key, $iv) != -1) 
            {
            $enc_text=base64_encode(mcrypt_generic ($td,$iv.$text));
            mcrypt_generic_deinit ($td);
            mcrypt_module_close ($td);
            return $this->strToHex($enc_text);
            }       
    }
    
    protected function strToHex($string)
    {
        $hex='';
        for ($i=0; $i < strlen($string); $i++)
        {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    protected function mc_decrypt($text, $key){
        $text = $this->hexToStr($text);        
        $td = mcrypt_module_open ("tripledes", '', 'cfb', '');
        $iv_size = mcrypt_enc_get_iv_size ($td);
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND);     
        if (mcrypt_generic_init ($td, $key, $iv) != -1) {
                $decode_text = substr(mdecrypt_generic ($td, base64_decode($text)),$iv_size);
                mcrypt_generic_deinit ($td);
                mcrypt_module_close ($td);
                return $decode_text;
        }
    }
        
    protected function hexToStr($hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2)
        {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }    

  /*  protected function mc_encrypt($encrypt, $mc_key) {
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
    }   */ 
    
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