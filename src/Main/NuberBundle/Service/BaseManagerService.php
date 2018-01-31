<?php
namespace Main\NuberBundle\Service;

use Doctrine\ORM\EntityManager;
use Main\CatalogBundle\Entity\Product;
use Main\CatalogBundle\Entity\ProductDiscount;
use Main\CatalogBundle\Entity\DiscountCondition;
use Main\CatalogBundle\Entity\ProductGroup;
use Main\CatalogBundle\Entity\ProductImage;
use Main\CatalogBundle\Entity\ProductModifier;


class BaseManagerService{
    
    private $em;
    private $groups;
    private $products;
    private $project;
    private $mods;

    
    function __construct(EntityManager $em,$sc){
        $this->em = $em;
        $this->project = $this->em->getRepository('MainNuberBundle:Project')->find(1);

    }
    
    public function setBase($base){
        $this->initCurrentBase();

        foreach($base->group as $group){
            $this->addOrUpdateGroup($group,1);
        }
    
        foreach($base->item as $product){
            $this->addOrUpdateProduct($product,1);
        }
        foreach($base->modifier as $mod){
            $this->setModificators($mod);
        }
        //$this->delMods();
        
        return false;    
    }
    
    protected function setModificators($m){
        if($m->isDeleted == 0){
            if(count($m) > 0 ){
                if(isset($this->mods[$m->iikoId])){
                    $modif = $this->mods[$m->iikoId];
                    $modif->setNuberId($m->id);
                    $modif->setMaxAmount($m->maxAmount);
                    $modif->setMinAmount($m->minAmount);
                    $modif->setDefaultAmount($m->defaultAmount);

                }else{  
                 
                    $modif = new ProductModifier();
                    
                    $modif->setMaxAmount($m->maxAmount);
                    $modif->setMinAmount($m->minAmount);
                    $modif->setRequired($m->required);
                    $modif->setDefaultAmount($m->defaultAmount);
                    $modif->setIsIiko($m->isIiko);
                    $p = $this->em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$m->mod_product_id));
                    $p2 = $this->em->getRepository('MainCatalogBundle:Product')->findOneBy(array("nuber_id"=>$m->product_id));
                    $modif->setModifier($p2);
                    $modif->setNuberId($m->id);
                    $modif->setModProduct($p);
                }
                    
                $this->em->persist($modif);
                $this->em->flush();              
            } 
        } else {
            $modif = $this->mods[$m->id];
            if($modif){
                $this->em->remove($modif);
                $this->em->flush();
            }      
        }    
    }
    
    protected function delMods(){
        if(count($this->mods)> 0){
            foreach($this->mods as $mm){
       
                $this->em->remove($mm);
                $this->em->flush();
            }     
        }    
    }
    
    protected function addImages($val,$isIiko,$curItem){
        if(count($val) > 0 ){
            $list = $curItem->getImages();
            if(count($list) > 0){
                foreach($list as $img){
                    $imgs[$img->getNuberId()] = $img;
                }
            }else{$imgs= array(); }
            
            foreach($val as $im){
                if(isset($imgs[$im->id])){
                    unset($imgs[$im->id]);       
                }else{
                    $image = new ProductImage();   
                    
                    $image->setNuberId($im->id); 
                    $image->setUrlOriginal($im->url); 
                    $image->setUploadDate(new \DateTime()); 
                    $image->setIsIiko($isIiko);
                    $image->setProduct($curItem);
                    
                    $this->em->persist($image);
                    $this->em->flush();

                }
            } 
            if(count($imgs)> 0){
                foreach($imgs as $img){
                    
                    $this->em->remove($img);
                    $this->em->flush();
                }     
            }
        } 
    }
    
    public function addOrUpdateProduct($product,$isIiko){
        
        if(isset($this->products[$product->_id])){
            $p = $this->products[$product->_id];
        } else {
            $p = new Product();
        } 
        
        $p->setData($product);
        
        if($product->parent_group_id != null){
            $parentGroup = $this->em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$product->parent_group_id));
            $p->setParentGroup($parentGroup);
        }
        $this->em->persist($p);
        $this->em->flush(); 
                
        if(count($product->images) > 0){
            $this->addImages($product->images,$isIiko,$p);       
        }
        
        $curDiscount = $p->getDiscount();
        
        if(count($curDiscount) > 0){
            foreach($curDiscount as $cD){
                $curDiscountList[$cD->getNuberId()] = $cD;
            }
        }
        
        if(count($product->discount) > 0){
            foreach($product->discount as $disc){
                if(isset($curDiscountList[$disc->id])){
                    $d = $curDiscountList[$disc->id];
                   
                    $condList = $d->getCondition();
                    if(count($condList) > 0){
                        foreach($condList as $cond){
                            $this->em->remove($cond);
                            $this->em->flush();   
                        }    
                    }
                    
                } else {
                    $d = new ProductDiscount();
                }
                $d->setData($disc);
                $d->setProduct($p);
                
                $this->em->persist($d);
                $this->em->flush();
                
                if(count($disc->condition) > 0){
                    foreach($disc->condition as $cond){ 
                    
                        $c = new DiscountCondition();
                        $c->setData($cond);
                        
                        $c->setDiscount($d);
                        $this->em->persist($c);
                        $this->em->flush();     
                    }   
                }
                if(isset($curDiscountList[$d->getNuberId()])){
                    unset($curDiscountList[$d->getNuberId()]);
                }
            }
        }
        
        if(isset($curDiscountList) && count($curDiscountList) > 0){
            foreach($curDiscountList as $cD){
                $this->em->remove($cD);
                $this->em->flush();             
            }
        }
 
    }
    
    protected function initCurrentBase(){
        $g = $this->em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        $p = $this->em->getRepository('MainCatalogBundle:Product')->findAll();
        $m = $this->em->getRepository('MainCatalogBundle:ProductModifier')->findAll();  
        if(count($g) > 0){  
            foreach($g as $item){
                $this->groups[$item->getIikoId()] = $item;
            } 
        }
        if(count($p) > 0){
            foreach($p as $item){
                $this->products[$item->getIikoId()] = $item;
            } 
        }
        if(count($m) > 0){
            foreach($m as $item){
                $this->mods[$item->getId()] = $item;
            } 
        }
    }
    
    public function addOrUpdateGroup($group,$isIiko){
        
        if(isset($group->isDeleted)){
            $item = $this->groups[$group->_id];

            $this->em->remove($item);
            $this->em->flush();
            
        }else{
            if(isset($this->groups[$group->_id])){
                $g = $this->groups[$group->_id];
                $g->setData($group);
                
                if($group->parent_group_id != null){
                    $parentGroup = $this->em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$group->parent_group_id));
                    $g->setParentGroup($parentGroup);
                }
                
            }else{
                $g = new ProductGroup(); 
                $g->setData($group);
                
                if($group->parent_group_id != null){
                    $parentGroup = $this->em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$group->parent_group_id));
                    $g->setParentGroup($parentGroup);
                }

            } 
            
            $this->em->persist($g);
            $this->em->flush();
            
         /*   $list = $g->getMeRecommended();
            if($list){
                foreach($list as $li){
                    $g->removeMeRecommended($li);
                    $this->em->persist($g);
                    $this->em->flush();
                }
            }
            $ne = $group->rec;
            if(count($ne) > 0){
                foreach($ne as $n){
                    $g2 =  $this->em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array("nuber_id"=>$n));
                    $g->addMeRecommended($g2);
                    $this->em->persist($g);
                    $this->em->flush();
                }
            }
         */   
        }
    }
    
    public function getOneProduct($id){
        
        $product = $em->getRepository('MainCatalogBundle:Product')->find($id); 
        $dicounts = $em->getRepository('MainCatalogBundle:ProductDiscount')->getTrue($product,$this->em,$this->user);
        
        if($dicounts){
            $curPrice = $product->getPrice();
            $product->setPriceWithOutDiscount($curPrice);
            foreach($dicounts as $dicount){
                $product->addTrueDiscount($dicount);
                $product->setDiscountValue($dicount->getPercent()) ;    
            }
            $product->setIsPriceDiscount(true);
        }
        
        return $product;
        
    }
    
    
    
}