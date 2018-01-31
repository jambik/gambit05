<?php

namespace Main\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request; 
use Main\CatalogBundle\Entity\ProductGroup;
use Main\CatalogBundle\Entity\DiscountType;
use Main\CatalogBundle\Entity\Product;
use Main\NuberBundle\Entity\User;
use Main\CatalogBundle\Entity\BasketItem;

class AjaxController extends Controller
{
    public function getMoreProductAction(ProductGroup $group, Request $request){
        if($request->isXmlHttpRequest()){
            $list = explode(",",$request->request->get('list'));
            $em = $this->getDoctrine()->getManager();
            $repo = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('MainCatalogBundle:Product');
                         
            $product = $repo->createQueryBuilder('p')
                            ->select('p')
                            ->where('p.parentGroup = :id')
                            ->andWhere('p.id NOT IN (:ids)')
                            ->andWhere('p.isIncludedInMenu = 1')
                            ->orderBy('p.order_by', 'ASC')
                            ->setParameter('id', $group->getId())
                            ->setParameter('ids', $list)
                            ->getQuery()
                            ->getResult();
                            
            $rec_list = $em->getRepository('MainCatalogBundle:Product')->getRecommendedItem($em,$this->getUser(),null,$group);               
                                                                 
            $result = "";
            $engine = $this->container->get('templating');
            $url  = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($group->getId());
            foreach($product as $key=>$val){
                if(!is_null($val)){
                    $result .=  $engine->render('MainCatalogBundle:Product:product.html.twig',array(
                                    "product"=>$val,
                                    "url" =>$url,
                                    "rec" =>$rec_list,
                                    "mod" =>$this->getModList($val,$em)
                                    
                    )); 
                }   
            }
            $response = json_encode(array("product"=>$result));            
            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));  
        }    
        
    }
    
    public function setPingTimeAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $user->setLastStep(new \DateTime());
            $em->persist($user);
            $em->flush();
        }
        return new Response(json_encode('ok'), 200, array(
            'Content-Type' => 'application/json'
        ));        
    }
    
    protected function getClearBasket($user,$em){
        $curBasketItem = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));
        foreach($curBasketItem as $c){
            $em->remove($c);
            $em->flush();    
        }                
    }
    
    public function getSaleProductAction(DiscountType $sale,Request $request){
        if($request->isXmlHttpRequest()){
            $res = "";
            $em = $this->getDoctrine()->getManager();
            $disc =  $sale->getDiscount();
            $stop = rand(0,count($disc)-1);
            foreach($disc as $k=>$d){
                if($k == $stop){
                    $product =  $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$d->getProduct()->getId(),$this->getUser());  
                } 
            }
            if(!is_null($product)){
                $img = $product->getImages();
                $img_url = $img[0]->getSrc();
                $remainder = ($product->getRemainderItem() > 0)?'<div class="counter">Осталось '.$product->getRemainderItem().' шт</div>':'';
                $res = '<article class="goods-item item-moment" data-group-title="'.$product->getParentGroup()->getName().'" data-is-discount="'.$product->getIsPriceDiscount().'" data-max-basket-count="'.$product->getMaxBasketCount().'">
                          <div class="coverImage">
                              <img src="'.$img_url.'">
                          </div>
                          <div class="bodyPlayer"></div>
                        '.$remainder.'
                        <div class="time-left">
                            '.$product->getTimeUntilTheEnd().'
                        </div>
                        <div class="info">
                            <a href="'.$product->getHref().'"><h4>'.$product->getName().'</h4></a>
                            <p>'.$product->getDescription().'</p>
                            <p class="price-moment"><span>'.$product->getPrice().'</span><i class="fa fa-rub"></i></p>
                            <p class="old-price"><span>'.$product->getPriceWithOutDiscount().'</span><i class="fa fa-rub"></i></p>
                            <button id="'.$product->getId().'" data-group-id="'.$product->getParentGroup()->getId().'" data-group-name="'.$product->getParentGroup()->getName().'" class="cart-buy">Добавить к заказу</button>
                        </div>
                    </article>';
            }
            return new Response(json_encode($res), 200, array(
                'Content-Type' => 'application/json'
            ));        
        }   
        
    }
    
    public function getRecPageAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $box = $request->request->get('box');
            $id = $request->request->get('page');
            
            $list = $em->getRepository('MainPageBundle:ProductByPage')->findBy(array('where'=>$box));
            $res = array();
            $result = '';
            foreach($list as $item){      
                if($item->getGroup() == null){
                    $res[] = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$item->getProduct()->getId(),$this->getUser()); 
                } else {
                    $products = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$item->getGroup()));
                    $stop = rand(0,count($products)-1);
                    foreach($products as $key=>$val){ 
                        if($key == $stop){
                            $res[] = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$val->getId(),$this->getUser());;
                        }
                    }                      
                }
            }
            
            foreach($res as $val){
                if(!is_null($val)){
                    $img = $val->getImages();
                    $img_url = $img[0]->getSrc();
                    $result .= "<div>
                                    <img class='item-img' src='".$img_url."'>
                                    <span class='product-info' data-product-parent-name='".$val->getParentGroup()->getName()."' data-product-parent-id='".$val->getParentGroup()->getId()."' data-product-name='".$val->getName()."' data-product-href='".$val->getHref()."'  data-product-id='".$val->getId()."' data-product-price='".$val->getPrice()."'></span>
                                    <a class='item-name' href='".$val->getHref()."'>".$val->getName()."</a>
                                    <button id='".$val->getId()."' data-group-id='".$val->getParentGroup()->getId()."' data-group-name='".$val->getParentGroup()->getName()."' class='buy basket-buy'><span class='rec-price'>".$val->getPrice()."</span><i class='fa fa-rub'></i></button>
                                </div>";
                }
            }
            
            return new Response(json_encode($result), 200, array(
                'Content-Type' => 'application/json'
            ));           
        }    
    }    
    
    public function recomendedAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $id_ = $request->request->get('id');
            $id = explode("-",$id_);
            $user = $this->getUser(); 
            
            $currentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->find($id[0]);
            $recommendedGroup = $currentGroup->getMeRecommended();
             // echo count($recommendedGroup);
            if(count($recommendedGroup) > 0){
                $earlierBasketItem = $em->getRepository('MainCatalogBundle:BasketItem')
                                        ->createQueryBuilder('b')
                                        ->select('b')
                                        ->where('b.user = :user')
                                        ->andWhere('b.status = -1')
                                        ->andWhere('b.status = 1')
                                        ->setParameter('user', $user)
                                        ->getQuery()
                                        ->getResult();  
                                                  
                foreach($recommendedGroup as $key=>$val){
                    if($key == $id[1]){ break; }
                    $url[$val->getId()] = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($val->getId());
                    $product[] = $em->getRepository('MainCatalogBundle:Product')->findBy(array('parentGroup'=>$val),array('order_by'=>'ASC'),$id[2]);
                }
                
                $engine = $this->container->get('templating');
               
                $result =  $engine->render('MainCatalogBundle:Product:commended.html.twig',array(
                                    "product"=>$product,
                                    "url" => $url
                    ));    
            } else {
                $result = "";
            }
            
            return new Response(json_encode($result), 200, array(
                'Content-Type' => 'application/json'
            ));            
        }    
    }
    
    public function getSearchResultAction(Request $request){
        if($request->isXmlHttpRequest()){
            $q = $request->request->get('q');
            $searchd = $this->get('iakumai.sphinxsearch.search');
            $result = $searchd->searchEx($q, array('GambitIndex'));
         
            $res = "";
            if(isset($result["matches"])){
                foreach($result["matches"] as $key=>$val){
                    $item = $val["entity"];
                    if($item != null && $item->getPrice() > 0 && $item->getType() != 'modifier'){
                     
                        $res .= '<li>
                                    <span class="product-info" data-product-parent-name="'.$item->getParentGroup()->getName().'" data-product-parent-id="'.$item->getParentGroup()->getId().'" data-product-name="'.$item->getName().'" data-product-href="'.$item->getHref().'"  data-product-id="'.$item->getId().'" data-product-price="'.$item->getPrice().'"></span>
                                    <a data-max-basket-count="'.$item->getMaxBasketCount().'" data-is-discount="'.$item->getIsPriceDiscount().'" data-group-id="'.$item->getParentGroup()->getId().'" data-group-name="'.$item->getparentGroup()->getName().'" data-product-id="'.$item->getId().'" class="search-item" href="'.$item->getHref().'">'.$item->getName().'</a>
                                    <button class="buy">
                                        <span>'.$item->getPrice().'</span>
                                        <i class="fa fa-rub"></i>
                                    </button>
                                </li>';
                    
                    }
                }
            } else {
                $res = "<li>По вашему запросу ничего не найдено!</li>";
            }
            if($res == ''){$res = "<li>По вашему запросу ничего не найдено!</li>";}
            return new Response(json_encode($res), 200, array(
                'Content-Type' => 'application/json'
            ));
        }    
    }
    
    protected function newUser($em){
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
          
        return $user;   //  exit;
    }
    
    public function setNewBasketAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $str_basket = $request->request->get('basket'); 
            $uId = $request->request->get('user'); 
            $securityContext = $this->container->get('security.authorization_checker'); 
            $auth = $user = null;
            
            if($securityContext->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')){
                if($uId == 'false' && $str_basket != 'false'){
                    $user = $this->newUser($em); 
                    $auth = $user->getId();                 
                } else {
                     $user = $this->getUser();
                }
               
            } else {
                $user = $this->getUser();    
            }
                   
            if($user){ 
                $userBasket = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));   
                
                foreach($userBasket as $uB){
                    $curBasket[$uB->getProduct()->getId()][$uB->getBaseId()] = $uB;  
                }    
                      
                if($str_basket != "false"){
                    $list = explode("-",substr($str_basket, 0, -1));
                    foreach($list as $val){    
                        $key = explode("x",$val);
                        $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$key[0],$user);
                        $bItem = $em->getRepository('MainCatalogBundle:BasketItem')->findOneBy(array('product'=>$product,'user'=>$user,'status'=>0,'baseId'=>$key[2]));    
                        if($bItem){
                            $bItem->setCount($key[1]);
                            unset($curBasket[ $key[0] ][ $key[2] ]); 
                        } else {
                            $bItem = new BasketItem();
                            if($key[4] > 0){
                                $prod = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$key[4],$user);
                                $pItem = $em->getRepository('MainCatalogBundle:BasketItem')->findOneBy(array('product'=>$prod,'user'=>$user,'status'=>0,'baseId'=>$key[3]));    
                                $bItem->setParent($pItem->getId());
                            }
                            $bItem->setProduct($product);
                            $bItem->setCount($key[1]);
                            $bItem->setBaseId($key[2]);
                            $bItem->setUser($user);
                        }
                        $em->persist($bItem);
                        $em->flush();                    
                    }
                } 
                if(isset($curBasket)){
                    foreach($curBasket as $cB){
                        foreach($cB as $ccB){
                            $ccB->setStatus(-1);
                            $em->persist($ccB);
                            $em->flush();                        
                        }
                    }
                }
            }
            $w = ($user != null && $user->getPhone() == "" && $str_basket != "false")?1:0;
            $gift = $this->getGift($em,$user);
            $res = array('w'=>$w,'gift'=>$gift,'auth'=>$auth);
            
            return new Response(json_encode($res), 200, array(
                'Content-Type' => 'application/json'
            ));            
        }    
    }
    
    public function setBasketAction(Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $str_basket = $request->request->get('basket');
            $user = $this->getUser(); 
            $inf = array();
            $userBasket = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));
                
            if($str_basket != "false"){
                $list = explode("-",substr($str_basket, 0, -1));
               
                foreach($userBasket as $uB){
                    $curBasket[$uB->getId()] = $uB;    
                }
                
                foreach($list as $val){    
                    $key = explode("x",$val);
                    //$curBasket[$key[3]][$key[1]] = $key[2];
                  
                    if($key[3] != 0){   
                        $bItem = $em->getRepository('MainCatalogBundle:BasketItem')->find($key[3]); 
                        $bItem->setCount($key[2]);
                        $em->persist($bItem);
                        $em->flush(); 
                        unset($curBasket[$key[3]]);                              
                    } else {                
                        $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$key[0],$user);
                        if($product){
                            $item = new BasketItem();
                            
                            if($product->getType() == 'modifier'){
                                  $parent = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array('modifier'=>$product));
                                  $list = $em->getRepository('MainCatalogBundle:BasketItem')
                                        ->createQueryBuilder('b')
                                        ->select('MAX(b.id)')
                                        ->where('b.product = :prod')
                                        ->andWhere('b.status = :status')
                                        ->setParameter('prod', $parent->getModProduct())  
                                        ->setParameter('status', 0)  
                                        ->getQuery()->getSingleResult(); 

                                   $item->setParent($list[1]); 
                            }
                            
                            $item->setIsDiscount($key[1]);  
                            if($key[1] > 0){
                                $item->setMaxCount($product->getMaxBasketCount());
                                $trueDisc = $product->getTrueDiscount();
                                if($trueDisc){
                                    foreach($trueDisc as $disc){
                                        $item->addDiscount($disc);
                                    }
                                }
                            }
                            $item->setProduct($product);
                            $item->setCount($key[2]);
                            $item->setUser($user);
                            
                            $em->persist($item);
                            $em->flush();   
                         
                            $inf = array('id'=>$product->getId()*$key[4],'bid'=>$item->getId(),'isDis'=>($key[1] ==0)?1:0,'parent'=>$item->getParent()); 
                        }
                    }
                }
                if(isset($curBasket)){
                    foreach($curBasket as $cB){
                        $cB->setStatus(-1);
                        $em->persist($cB);
                        $em->flush();                   
                    }
                }

             /*   foreach($userBasket as $key=>$val){
                    $p = $val->getProduct()->getId(); 
                    $g = $val->getIsDiscount(); 
                    $curId =  $rndArr[$p]*$p;
                    if(isset($curBasket[$curId][$g]) && ($val->getMaxCount() == 0 || $curBasket[$curId][$g] <= $val->getMaxCount())){
                        if($val->getCount() != $curBasket[$p][$g]){
                            $val->setCount($curBasket[$curId][$g]);
                            $em->persist($val);
                            $em->flush(); 
                        }  
                        unset($curBasket[$p][$g]);  
                    }else if (!isset($curBasket[$p][$g])){
                        $val->setStatus(-1);
                        $em->persist($val);
                        $em->flush();    
                    }    
                }*/
                
               /* foreach($curBasket as $key=>$val){
                    $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$key,$user);
                    
                    if($product){
                        foreach($val as $k=>$v){
                            $item = new BasketItem();
                            if($product->getType() == 'modifier'){
                            
                                  $parent = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array('modifier'=>$product));
                                  $list = $em->getRepository('MainCatalogBundle:BasketItem')
                                        ->createQueryBuilder('b')
                                        ->select('MAX(b.id)')
                                        ->where('b.product = :prod')
                                        ->andWhere('b.status = :status')
                                        ->setParameter('prod', $parent->getModProduct())  
                                        ->setParameter('status', 0)  
                                        ->getQuery()->getSingleResult(); 

                                   $item->setParent($list[1]); 
                            }                                  
                            
                            $item->setIsDiscount($k);  
                            if($k > 0){
                                $item->setMaxCount($product->getMaxBasketCount());
                                $trueDisc = $product->getTrueDiscount();
                                if($trueDisc){
                                    foreach($trueDisc as $disc){
                                        $item->addDiscount($disc);
                                    }
                                }
                            }
                            $item->setProduct($product);
                            $item->setCount($v);
                            $item->setUser($user);
                            
                            $em->persist($item);
                            $em->flush();    
                        }
                    }       
                }
            } else {
                $userBasket = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0)); 
                
                foreach($userBasket as $key=>$val){
                    $val->setStatus(-1);
                    $em->persist($val);
                    $em->flush();                
                }  
            }   */

            $w = ($user->getPhone() == "" && $str_basket != "false")?1:0;
            
            $gift = $this->getGift($em,$user);
            
            return new Response(json_encode(array('w'=>$w,'gift'=>$gift['txt'],'inf'=>$inf)), 200, array(
                'Content-Type' => 'application/json'
            ));            
        }else{

            if(isset($userBasket)){
                    foreach($userBasket as $cB){
                        $cB->setStatus(-1);
                        $em->persist($cB);
                        $em->flush();                   
                    }
                }
            return new Response(json_encode(array()), 200, array(
                'Content-Type' => 'application/json'
            ));            
        }
      }   
    }
    
    private function getGift($em,$user){
        $userBasket = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array('user'=>$user,'status'=>0));
        $basketSum = 0;
        $res['txt'] = "";
        foreach($userBasket as $ub){
            $product = $em->getRepository('MainCatalogBundle:Product')->getProduct($em,$ub->getProduct()->getId(),$user);
            $basketSum += $product->getPrice()*$ub->getCount(); 
        }
        
        $gifts = $em->getRepository('MainCatalogBundle:UserGift')->findAll();
        /*                                ->createQueryBuilder('g')
                                        ->select('g')
                                        ->where('g.price <= :price')
                                        ->setParameter('price', $basketSum)
                                        ->getQuery()
                                        ->getResult();    */
                                        
        foreach($gifts as $gf){
            $info = ($gf->getPrice() <= $basketSum)?$gf->getBodyFinal():$gf->getBody();
            $info = str_replace("%sum%",$gf->getPrice()-$basketSum,$info);
            $per = 100 - ($gf->getPrice()-$basketSum)*100/$gf->getPrice();
            $res['txt'] .= "<tr class='cart-info'><td>".$info."</td></tr><tr class='progress-bar'>
                <td colspan='5'>
                    <div>
                        <span style='width:".$per."%'>nhh</span>
                    </div>
                </td></tr>";  
              
             if($gf->getPrice() <= $basketSum){
                $res['list'][] = $gf->getProduct();    
             }   
        }      
                                  
      /*  $res = "<tr>
                    <td class='quantity'>2</td>
                    <td class='gift' colspan='4'>в подарок</td>
                 </tr>".$res;                     */
       
        return $res;
    }
    
    public function indexAction(ProductGroup $group, $count, $catch, Request $request){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $repo = $this->getDoctrine()
                         ->getManager()
                         ->getRepository('MainCatalogBundle:Product');
                         
            $qb = $repo->createQueryBuilder('p');
            $qb->select('COUNT(p)');
            $qb->where('p.parentGroup = :id');
            $qb->andWhere('p.isIncludedInMenu = 1');
            $qb->setParameter('id', $group);   
            $count = ($count == -1)?null:$count;
            $url = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($group->getId());
            
            $_count = $qb->getQuery()->getSingleScalarResult();
            $user = $this->getUser();
            //echo $count;
            $product = $em->getRepository('MainCatalogBundle:Product')->getProductList($em, $group, $count,'ASC',$user);
            //echo count($product);
            /*
            $data = $repo->createQueryBuilder('p')
                            ->select('p')
                            ->where('p.parentGroup = :id')
                            ->orderBy('p.order_by', 'ASC')
                            ->setParameter('id', $group);
            if($count > 0){
                $data->setMaxResults($count);
            }                
                            
            $product = $data->getQuery()->getResult();*/
            
            $result = "";
            $result_group = "";
            $engine = $this->container->get('templating');
           
            $rec_list = $em->getRepository('MainCatalogBundle:Product')->getRecommendedItem($em,$user,null,$group);
            
             //$this->getGroupRecList($group,$em);
            // echo count($rec_list);exit;
            $stop = 0;
            foreach($product as $key=>$val){
                if($stop == $count && $count!=null){ break; }
                
                $result .=  $engine->render('MainCatalogBundle:Product:product.html.twig',array(
                                "product" => $val,
                                "url"     => $url,
                                "rec"     => $rec_list,
                                "mod"     => $this->getModList($val,$em)
                ));  
                $stop++;  
            }
            $group_list = $em->getRepository('MainCatalogBundle:ProductGroup')->findBy(array('parentGroup'=>$group,'isIncludedInMenu'=>true));
            foreach($group_list as $key=>$val){
                $img = $val->getImages();
                $img_url = (count($img) > 0)?$img[0]->getSrc():'/img/product/no-photo.png';
                $result_group .=  $engine->render('MainCatalogBundle:Product:group.html.twig',array(
                                "group" => $val,
                                "img" =>$img_url,
                                "alias" => $group->getAlias()."/".$val->getAlias(),
                ));    
            }
            
            $info = (date("H") >= 23 || date("H") <= 11)?"Мы принимаем заказы на доставку каждый день с 11 до 23 часов.":"";
            $response = json_encode(array("group"=>$result_group,"product"=>$result,"count"=>$_count - $count,"info"=>$info));
        } else {
            $response = json_encode(array("errorCode"=>403,"message"=>"Нет доступа!"));
        }
        
        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }
    
    function getModList(Product $product,$em){
        
        $mod = $product->getModifiers();
        
        $res = array();
        foreach($mod as $m){
            
            $prod = $m->getModifier();
            $res[$m->getRequared()][] = array(
                                            'id'=>$m->getModifier()->getId(),
                                            'name'=>$prod->getName(),
                                            'price'=>$prod->getPrice(),
                                            'min'=>$m->getMinAmount(),
                                            'max'=>$m->getMaxAmount(),
                                           
                                            );    
        }
           
        
        return $res;
    }
    
    function getGroupRecList(ProductGroup $group,$em){
        
        $res = array();
        $recommendedGroup = $group->getMeRecommended();
        if(count($recommendedGroup) > 0){
           /* $earlierBasketItem = $em->getRepository('MainCatalogBundle:BasketItem')
                                    ->createQueryBuilder('b')
                                    ->select('b')
                                    ->where('b.user = :user')
                                    ->andWhere('b.status = -1')
                                    ->andWhere('b.status = 1')
                                    ->setParameter('user', $user)
                                    ->getQuery()
                                    ->getResult();  */
                                              
            foreach($recommendedGroup as $key=>$val){
              //  if($key == $id[1]){ break; }
                $url[$val->getId()] = $em->getRepository('MainCatalogBundle:ProductGroup')->getUrl($val->getId());
                $product[] = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array('parentGroup'=>$val),array('order_by'=>'ASC'),1);
                
                
            }
            $res = array('url'=>$url,'prod'=>$product);
        } else {
            $res = array('url'=>array(),'prod'=>array());
        }
        //var_dump($res);
        return $res;
    }
}
