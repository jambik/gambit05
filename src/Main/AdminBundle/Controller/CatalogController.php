<?php

namespace Main\AdminBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\CatalogBundle\Entity\ProductGroup;
use Main\CatalogBundle\Entity\ProductDiscount;
use Main\CatalogBundle\Entity\DiscountCondition;
use Main\CatalogBundle\Entity\Product;
use Main\NuberBundle\Entity\Updater;
use Main\CatalogBundle\Entity\ProductImage;
use Main\CatalogBundle\Entity\ProductModifier;


class CatalogController extends Controller
{
    
    public function refreshTreeAction(){
        $em = $this->getDoctrine()->getManager();
        $list = array();
        $productGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        foreach($productGroup as $pG){
            $parent = ($pG->getParentGroup() == null)?0:$pG->getParentGroup()->getId();
            $list[$parent][$pG->getId()] =  $pG;    
        }

        $response = new Response(json_encode($this->build_tree($list,0)));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
    
    protected function build_tree($cats,$parent_id,$only_parent = false){
        if(is_array($cats) and isset($cats[$parent_id])){
            $tree = '<ul data-lavel="'.$parent_id.'">';
            if($only_parent==false){
                foreach($cats[$parent_id] as $cat){
                    $tree .= '<li><a data-group-id="'.$cat->getId().'">'.$cat->getName()."</a>";
                    $tree .=  $this->build_tree($cats,$cat->getId());
                    $tree .= '</li>';
                }
            }elseif(is_numeric($only_parent)){
                $cat = $cats[$parent_id][$only_parent];
                $tree .= '<li><a data-group-id="'.$cat->getId().'">'.$cat->getName().'</a>';
                $tree .=  $this->build_tree($cats,$cat->getId());
                $tree .= '</li>';
            }
            $tree .= '</ul>';
        }
        else return null;
        return $tree;
    }
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $productGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        foreach($productGroup as $pG){
            $parent = ($pG->getParentGroup() == null)?0:$pG->getParentGroup()->getId();
            $list[$parent][$pG->getId()] =  $pG;    
        }
       
        
        return $this->render('MainAdminBundle:Catalog:index.html.twig',array(
            "productGroup" => $productGroup,
            "productTree" => $this->build_tree($list,0)
            
        ));
    }
    
    public function ImgUploadAction(Request $request){
        
        $files = array();
        $uploaddir = '/var/www/gambit/web/img/tmp/';
        
        foreach( $_FILES as $file ){
            if( move_uploaded_file( $file['tmp_name'], $uploaddir . basename($file['name']) ) ){
                $files[] = "/img/tmp/" . $file['name'];
            } 
        }
        
        $response = new Response(json_encode(array('files' => $files )));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;
        
    }
    
    public function delRecommendedAction(Request $request,ProductGroup $group){
        $em = $this->getDoctrine()->getManager();
        $id  = $request->request->get('id'); 
        
        $group_ = $em->getRepository('MainCatalogBundle:ProductGroup')->find($id);
        $group->removeMeRecommended($group_);
       // $group->setSynx(false);
        $em->persist($group);
        $em->flush();
        
      //  $this->get('update_manager')->set(array("product"=>true),$group->getProject());
       // $this->get('update_manager')->send($group->getProject());
        
        $response = new Response(json_encode(array()));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;  
        
    }
    
