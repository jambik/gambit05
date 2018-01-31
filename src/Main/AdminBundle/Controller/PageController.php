<?php

namespace Main\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Main\PageBundle\Entity\Page;
use Main\PageBundle\Entity\ProductByPage;
use Main\CatalogBundle\Entity\ProductGroup;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller  
{
    
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $page =  $em->getRepository('MainPageBundle:Page')->findAll();
        return $this->render('MainAdminBundle:Page:index.html.twig',array(
            'page' =>$page
        ));        
    }
    
    public function detailAction(Page $page){
        $em = $this->getDoctrine()->getManager();
        if(isset($_POST['page-meta-title'])){
            $page->setTitle($_POST['page-meta-title']);  
            $page->setAlias($_POST['page-alias']);  
            $page->setH1($_POST['page-meta-h1']);  
            $page->setHead($_POST['page-meta-head']);  
            $page->setMetaDescription($_POST['page-meta-description']);  
            $page->setMetaKeys($_POST['page-meta-key']);  
            $page->setPreviewText($_POST['page-meta-preview']);  
            $page->setTemplate($_POST['page-tpl']);  
            $page->setPageType($_POST['page-type']);  
           // $page->setContent($_POST['page-body']);  

            $em->persist($page);
            $em->flush();  
        }

        return $this->render('MainAdminBundle:Page:detail.html.twig',array(
            'page' =>$page
        ));        
    }

    public function getProductAction(ProductGroup $group){
        $list = $group->getProduct();
        $res = null;
        
        foreach($list as $l){
            $res[] = array('id'=>$l->getId(),'name'=>$l->getName());
        }

        return new Response(json_encode($res), 200, array(
            'Content-Type' => 'application/json'
        ));        
    }
    
    public function PbPsaveAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $prod = $request->request->get('prod');
        $group = $request->request->get('group');
        $place = $request->request->get('place');
        $page = $request->request->get('page');
        
        if($id == 0){
            $pbp = new ProductByPage();
        } else{
            $pbp = $em->getRepository('MainPageBundle:ProductByPage')->find($id);
        }
        $pbp->setWhere($place);
        $page = ($page == 0)?null:$em->getRepository('MainPageBundle:Page')->find($page);
        $prod = ($prod == 0)?null:$em->getRepository('MainCatalogBundle:Product')->find($prod);
        $group = ($group == 0)?null:$em->getRepository('MainCatalogBundle:ProductGroup')->find($group);
        $pbp->setProduct($prod);
        if($prod == null){
            $pbp->setGroup($group);
        }
        $em->persist($pbp);
        $em->flush();        
        
        return new Response(json_encode(true), 200, array(
            'Content-Type' => 'application/json'
        ));                
    }
 
     public function PbPEditAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        
        $item =  $em->getRepository('MainPageBundle:ProductByPage')->find($id);
        $res['setPage'] = $item->getPage()->getId();        
        $res['place'] = $item->getWhere();     
        $res['setProduct'] = (is_null($item->getProduct()))?0:$item->getProduct()->getId();
        $res['setGroup'] = (is_null($item->getGroup()))?0:$item->getGroup()->getId();
        
        if($res['setGroup'] > 0){
            $gr =   $em->getRepository('MainCatalogBundle:ProductGroup')->find($res['setGroup']);            
        }else{
            $res['setGroup'] = $item->getProduct()->getParentGroup()->getId();
            $gr =   $em->getRepository('MainCatalogBundle:ProductGroup')->find($item->getProduct()->getParentGroup()->getId());            
        }
        $pr = $gr->getProduct();
        foreach($pr as $p){ 
            $res['product'][] = array('id'=>$p->getId(),'name'=>$p->getName());
        }
        
        $page =  $em->getRepository('MainPageBundle:Page')->findAll();
        foreach($page as $p){
            $res['page'][] = array('id'=>$p->getId(),'name'=>$p->getTitle()); 
        }
        
        $group =  $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        foreach($group as $g){
            $res['group'][] = array('id'=>$g->getId(),'name'=>$g->getName()); 
        }
        
        return new Response(json_encode($res), 200, array(
            'Content-Type' => 'application/json'
        ));
        
     }    
    public function PbPAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $res['place'] = '';
        $res['setPage'] = $res['setProduct'] = $res['setGroup']= 0;
        $res['product'] = array();
        
        
        $page =  $em->getRepository('MainPageBundle:Page')->findAll();
        foreach($page as $p){
            $res['page'][] = array('id'=>$p->getId(),'name'=>$p->getTitle()); 
        }
        $group =  $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        foreach($group as $g){
            $res['group'][] = array('id'=>$g->getId(),'name'=>$g->getName()); 
        }
        
        if($id != 0){
            $item =  $em->getRepository('MainPageBundle:ProductByPage')->find($id);
            $res['setPage'] = $item->getPage()->getId();        
            $res['place'] = $item->getWhere();     
            $res['setProduct'] = (is_null($item->getProduct()))?0:$item->getProduct()->getId();
            $res['setGroup'] = (is_null($item->getGroup()))?0:$item->getGroup()->getId();
            
            if($res['setProduct'] != null){
                $product = $em->getRepository('MainCatalogBundle:Product')->find($res['setProduct']['id']);
                $list = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$product->getParentGroup()));
                foreach($list as $l){
                    $res['product'][] = array('id'=>$l->getId(),'name'=>$l->getName());     
                }
            }   
        }
        

        return new Response(json_encode($res), 200, array(
            'Content-Type' => 'application/json'
        ));
    }
          
    public function itemRecAction(){      
        $em = $this->getDoctrine()->getManager();
        $items =  $em->getRepository('MainPageBundle:ProductByPage')->findAll();
     
        return $this->render('MainAdminBundle:Page:rec.html.twig',array(
            'items' =>$items
        ));
    }
}