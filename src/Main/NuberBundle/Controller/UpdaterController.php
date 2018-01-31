<?php

namespace Main\NuberBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Main\NuberBundle\Entity\Updater;
use Main\PageBundle\Entity\Page;
use Main\NuberBundle\Form\UpdaterType;

class UpdaterController extends Controller
{
    protected $um;
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $updaters = $em->getRepository('MainNuberBundle:Updater')->findAll();

        return $this->render('updater/index.html.twig', array(
            'updaters' => $updaters,
        ));
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
    
    public function sendNuberRequet($id){
        $em = $this->getDoctrine()->getManager();
    } 
    
    public function newAction(Request $request)
    {
        $key = json_decode($request->getContent());
        $this->um =  $this->container->get('update_manager');
        
        $answer = $this->um->setNuberUpdater($key);
        
        return new Response(json_encode(array("errorCode"=>0,"message"=>$answer)), 200, array(
            'Content-Type' => 'application/json'
        ));    
    }
    
    public function answerAction(Request $request){
        $em = $this->getDoctrine()->getManager();
      
        $key = json_decode($request->getContent());
        $project = $em->getRepository('MainNuberBundle:Project')->find(1);
        
        $de_key = $this->mc_decrypt($key->key,$project->getNuberSecret());
       
        $req = explode("~",$de_key);

        $update = $em->getRepository('MainNuberBundle:Updater')->find($req[0]);
        $req_data = $this->mc_decrypt($req[1],$update->getSecret()) ;

        $acc = explode("~",$req_data);    
        $login = $acc[0];
        $password = $acc[1];
        $user = $em->getRepository('MainNuberBundle:User')->findOneBy(array("username"=>$login));
     
        if($user->getPassword() == $password){
            $order = $product = $base = null;
            
            if($update->getOrder()){
                $order_list = $em->getRepository('MainCatalogBundle:Order')
                                 ->findBy(array("synx"=>NULL));    
                     
                if($order_list){
                    foreach($order_list as $o){
                        $item_list = $em->getRepository('MainCatalogBundle:BasketItem')->findBy(array("order"=>$o,"status"=>1));
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
                        $order[] =  array(
                                        "comment"   => $o->getComment(),
                                        "item"      => $item,
                                        "created"   => $o->getCreatedAt(),
                                        "city"      => 'Махачкала',
                                        'id'        => $o->getId(),
                                        "name"      => ($o->getUser()->getName() == null)?'Не указан':$o->getUser()->getName(),
                                        "phone"     => $o->getUser()->getPhone(),  
                                        "street"    => $o->getUser()->getStreet(),  
                                        "house"     => $o->getUser()->getHouse(),  
                                        "build"     => $o->getUser()->getBuild(), 
                                        "user_mail" => ($o->getUser()->getRegDate() != null)?$o->getUser()->getEmail():'не указан',  
                                        "apartment" => $o->getUser()->getApartment(),
                                        "isReg"     => ($o->getUser()->getIsReg())?1:0,
                                        "regDate"   => ($o->getUser()->getRegDate() != null)?$o->getUser()->getRegDate()->format('j.n.Y H:i'):null,
                                        "isPickUp"  => ($o->getIsPickUp())?1:0,  
                                       // "isCash"  => ($o->getIsCash())?1:0
                                    );
                           
                        $o->setSynx(new \DateTime());
                        $em->persist($o);
                        $em->flush();                
                    }
                }
            }
            $response = json_encode(array("errorCode"=>0,"message"=>array("order"=>$order)));            
        }else{
            $response = json_encode(array("errorCode"=>401,"message"=>"У вас нет прав доступа к данной информации!"));     
        }
         
        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
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
   
    public function reqAction(){
        $em = $this->getDoctrine()->getManager();

        $updaters = $em->getRepository('MainNuberBundle:Updater')->findOneBy(array("status"=>false,"type"=>false));
        $project = $em->getRepository('MainNuberBundle:Project')->find(1);
        
        if($updaters){
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
                ->select('u')
                ->from('MainNuberBundle:User', 'u')
                ->where('u.project = :id');

            $user = $qb->getQuery()
                ->setParameter('id', 1)
                ->getOneOrNullResult(); 
                
            $key_ = $this->mc_encrypt($user."~".$user->getPassword(),$updaters->getSecret());    
          
            $key = $this->mc_encrypt($updaters->getNuberId()."~".$key_,$project->getNuberSecret());
            
            $result = $this->curl_post(
                            "http://v2.nuber.ru/nuber/updater/getdata",
                            json_encode(array("key"=>$key,"id"=>$project->getNuberId())),
                            array(CURLOPT_HTTPHEADER=> array('Content-Type: application/x-www-form-urlencoded; charset=utf-8') )
                        );
                      
            $answer =  json_decode($result);        
            $page = $this->setUpdatePageData($answer->message->page,$em);
            
            if($page){
                $updaters->setStatus(true); 
                $em->persist($updaters);
                $em->flush();                   
            }
        }       
        
        exit;
    }
    
     public function answerOLDERAction(Request $request){
       
        $em = $this->getDoctrine()->getManager();
      
        $key = json_decode($request->getContent());
        
        $project = $em->getRepository('MainNuberBundle:Project')->find(1);
        
        $de_key = $this->mc_decrypt($key->key,$project->getNuberSecret());
        $req = explode("~",$de_key);
        
       
        $update = $em->getRepository('MainNuberBundle:Updater')->find($req[0]);
       
        $req_data = $this->mc_decrypt($req[1],$update->getSecret()) ;
        
        $acc = explode("~",$req_data);
        $login = $acc[0];
        $password = $acc[1];
        
        $user = $em->getRepository('MainNuberBundle:User')->findOneBy(array("username"=>$login));
        if($user->getPassword() == $password){
            foreach ($update->getPage() as $numObject => $obj){                                                                
                $pages[$obj->getId()] = array(
                                "alias"=>$obj->getAlias(),
                                "parent_page"=>$obj->getParentPage(),
                                "parent_page_id"=>($obj->getParentPage() == null)?null:$obj->getParentPage()->getId(),
                                "title"=>$obj->getTitle(),
                                "h1"=>$obj->getH1(),
                                "preview_text"=>$obj->getPreviewText(),
                                "preview_pic"=>$obj->getPreviewPic(),
                                "img"=>$obj->getImg(),
                                "content"=>$obj->getContent(),
                                "head"=>$obj->getHead(),
                                "meta_keys"=>$obj->getMetaKeys(),
                                "meta_description"=>$obj->getMetaDescription(),
                            );
            }
           
            $data = array("page"=>$pages);    
            $response = json_encode(array("errorCode"=>0,"message"=>$data));  
            
            $update->setStatus(true); // Забрали данные
            $update->setUploadDate(new \DateTime());
            $em->persist($update);
            $em->flush();            
        } else {
            $response = json_encode(array("errorCode"=>401,"message"=>"У вас нет прав доступа к данной информации!"));   
        }
         
        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
        
     }
    
    protected function setUpdatePageData($pages,$em){
        foreach($pages as $key=>$val){
            $page = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$key));
            
            if(!$page){    
                $page = new Page();
                $page->setMassPageData($val,$key);
                if($val->parent_page_id){
                    $parent = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$val->parent_page_id));
                    $page->setParentPage($parent);
                }
                $em->persist($page);
                $em->flush();                
            } else {
                $page->setMassPageData($val,$key);
                if($val->parent_page_id){
                    $parent = $em->getRepository('MainPageBundle:Page')->findOneBy(array("nuber_id"=>$val->parent_page_id));
                    $page->setParentPage($parent);
                }
                $em->persist($page);
                $em->flush();
            }
        }
        
        return true;
            
    }

    /**
     * Finds and displays a Updater entity.
     *
     */
    public function showAction(Updater $updater)
    {
        $deleteForm = $this->createDeleteForm($updater);

        return $this->render('updater/show.html.twig', array(
            'updater' => $updater,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Updater entity.
     *
     */
    public function editAction(Request $request, Updater $updater)
    {
        $deleteForm = $this->createDeleteForm($updater);
        $editForm = $this->createForm('Main\NuberBundle\Form\UpdaterType', $updater);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($updater);
            $em->flush();

            return $this->redirectToRoute('updater_edit', array('id' => $updater->getId()));
        }

        return $this->render('updater/edit.html.twig', array(
            'updater' => $updater,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Updater entity.
     *
     */
    public function deleteAction(Request $request, Updater $updater)
    {
        $form = $this->createDeleteForm($updater);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($updater);
            $em->flush();
        }

        return $this->redirectToRoute('updater_index');
    }

    /**
     * Creates a form to delete a Updater entity.
     *
     * @param Updater $updater The Updater entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Updater $updater)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('updater_delete', array('id' => $updater->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