    public function getRecommendedAction(ProductGroup $group){
        $em = $this->getDoctrine()->getManager();
        $rec = $group->getMeRecommended();
        $imgs = array();
        $imgs = $group->getImages();
        
        if(count($imgs) > 0){
            foreach($imgs as $i){
                $src = ($i->getUrlNuberUpload() != null)?$i->getUrlNuberUpload():$i->getUrlOriginal();
                $img[] = array('id'=>$i->getId(),'src'=>$src);
            }
        } else {
            $img[] = array('id'=>0,'src'=>'/img/no-photo.png');
        }        
        
        $data = array(
            'id' => $group->getId(),
            'name'=> $group->getName(),
            'alias' =>$group->getAlias(),
            'desc' => $group->getDescription(),
            'parent' =>($group->getParentGroup())?array('id'=>$group->getParentGroup()->getId(),'name'=>$group->getParentGroup()->getName()):array('id'=>0,'name'=>'Без родительской категории'),
            'addInfo' => $group->getAdditionalInfo(),
            'seo_title' => $group->getSeoTitle(),
            'seo_text'=>$group->getSeoText(),
            'seo_keys'=>$group->getSeoKeywords(),
            'seo_desc'=>$group->getSeoDescription(),
            'hide_by_url'=>($group->getHideAliasByURL())?1:0,
            'img'=>$img
        );
        
        $groups = $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
       
       
        $res = $filter = array();
        foreach($rec as $val){
            $res[] = array("id"=>$val->getid(),"name"=>$val->getName());
            $filter[] = $val->getId();
        }
        
        foreach($groups as $val){
            if($val->getId() != $group->getId() && !in_array($val->getId(),$filter)){
                $list[] = array('id'=>$val->getId(),'name'=>$val->getName());
            } 
        }
        
        $response = new Response(json_encode(array("rec"=>$res,'data'=>$data)));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
    
    public function groupSaveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $data  = json_decode($request->request->get('data'));
        if($data->id == 0){
            $group = new ProductGroup();
            $group->setNuberId(0);
            $group->setIsIiko(false);
            $group->setIsIncludedInMenu(true);
        } else {
            $group = $em->getRepository('MainCatalogBundle:ProductGroup')->find($data->id);
        }
       
        if($data->parentGroup != 0){
            $ParentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->find($data->parentGroup); 
            if($ParentGroup){
                $group->setParentGroup($ParentGroup);
            }   
        } else {
            $group->setParentGroup(NULL);   
        }
       
        if($data->img == '/img/no-photo.png'){
            $imgGroup = $em->getRepository('MainCatalogBundle:ProductImage')->findOneBy(array('group'=>$group)); 
            if($imgGroup){
                $em->remove($imgGroup);
                $em->flush();
            }       
        } else {
            $imgGroup = $em->getRepository('MainCatalogBundle:ProductImage')->findOneBy(array('group'=>$group));
            $uploaddir = '/var/www/gambit/web/img/category/'.$data->alias;
            if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );  
            $img_path = $uploaddir."/".basename($data->img);
            copy('/var/www/gambit/web/'.$data->img,$img_path);
            
            if($imgGroup){
                $imgGroup->setUrlNuberUpload('/img/category/'.$data->alias.'/'.basename($data->img));
                $em->persist($imgGroup);
                $em->flush();
            } else {
                $imgGroup = new ProductImage();
                $imgGroup->setUrlNuberUpload('/img/category/'.$data->alias.'/'.basename($data->img));
                $imgGroup->setGroup($group);
                $imgGroup->setNuberId(0);
                $imgGroup->setIsIiko(false);
                $imgGroup->setUploadDate(new \DateTime());
                $em->persist($imgGroup);
                $em->flush();                
            }
            
        }
        
        $group->setName($data->name);
        $group->setSynx(null);
        $group->setAlias($data->alias);
        $group->setAdditionalInfo($data->addInfo);
        $group->setHideAliasByURL(($data->urlhide == 1)?true:false);
        $group->setDescription($data->desc);
        $group->setSeoDescription($data->seo_desc);
        $group->setSeoKeywords($data->seo_keys);
        $group->setSeoText($data->seo_text);
        $group->setSeoTitle($data->seo_title); 
      
        $em->persist($group);
        $em->flush();
         
        $this->addUpdateRow($em);
  
        $response = new Response(json_encode($data->urlhide));
        $response->headers->set('Content-Type','application/json') ;   
        
