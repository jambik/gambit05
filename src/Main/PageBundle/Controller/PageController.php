<?php

namespace Main\PageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse; 

use Main\NuberBundle\Entity\User;
use Main\NuberBundle\Entity\Updater;
use Main\CatalogBundle\Entity\Order;
use Main\CatalogBundle\Entity\Product;
use Main\NuberBundle\Form\SiteUserType;

class PageController extends Controller
{
    protected $um;
    protected $om;
    
    public function indexAction()
    {
        return $this->render('MainPageBundle:Page:index.html.twig');
    }


    public function getPageDetailAction($page=null,$alias){

        $em = $this->getDoctrine()->getManager();
        $parent =  $em->getRepository('MainPageBundle:Page')->findOneBy(array("alias"=>$alias,"parentPage"=>null));
        
        $page  = $em->getRepository('MainPageBundle:Page')->findOneBy(array("alias"=>$parent,"id"=>$page));
      
        $tpl = ($page->getTemplate() != null)?$page->getTemplate():"default";
        return $this->render('MainPageBundle:Page:'.$tpl.'.html.twig', array(
            "page" => $page
        ));         
    }
    
    public function getCatalogAction(Request $request){
        $routeParams = $request->attributes->get('_route_params');
        echo "123";exit;    
    }
    
    protected function newUser($em,$request){
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

        $this->container->get('fos_user.security.login_manager')->logInUser('main', $user);
        
        $response = new RedirectResponse($request->getUri());
        $response->headers->clearCookie('demo_user_id');
        $response->headers->setCookie(new Cookie('demo_user_id', $user->getId(), time() + (3600 * 24 * 300))); 
        $response->send();   
    
        return $user; 
    }
    
