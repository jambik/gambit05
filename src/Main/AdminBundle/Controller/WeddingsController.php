<?php

namespace Main\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Main\PageBundle\Entity\Page;
use Main\PageBundle\Entity\PageProperty;

class WeddingsController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();    
        $list = $em->getRepository('MainPageBundle:Page')->findBy(array('parentPage' => 6, 'isPublished'=>true));
        

        return $this->render('MainAdminBundle:Weddings:index.html.twig',array(
            "pages" => $list
        ));
    }
    
    protected function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
           foreach($objs as $obj) {
             is_dir($obj) ? removeDirectory($obj) : unlink($obj);
           }
        }
        rmdir($dir);
    }    
    
    public function setImgAction(Request $request){
       
        move_uploaded_file($_FILES['file']['tmp_name'], "/var/www/open/web/img/page/tmp/" . $_FILES['file']['name']);

        return new Response($_FILES['file']['name'], 200, array(
            'Content-Type' => 'application/json'
        ));            
    }
    
    public function setMImgAction(Request $request,Page $page){
        
        $em = $this->getDoctrine()->getManager();
        if(!is_dir("/var/www/open/web/img/page/".$page->getId())){
            mkdir("/var/www/open/web/img/page/".$page->getId());    
        }
        move_uploaded_file($_FILES['file']['tmp_name'], "/var/www/open/web/img/page/".$page->getId()."/" . $_FILES['file']['name']);
        
        $page->setPreviewPic("/img/page/".$page->getId()."/" . $_FILES['file']['name']);
        
        $em->persist($page);
        $em->flush();
        
        return new Response(json_encode(array($_FILES['file']['name'])), 200, array(
            'Content-Type' => 'application/json'
        ));            
    }
    
    public function createCheckAction(){
        $em = $this->getDoctrine()->getManager();
        $sWeddDate = \DateTime::createFromFormat("j.n.Y H:i:s", date('j.n.Y')." 00:00:00");
        $events = $em->getRepository('MainPageBundle:Page')
                         ->createQueryBuilder('p')
                         ->where('p.parentPage = 6')
                         ->leftJoin('p.property', 'prop')
                         ->andWhere('prop.name = :WDate')
                         ->andWhere('prop.dateValue > :d')
                         ->setParameter('WDate', 'WeddDate')
                         ->setParameter('d', $sWeddDate)
                         ->getQuery()
                         ->getResult();
        $dis = $res = array();
        $limi = "";
        if($events){
            
            foreach($events as $ev){
                $props = $ev->getProperty(); 
                foreach($props as $prop){
                    if($prop->getName() == "WeddDate"){
                        $d = $prop->getDateValue()->format("d.m.Y"); 
                    }
                    if($prop->getName() == "place"){
                        $p = $prop->getValue(); 
                    }
                }
                $res[$d] = (isset($res[$d]))?$res[$d] + ($p + 1):($p + 1);    
            }
            
            foreach($res as $k=>$v){
                if($v == 3){
                    $dis[] = $k;
                }else{
                    $limi[$k] = $v;
                } 
            }
        }       
                                
        return new Response(json_encode(array('dis'=>$dis,'one'=>$limi)), 200, array(
            'Content-Type' => 'application/json'
        ));
    }
    
    public function deleteAction(Page $page){
        $em = $this->getDoctrine()->getManager();
        $this->removeDirectory("/var/www/open/web/img/page/".$page->getId());
        
        $props = $page->getProperty();
        
        foreach($props as $prop){
            $page->removeProperty($prop);
            $em->persist($page);
            $em->flush();
            
            $em->remove($prop);
            $em->flush();    
        }
        
        $em->remove($page);
        $em->flush();      
        
           
        return new Response(json_encode(0), 200, array(
            'Content-Type' => 'application/json'
        )); 
    }
    
    protected function setProper($name,$value,$Dvalue = null){
        $em = $this->getDoctrine()->getManager();
        
        $p = new PageProperty();
        $p->setName($name);
        $p->setType(1);
        $p->setIsActive(true);
        $p->setIsParentCopy(true);
        if($Dvalue != null){
            $WeddDate = \DateTime::createFromFormat("j.n.Y H:i", $Dvalue);
            $p->setDateValue($WeddDate);    
        }else{
            $p->setValue($value); 
        }
        $em->persist($p);
        $em->flush(); 
        
        return $p;   
    }
    
    public function translit($str){
        $alphavit = array(
            /*--*/
            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
            "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l", "м"=>"m",
            "н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t",
            "у"=>"y","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch", "ш"=>"sh","щ"=>"sh",
            "ы"=>"i","э"=>"e","ю"=>"u","я"=>"ya",
            /*--*/
            " "=>"_","*"=>"_","/"=>"_","|"=>"_", "!"=>"_", "@"=>"_", "#"=>"_",
            "$"=>"_", "%"=>"_", "&"=>"and", "?"=>"_", "№"=>"_",'"'=>"",
            /*--*/
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E", "Ё"=>"Yo",
            "Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"I","К"=>"K", "Л"=>"L","М"=>"M",
            "Н"=>"N","О"=>"O","П"=>"P", "Р"=>"R","С"=>"S","Т"=>"T","У"=>"Y",
            "Ф"=>"F", "Х"=>"H","Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh",
            "Ы"=>"I","Э"=>"E","Ю"=>"U","Я"=>"Ya",
            "ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>""
        );
        return strtolower(strtr($str, $alphavit));
    }  
    
    public function createAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        
        $wD = $request->request->get('d');
        $family = $request->request->get('f');
        $mN  = $request->request->get('mN');
        $wN = $request->request->get('wN');
        $wP = $request->request->get('wP');
        $mP = $request->request->get('mP');
        $a =  $request->request->get('a') ;
        $ac = ($request->request->get('ac') == 'true')?true:false;
        $place = ($request->request->get('p') == "option2")?1:0;
        
        $alias =($a != "")?$a:$this->translit($family);
        
        $page = new Page();
        
        $parent = $em->getRepository('MainPageBundle:Page')->find(6);
        $page->setParentPage($parent);
        $page->setPageType(2);
        $page->setisPublished($ac);
        $page->setAlias($alias);
        $page->setTemplate('wedding');
        $page->setNuberId(111);

        $page->addProperty($this->setProper("mName",$mN));
        $page->addProperty($this->setProper("wName",$wN));
        $page->addProperty($this->setProper("family",$family));
        $page->addProperty($this->setProper("place",$place));
        $page->addProperty($this->setProper("WeddDate",null,$wD));

        $em->persist($page);
        $em->flush();

        if(!is_dir("/var/www/open/web/img/page/".$page->getId())){
            mkdir("/var/www/open/web/img/page/".$page->getId());    
        } 
        if($mP != ""){
            copy('/var/www/open/web/img/page/tmp/'.$mP,'/var/www/open/web/img/page/'.$page->getId().'/'.$mP);      
            $page->setPreviewPic('/img/page/'.$page->getId().'/'.$mP); 
        }
        if($wP != ""){
            copy('/var/www/open/web/img/page/tmp/'.$wP,'/var/www/open/web/img/page/'.$page->getId().'/'.$wP);       
            $page->setImg('/img/page/'.$page->getId().'/'.$wP);
        }
        $em->persist($page);
        $em->flush();        
        
        return new Response(json_encode($page->getid()), 200, array(
            'Content-Type' => 'application/json'
        )); 
    }
 
    public function getWeddingListAction(Request $request){
        $em = $this->getDoctrine()->getManager();  
        $filter = json_decode($request->request->get('data'));
        
        $repository = $this->getDoctrine()
                ->getRepository('MainPageBundle:Page');
                
        $query = $repository->createQueryBuilder('p')
                ->where('p.parentPage = 6')
                ->andWhere('p.alias != :alias')->setParameter('alias', 'new');
                
        if($filter->active > 0){
            $query->andWhere('p.isPublished = :is')->setParameter('is', $filter->active);
        }
        if($filter->pavilion != 0 || $filter->main != 0){
            $f = ($filter->pavilion == 1)?0:1;
            $query->leftJoin('p.property', 'prop')
                   ->andWhere('prop.name = :place')
                   ->andWhere('prop.value = :place_value')
                   ->setParameter('place', 'place')       
                   ->setParameter('place_value', $f); 
        }
        
        if($filter->family != ""){
            $query->leftJoin('p.property', 'prop2') 
                  ->andWhere('prop2.name = :family')
                  ->andWhere('prop2.value = :family_value')
                  ->setParameter('family', 'family')       
                  ->setParameter('family_value', $filter->family);   
        }

        if($filter->mName != ""){
            $query->leftJoin('p.property', 'prop3') 
                  ->andWhere('prop3.name = :mName')
                  ->andWhere('prop3.value = :mName_value')
                  ->setParameter('mName', 'mName')       
                  ->setParameter('mName_value', $filter->mName);   
        }        
        
        if($filter->gName != ""){
            $query->leftJoin('p.property', 'prop4') 
                  ->andWhere('prop4.name = :wName')
                  ->andWhere('prop4.value = :wName_value')
                  ->setParameter('wName', 'wName')       
                  ->setParameter('wName_value', $filter->gName);   
        }     
        
        if($filter->day != ""){
            $query->leftJoin('p.property', 'prop5') 
                ->andWhere('prop5.name = :dat')
                ->andWhere("DATE_FORMAT(prop5.dateValue, '%Y-%m-%d') = :date_value")
                ->setParameter('dat', 'WeddDate')       
                ->setParameter('date_value', $filter->year."-".$filter->month."-".$filter->day);    
        }else{
            if($filter->month != 0){
                $query->leftJoin('p.property', 'prop5') 
                    ->andWhere('prop5.name = :dat')
                    ->andWhere("DATE_FORMAT(prop5.dateValue, '%Y-%m') = :date_value")
                    ->setParameter('dat', 'WeddDate')       
                    ->setParameter('date_value', $filter->year."-".$filter->month);                 
            }else{
                $query->leftJoin('p.property', 'prop5') 
                    ->andWhere('prop5.name = :dat')
                    ->andWhere("DATE_FORMAT(prop5.dateValue, '%Y') = :date_value")
                    ->setParameter('dat', 'WeddDate')       
                    ->setParameter('date_value', $filter->year);  
            }
        }
        
       
           
                
        $qResult = $query->getQuery()->getResult();
        
        foreach($qResult as $item){
            $props = $item->getProperty();  
            foreach($props as $prop){
                if($prop->getName() == "family"){
                    $family = $prop->getValue();    
                }
                if($prop->getName() == "mName"){
                    $mName = $prop->getValue();    
                }
                if($prop->getName() == "wName"){
                    $wName = $prop->getValue();    
                }
                if($prop->getName() == "place"){
                    $place = $prop->getValue();    
                } 
                if($prop->getName() == "WeddDate"){
                    $WeddDate = date_format($prop->getDateValue(),"d.m.Y H:m");   
                }                                                                 
            }
            
            $result[] = array(
                            'id'=>$item->getId(),
                            'status'=>($item->getIsPublished())?"Опубликована":"Не опубликована",
                            'family'=>$family,
                            'mName'=>$mName,
                            'wName'=>$wName,
                            'place'=>($place == 1)?"Шатер":"Главный зал",
                            'WeddDate'=>$WeddDate
                        );            
        }
        
        
        return new Response(json_encode($result), 200, array(
            'Content-Type' => 'application/json'
        ));          
    }
    
    public function setWImgAction(Request $request,Page $page){
        
        $em = $this->getDoctrine()->getManager();
        if(!is_dir("/var/www/open/web/img/page/".$page->getId())){
            mkdir("/var/www/open/web/img/page/".$page->getId());    
        }
        move_uploaded_file($_FILES['file']['tmp_name'], "/var/www/open/web/img/page/".$page->getId()."/" . $_FILES['file']['name']);
        
        $page->setImg("/img/page/".$page->getId()."/" . $_FILES['file']['name']);
        
        $em->persist($page);
        $em->flush();
        
        return new Response(json_encode(array($_FILES['file']['name'])), 200, array(
            'Content-Type' => 'application/json'
        ));            
    }   
    
    public function editWeddingDataAction(Request $request,Page $page){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager(); 
            
            $family = $request->request->get('family');
            $mN  = $request->request->get('mN');
            $wN = $request->request->get('wN');
            $wD = $request->request->get('wD');
            $ac = $request->request->get('ac');
            $place = $request->request->get('place');
            
            $ac = ($ac == "true")?1:0;
            $page->setIsPublished($ac);
            $em->persist($page);
            $em->flush();
            
            $props = $page->getProperty(); 
            foreach($props as $prop){
                if($prop->getName() == "family"){
                    $prop->setValue($family);    
                }
                if($prop->getName() == "mName"){
                   $prop->setValue($mN);    
                }
                if($prop->getName() == "wName"){
                    $prop->setValue($wN);    
                }
                if($prop->getName() == "WeddDate"){
                    $WeddDate = \DateTime::createFromFormat("j.n.Y H:i:s", $wD." 12:00:00");
                    $prop->setDateValue($WeddDate);
                }
                
                if($prop->getName() == "place"){
                    $prop->setValue($place);    
                }
                                
                $em->persist($prop);
                $em->flush();
 
                                                                                 
            }

            return new Response(json_encode(array()), 200, array(
                'Content-Type' => 'application/json'
            ));  
        }   
    }
    
    
    public function getWeddingDataAction(Request $request,Page $page){
        if($request->isXmlHttpRequest()){
            $em = $this->getDoctrine()->getManager();
            $family = $mName = $wName = $WeddDate= $place = array();
            $props = $page->getProperty();  
            foreach($props as $prop){
                if($prop->getName() == "family"){
                    $family = $prop->getValue();    
                }
                if($prop->getName() == "mName"){
                    $mName = $prop->getValue();    
                }
                if($prop->getName() == "wName"){
                    $wName = $prop->getValue();    
                }
                if($prop->getName() == "place"){
                    $place = $prop->getValue();    
                } 
                if($prop->getName() == "WeddDate"){
                    $WeddDate = date_format($prop->getDateValue(),"d.m.Y");   
                }                                                                 
            }
        
            $response = json_encode(array(  "family"=>$family,
                                            "mName"=>$mName,
                                            "mPhoto"=>$page->getPreviewPic(),
                                            "wPhoto"=>$page->getImg(),
                                            "wName"=>$wName,
                                            "place"=>$place,
                                            "WeddDate"=>$WeddDate,
                                            "active"=>$page->getIsPublished()));       
            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));  
        }    
    }
}
