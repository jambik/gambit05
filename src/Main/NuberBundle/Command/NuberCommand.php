<?php
namespace Main\NuberBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Main\NuberBundle\Entity\Updater;
use Doctrine\ORM\EntityManager;
use Main\PageBundle\Entity\Page;
use Main\CatalogBundle\Entity\ProductGroup;
use Main\CatalogBundle\Entity\ProductModifier;
use Main\CatalogBundle\Entity\ProductImage;
use Main\CatalogBundle\Entity\Product;

class NuberCommand extends ContainerAwareCommand
{
     
    protected function configure()
    {
        $this
            ->setName('nuber:updater')
            ->setDescription('Greet someone')
            ->addArgument(
                'action',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        
      //  $om = $this->getContainer()->get('order_manager');
        //$om->autoComplete();
      
      //  $sm = $this->getContainer()->get('update_manager');
   //     $next = $sm->send();
        
     //   if($next){
     //       $cm = $this->getContainer()->get('collector_manager');
     //       $next = $cm->getData();            
     //   }
        
        if(true){
            $upm = $this->getContainer()->get('uploader_manager'); 
            $next = $upm->checkImg();   
        }   
        
        
        $response = json_encode(array("errorCode"=>0,"message"=>$next));
        $output->writeln($response);
        /*        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        
        
        */
    
        
    }
    
    protected function setUpdateBaseData($base,$em){
        if($base->group){
            foreach($base->group as $key=>$val){
                $g = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$val->id));
                if(!$g){
                    $g = new ProductGroup(); 
                    $g->setData($val);     
                    
                    if($val->parent_group != ""){
                        $parentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$val->parent_group_id));
                        $g->setParentGroup($parentGroup);
                    }
                    $em->persist($g);
                    $em->flush();
                    
                    if($val->images != null){
                        foreach($val->images as $im){
                            $image = new ProductImage();   
                            
                            $image->setNuberId($im->id); 
                            $image->setUrlOriginal($im->url); 
                            $image->setIsIiko($im->is_iiko);
                            $image->setUploadDate(new \DateTime());
                            $image->setGroup($g);
                            
                            $em->persist($image);
                            $em->flush();
                        }
                    }
                    
                }else {
                    $g->setData($val);
                    $em->persist($g);
                    $em->flush();
                }    
            }
        }
        
        if($base->item){
            foreach($base->item as $key=>$val){ 
                $p = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$val->id));
                if(!$p){
                    $p = new Product(); 
                    $p->setData($val);     
                    
                    if($val->parent_group != ""){
                        $parentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$val->parent_group_id));
                        $p->setParentGroup($parentGroup);
                    }
                    $em->persist($p);
                    $em->flush();
                    
                    if($val->images != null){
                        foreach($val->images as $im){
                            $image = new ProductImage();   
                            
                            $image->setNuberId($im->id); 
                            $image->setUrlOriginal($im->url); 
                            $image->setIsIiko($im->is_iiko);
                            $image->setUploadDate(new \DateTime());
                            $image->setProduct($p);
                            
                            $em->persist($image);
                            $em->flush();
                        }
                    }
                    
                }else {
                    $p->setData($val);
                    $em->persist($p);
                    $em->flush();
                }                
                
            }   
        }
        
        if($base->modifier){
            foreach($base->modifier as $key=>$val){ 
                $mod = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array("nuber_id"=>$val->id));
                if(!$mod){
                    $p = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$val->product_id));
                    $mp = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$val->mod_product_id));
                    
                    $mod = new ProductModifier();
                    
                    $mod->setNuberId($val->id);
                    $mod->setMaxAmount($val->maxAmount);
                    $mod->setMinAmount($val->minAmount);
                    $mod->setModProduct($mp);
                    $mod->setModifier($p);
                    $mod->setIsIiko($val->isIiko);
                   
                    $mod->setRequired($val->required);
                    $mod->setDefaultAmount($val->defaultAmount);
                    
                    $em->persist($mod);
                    $em->flush();                   
                } else {
                    $p = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$val->product_id));
                    $mp = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$val->mod_product_id));
                    
                    $mod->setNuberId($val->id);
                    $mod->setMaxAmount($val->maxAmount);
                    $mod->setMinAmount($val->minAmount);
                    $mod->setModProduct($mp);
                    $mod->setModifier($p);
                    $mod->setIsIiko($val->isIiko);
                   
                    $mod->setRequired($val->required);
                    $mod->setDefaultAmount($val->defaultAmount);
                    
                    $em->persist($mod);
                    $em->flush();    
                }
            }   
        }
        
        return true;    
    }
    
    
    protected function setUpdatePageData($pages,$em){
        foreach($pages as $key=>$val){
            
            $page = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$key));
           
            if(isset($val->isNuberDeleted) && $key > 0){
                $em->remove($page);
                $em->flush();    
            }else{
                if(!$page){    
                    $page = new Page();
                    $page->setMassPageData($val,$key);
                    if($val->parent_page_id > 0){
                        $parent = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$val->parent_page_id));
                        $page->setParentPage($parent);
                    }
                    $em->persist($page);
                    $em->flush();                
                } else {
                    $page->setMassPageData($val,$key);
                    if($val->parent_page_id > 0){
                        $parent = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$val->parent_page_id));
                        $page->setParentPage($parent);
                    }
                    $em->persist($page);
                    $em->flush();
                }
            }
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