    public function getRecPage($page,$em){
        if($page){
            $pageGroup = $page->getProductRecommended();
            $prod = $url = null;    
            
            if($pageGroup){
                foreach($pageGroup as $key=>$val){
                    if($key == 3){ break; }
                    $url[$val->getId()] = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($val->getId());
                    $product = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$val),array('order_by'=>'ASC'),1);
                    if($product){
                        $prod[] = $product;
                    }
                }
            } 
            return array("product"=>$prod,"url"=>$url);
        } else {
            return array("product"=>array(),"url"=>array());
        }
    }
    
    
    public function getRecProduct($product,$em){
        $currentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->find($product->getParentGroup()->getId());
        $recommendedGroup = $currentGroup->getMeRecommended();
          
        $prod = $url = array();
        if($recommendedGroup){
            foreach($recommendedGroup as $key=>$val){
                if($key == 3){ break; }
                $url[$val->getId()] = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($val->getId());
                $prod[] = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$val),array('order_by'=>'ASC'),1);
            }
        } 
      
        return array("product"=>$prod,"url"=>$url);
    }
        
    public function getPageAction(Request $request){ 
                
        $this->um =  $this->container->get('update_manager');
        $this->om =  $this->container->get('order_manager');
        
        $routeParams = $request->attributes->get('_route_params');
        $em = $this->getDoctrine()->getManager();   
     
        $user = $formComplete = $group_url = $recPage = $recommended = null;
        $group = $bskt = $mod = array();
        $securityContext = $this->container->get('security.authorization_checker');
       
        $user_id = $request->cookies->get('demo_user_id');
       
        if($user_id != null){  
            if($securityContext->isGranted('ROLE_ADMIN')){ 
                $user = $this->getUser();
                $this->container->get('fos_user.security.login_manager')->logInUser('main', $user);

                if($user->getId() != $user_id){
                    $response = new RedirectResponse($request->getUri());
                    $response->headers->clearCookie('demo_user_id');
                    $response->headers->setCookie(new Cookie('demo_user_id', $user->getId(), time() + (3600 * 24 * 300))); 
                    $response->send();
                }
            }else{
               //   $user = $this->getUser();
               // echo $user_id;
                $userManager = $this->container->get('fos_user.user_manager');
                $user = $userManager->findUserBy(array("id"=>$user_id));
                $this->container->get('fos_user.security.login_manager')->logInUser('main', $user);   
                $user = $this->getUser();
                              
            }  
        } 
       
        $count = count($routeParams);
        $product_page = $basket = $product_detail = $group = $prod = null;
        if($user){
            $basket =  $em->getRepository('MainCatalogBundle:BasketItem')->getBasket($em,$user,0);
        }
        $group_ = $em->getRepository('MainCatalogBundle:ProductGroup')->findAll();
        foreach($group_ as $val){
             $group_url[$val->getId()] = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($val->getId());
        }
       
        if($basket){
            foreach($basket as $b){
                if(!is_null($b->getProduct())){
                    if($b->getProduct()->getType() == 'modifier'){
                         $mod = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array('modifier'=>$b->getProduct()));
                         $bskt[$mod->getModProduct()->getId()][] = $b;
                    } else {
                         $bskt[$b->getProduct()->getParentGroup()->getName()][] = $b;     
                    }
                }
            }
        }      
        if($count == 0){
            $page =  $em->getRepository('MainPageBundle:Page')->findOneBy(array("page_type"=>0));   
             
        } else {
            
            if(isset($routeParams["first"])){  
                $page =  $em->getRepository('MainPageBundle:Page')->findOneBy(array("alias"=>$routeParams["first"])); 
               
            }
            if(isset($routeParams["first"]) && $page != null && $page->getPageType() == 2 ){
                
                $product_detail = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$routeParams["alias"],$user,"alias");
                if(!is_null($product_detail)){
                    $page =  $em->getRepository('MainPageBundle:Page')->findOneBy(array("page_type"=>3));   
                    $recommended = $em->getRepository('MainCatalogBundle:Product')->getRecommendedItem($em,$user,$product_detail);
                    $mod = $this->getModList($product_detail,$em);
                }else{
                    $group = $em->getRepository('MainCatalogBundle:ProductGroup')->getGroupByAlias($routeParams,$em);
                    if($group){
                       $prod = $this->getProductByGroup($group,$em,100);    
                    }   
                }   
            } else {
                $alias = $routeParams["alias"];
               
                if($alias == 'work'){ header('Location: /work/');exit; }
             
                $parent = null;
                foreach($routeParams as $key=>$val){
                    if($key != "alias"){
                        $parent = $em->getRepository('MainPageBundle:Page')->findOneByAlias($val);
                        if (!$parent) {
                            throw $this->createNotFoundException('The product does not exist');
                        }
                    } else {
                        $page = $em->getRepository('MainPageBundle:Page')->findOneBy(array("alias"=>$alias,"parentPage"=>$parent));
                    }
                }  
            }
        }      
        
        $formComplete = $this->createForm( 'Main\CatalogBundle\Form\OrderType', new Order());
        $formComplete->handleRequest($request); 
                    
        if ($formComplete->isSubmitted() && $formComplete->isValid()) {
            $em = $this->getDoctrine()->getManager();
           // exit;            
            $order = $this->om->set($formComplete,$user);
            if(!$this->um->push($order)){
                $this->um->set(array("order"=>true));
            }
            
            return $this->redirectToRoute('route_page1', array('alias' => 'checkout-send'));
        } 
        
        if($page){
            $recPage = $this->getRecPage($page,$em);
            $children = $page->getChildrenPage();
            $tpl = ($page->getTemplate() != null)?$page->getTemplate():"default"; 
        } else {
             throw $this->createNotFoundException('The product does not exist');
        }
            
        return $this->render('MainPageBundle:Page:'.$tpl.'.html.twig', array(
            "page" => array(
                        "data"=>$page,
                        "complete_form" => ($formComplete != null)?$formComplete->createView():null,
                        "children"=>$children,
                        "group"=>$group,
                        "prod" => $prod,
                        "group_url" => ($group_url != null)?$group_url:"",
                        "basket"=>$bskt,
                        "product_detail" => $product_detail,
                        "product_detail_rec" => $recommended,
                        "RecGroup" => $recPage,
                        "gift" => $this->getGift($em,$user),
                        "mod"     => $mod,
                        'menu' => $this->getMenu($em)
                    ),
            "user" => $user,
        ));             
    }
    
    protected function getProductByGroup($group,$em,$limit){
        $engine = $this->container->get('templating');
        $result = '';
        $prod = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$group,'isIncludedInMenu'=>true)); 
        $user = $this->getUser();
        $rec_list = $em->getRepository('MainCatalogBundle:Product')->getRecommendedItem($em,$user,null,$group);
        $url = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($group->getId());
        $stop = 0;

        $repo = $this->getDoctrine()
                 ->getManager()
                 ->getRepository('MainCatalogBundle:Product');
                 
        $qb = $repo->createQueryBuilder('p');
        $qb->select('COUNT(p)');
        $qb->where('p.parentGroup = :id');
        $qb->andWhere('p.isIncludedInMenu = 1');
        $qb->setParameter('id', $group); 
        $_count = $qb->getQuery()->getSingleScalarResult();  

        foreach($prod as $key=>$val){
   
            if($stop == $limit ){ break; }
            
            $result .=  $engine->render('MainCatalogBundle:Product:product.html.twig',array(
                            "product" => $val,
                            "url"     => $url,
                            "rec"     => $rec_list,
                            "mod"     => $this->getModList($val,$em)
            ));  
      
            $stop++;  
        }    
        
        return array('data'=>$result,'count'=>$_count - $limit);
        
    }
    
    protected function getParentMenu($list,$parent,$em){
        $res = array();
        foreach($list as $l){    
            if($l->getParentGroup() != NULL && $l->getParentGroup()->getId() == $parent){
           
      
                $prod = $this->getProductByGroup($l,$em,3);  
                                     
                $res[$l->getOrderBy()] = array('item'=>$l,'parent'=>$this->getParentMenu($list,$l->getId(),$em),'product'=>$prod['data'], 'prodCount'=> $prod['count']);
                
            }
        }
        ksort($res);
        return $res;
    }
    
    protected function getMenu($em){
        $list = array();
        $menu = $em->getRepository('MainPageBundle:Menu')->findAll();
       
        $index = 0;
        foreach($menu as $me){
            if($me->getTypeId() == 1){   
                $pGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findBy(array('isIncludedInMenu'=>true));
                 
                foreach($pGroup as $g){
                    if($g->getParentGroup()== NULL){ 
                  
                        $list[$me->getAlias()][$g->getOrderBy()] = array('item'=>$g,'parent'=>$this->getParentMenu($pGroup,$g->getId(),$em));    
						
                    } 
                    
                }
            }  
        }

        return $list;
    }
    
    private function getGift($em,$user){
        $userBasket = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));
        $basketSum = 0;
        $res = "";
        foreach($userBasket as $ub){
            if(!is_null($ub->getProduct())){
                $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$ub->getProduct()->getId(),$user);
                $basketSum += $product->getPrice()*$ub->getCount(); //учесть скидки как то... 
            }  
        }
        
        $gifts = $em->getRepository('MainCatalogBundle:UserGift')->findBy(array(), array('id' => 'ASC'));
                                        
        foreach($gifts as $gf){
            $info = ($gf->getPrice() <= $basketSum)?$gf->getBodyFinal():$gf->getBody();
            $info = str_replace("%sum%",$gf->getPrice()-$basketSum,$info);
            $per = 100 - ($gf->getPrice()-$basketSum)*100/$gf->getPrice();
            $res .= "<div class='col-sm-4' id='gift-".$gf->getId()."' data-gift-price='".$gf->getPrice()."'>
                        <img src='".$gf->getImg()."'>
                        <p>
                            ".$info."                       
                        </p>
                        <div class='progress-bar'>
                        <span style='width:".$per."%'></span>
                        </div>
                     </div>"; 
        }      
       
        return $res;
    }
    
    function getModList(Product $product,$em){
        
        $mod = $product->getModifiers();
        $res = array();
        
        foreach($mod as $m){
            $prod = $m->getModifier();
          
            $res[$m->getRequared()][] = array('id'=>$m->getModifier()->getId(),
                                            'name'=>$prod->getName(),
                                            'price'=>$prod->getPrice(),
                                            'alias'=>$prod->getAlias(),
                                            'default'=>$m->getDefaultAmount(),
                                            'min'=>$m->getMinAmount(),
                                            'max'=>$m->getMaxAmount(),
                                            'img'=>(count($prod->getImages()) > 0)?$prod->getImages()[0]->getUrlNuberUpload():''
                                            );    
                                             
        }
           
        
        return $res;
    }
}