         return $response; 
    }
    
    protected function addUpdateRow($em){
        $project = $em->getRepository('MainNuberBundle:Project')->find(1);
        $upd = $em->getRepository('MainNuberBundle:Updater')->findOneBy(array("status"=>0,"project"=>$project));
      
        if(!$upd){      
            $upd = new Updater();         
            $upd->setProject($project);       
            $upd->setType(1);       
            $upd->setSecret($upd->genHash());
            $upd->setProduct(true);
           
            $em->persist($upd);
            $em->flush();
        } else {    
             $upd->setProduct(true);  
             $em->persist($upd);
             $em->flush();              
        }
        
        
    }
    
    public function saveProductDataAction(Request $request,Product $product){
        $em = $this->getDoctrine()->getManager();
        $data  = json_decode($request->request->get('data'));
      
        if($data->id == 0){
            $product = new Product();
            $product->setNuberId(0);
            $product->setIsIiko(true);
            $product->setIsIncludedInMenu(true);
            $product->setDoNotPrintInCheque(false);
             
        }else{
            $product = $em->getRepository('MainCatalogBundle:Product')->find($data->id);
        }
      
        /* if($data->group != 0){
            $ParentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->find($data->group); 
            if($ParentGroup){
                $product->setParentGroup($ParentGroup);
            }   
        } else {
            $product->setParentGroup(NULL);   
        }  */

            
       /* if($data->img == '/img/no-photo.png'){
            $imgProd = $em->getRepository('MainCatalogBundle:ProductImage')->findOneBy(array('product'=>$product)); 
            if($imgProd){
                $em->remove($imgProd);
                $em->flush();
            }       
        } else {
            $imgProd = $em->getRepository('MainCatalogBundle:ProductImage')->findOneBy(array('product'=>$product));
            $uploaddir = '/var/www/gambit/web/img/product/'.$data->alias;
            if( ! is_dir( $uploaddir ) ) mkdir( $uploaddir, 0777 );  
            $img_path = $uploaddir."/".basename($data->img);
            copy('/var/www/gambit/web/'.$data->img,$img_path);
            
            if($imgProd){
                $imgProd->setUrlNuberUpload('/img/product/'.$data->alias.'/'.basename($data->img));
                $em->persist($imgProd);
                $em->flush();
            } else {
                $imgProd = new ProductImage();
                $imgProd->setUrlNuberUpload('/img/product/'.$data->alias.'/'.basename($data->img));
                $imgProd->setProduct($product);
                $imgProd->setNuberId(0);
                $imgProd->setIsIiko(false);
                $imgProd->setUploadDate(new \DateTime());
                $em->persist($imgProd);
                            
            }
        }
        */
      
        $product->setName($data->name);
        //$product->setSynx(null);
        $product->setCode($data->code);
        $product->setAlias($data->alias);
        $product->setPrice($data->price);
        $product->setWeight($data->weight);
        $product->setDescription($data->desc);
        $product->setEnergyAmount($data->energy);
        $product->setCarbohydrateAmount($data->carbon);
        $product->setFatAmount($data->fat);
        $product->setFiberAmount($data->fiber);
        $product->setAdditionalInfo($data->addInfo);
        $product->setSeoTitle($data->seo_title);
        $product->setSeoText($data->seo_text);
        $product->setSeoKeywords($data->seo_key);
        $product->setSeoDescription($data->seo_desc);
        
        $em->persist($product);
        $em->flush();        
        
        //$this->addUpdateRow($em);
        
        $response = new Response(json_encode('true'));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;            
    }
    
    public function setRecommendedAction(Request $request,ProductGroup $group){
        $em = $this->getDoctrine()->getManager();
        $id  = $request->request->get('id');
        
        $group_ = $em->getRepository('MainCatalogBundle:ProductGroup')->find($id);
        $group->addMeRecommended($group_);
       // $group->setSynx(false);
        $em->persist($group);
        $em->flush();
        
        //$this->get('update_manager')->set(array("product"=>true),$group->getProject());
        
        $response = new Response(json_encode(array('id'=>$group_->getId(),'name'=>$group_->getName())));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;           
    }
    
    
    public function getProductDataAction(Request $request,Product $product){
        if($request->isXmlHttpRequest()){
                $em = $this->getDoctrine()->getManager();
                $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$product->getId(),$this->getUser());
                
                
                $img =  $disc =array();
                $imgs = $product->getImages();
                $mod = array(array(),array());
                if(count($imgs) > 0){
                    foreach($imgs as $i){
                        $src = ($i->getUrlNuberUpload() != null)?$i->getUrlNuberUpload():$i->getUrlOriginal();
                        $img[] = array('id'=>$i->getId(),'src'=>$src);
                    }
                } else {
                    $img[] = array('id'=>0,'src'=>'/img/no-photo.png');
                }
                
                $mods = $product->getModifiers();
                
                if(count($mods)>0){
                    foreach($mods as $m){
                        $mod[$m->getRequared()][] = array(
                                    'id'=>$m->getId(),
                                    'name' => $m->getModifier()->getName(),
                                    'price' => $m->getModifier()->getPrice(),
                                    'minAmount' => $m->getMinAmount(),
                                    'maxAmount' => $m->getMaxAmount(),
                                    'defaultAmount' => $m->getDefaultAmount()
                                    );    
                    }    
                } 
                
                $discs = $product->getDiscount();
                $trueDisc = $product->getTrueDiscount();
                $isTrue = '';
                    if(count($discs)>0){
                        foreach($discs as $d){
                            if(count($trueDisc) > 0){
                                foreach($trueDisc as $td){
                                    if($td->getId() == $d->getId()){
                                        $isTrue = 'isTrue';
                                    }    
                                }
                            }
                            $disc[] = array(
                                        'id' => $d->getId(),
                                        'isTrue' => $isTrue,
                                        'title' => $d->getTitle(),
                                        'top' => $d->getTopInList(),
                                        'percent' => $d->getPercent(),
                                        'num' =>$d->getNum(),
                                        'active' => $d->getActive(),
                                        'created' => $d->getCreatedAt()->format('Y:m:d H:i'),
                                    );
                        }    
                    }
                
                $data = array(
                            'img' => $img,
                            'id' => $product->getId(),
                            'name' =>$product->getName(),
                            'code' => $product->getCode(),
                            'alias' => $product->getAlias(),
                            'parent' => array('id'=>$product->getParentGroup()->getid(),'name'=>$product->getParentGroup()->getName()),
                            'price' => $product->getPriceWithOutDiscount(),
                            'weight' => $product->getWeight(),
                            'desc' => $product->getDescription(),
                            'isIiko' => $product->getIsIiko(),
                            'created' =>$product->getCreatedAt()->format('Y:m:d H:i'),
                            'curPrice' =>$product->getPrice(),
                            'energy' =>$product->getEnergyAmount(),
                            'fat' => $product->getFatAmount(),
                            'fiber' => $product->getFiberAmount(),
                            'carbon' =>$product->getCarbohydrateAmount(),
                            'additional' =>$product->getAdditionalInfo(),
                            'seotitle' =>$product->getSeoTitle(),
                            'seotext' =>$product->getSeoText(),
                            'seokeys' =>$product->getSeoKeywords(),
                            'seodesc' =>$product->getSeoDescription(),
                            'discount' => (count($disc) > 0)?$disc:0,
                            'mod' => (count($mod) > 0)?$mod:0
                        );
            

            $response = new Response(json_encode(array('product'=>$data)));
            $response->headers->set('Content-Type','application/json') ;   
                
            return $response;            
        }
    }
    
    public function setProductDiscountAction(Request $request,Product $product){
        $em = $this->getDoctrine()->getManager();
        $id  = $request->request->get('id');        
        $title  = $request->request->get('title');        
        $percent  = $request->request->get('percent');        
        $sum  = $request->request->get('sum');  
        $active  = ($request->request->get('ac') == 'true')?true:false;  
        $in_top  = ($request->request->get('top') == 'true')?true:false;  
              

        $dis = new ProductDiscount();
        $dis->setTitle($title);
        $dis->setTopInList($in_top) ;
        $dis->setActive($active);
        $dis->setPercent($percent);
        $dis->setNum($sum);
                                                                            
        $dis->setProduct($product);
        
        $em->persist($dis);
        $em->flush();
        
        
        $response = new Response(json_encode(array()));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
    
    public function getProductDiscountCondListAction(Request $request,ProductDiscount $discount){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $conds = $discount->getCondition();
            
            if(count($conds) > 0){
                foreach($conds as $c){
                    $cond[] = array(
                            'id'=>$c->getId(),
                            'start'=>$c->getTimeStart()->format('Y.m.d H:i'),
                            'stop' => $c->getTimeStop()->format('Y.m.d H:i'),
                            'repeat' => ($c->getRepeat() == true)?'Да':'Нет',
                            'sum' => $c->getBuyCount(),
                            'func'=>$c->getFuncId()
                    );    
                }
                
            } else {
                $cond = array();
            }
            

            $response = new Response(json_encode($cond));
            $response->headers->set('Content-Type','application/json') ;   
                
            return $response;            
        }   
    }
    
    public function delDiscountConditionAction(Request $request, DiscountCondition $condition){
        if($request->isXmlHttpRequest()){    
            $em = $this->getDoctrine()->getManager(); 
            $pid = $condition->getDiscount()->getId();
            $em->remove($condition);
            $em->flush();
            
            $response = new Response(json_encode($pid));
            $response->headers->set('Content-Type','application/json') ;   
                
            return $response;       
        }   
    }
    
    public function setDiscountConditionAction(Request $request, ProductDiscount $discount){
        $em = $this->getDoctrine()->getManager();
        $func  = $request->request->get('func');        
        $start  = $request->request->get('start');        
        $stop  = $request->request->get('stop');        
        $limit  = ($request->request->get('limit') == "")?null:$request->request->get('limit');        
        $repeat  = ($request->request->get('repeat') == "true")?true:false;     
        
        $cond = new DiscountCondition();
        $cond->setBuyCount($limit);
        $cond->setRepeat($repeat);
        $cond->setFuncId($func);
        $cond->setDiscount($discount);
        $cond->setTimeStart(\DateTime::createFromFormat("j.n.Y H:i", $start));
        $cond->setTimeStop(\DateTime::createFromFormat("j.n.Y H:i", $stop));
        
        $em->persist($cond);
        $em->flush();
                                                                                
        $response = new Response(json_encode(array()));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;   
    }
    
    public function getProductDiscountListAction(Request $request,Product $product){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$product->getId(),$this->getUser());
            
            $discs = $product->getDiscount();
            $trueDisc = $product->getTrueDiscount();
            $isTrue = '';
            
            if(count($discs)>0){
                foreach($discs as $d){
                    foreach($trueDisc as $td){
                        if($td->getId() == $d->getId()){
                            $isTrue = 'isTrue';
                        }    
                    }
                    $disc[] = array(
                                'id' => $d->getId(),
                                'isTrue' => $isTrue,
                                'title' => $d->getTitle(),
                                'top' => $d->getTopInList(),
                                'percent' => $d->getPercent(),
                                'num' =>$d->getNum(),
                                'active' => $d->getActive(),
                                'created' => $d->getCreatedAt()->format('Y:m:d H:i'),
                            );
                }    
            }

            $response = new Response(json_encode($disc));
            $response->headers->set('Content-Type','application/json') ;   
                
            return $response;        
        }   
    }
        
    public function getProductListAction(ProductGroup $group){
        $em = $this->getDoctrine()->getManager();
         
        $product = $em->getRepository('MainCatalogBundle:Product')->findBy(array("parentGroup"=>$group));  
    
        $res = array();
        foreach($product as $val){
            $res[] = array("id"=>$val->getid(),"name"=>$val->getName(),"price"=>$val->getPrice(),'synx'=>$val->getSynx(),"img"=>count($val->getImages()),"orderBy"=>$val->getOrderBy());
        }
        $response = new Response(json_encode($res));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;     
    }
    
}
