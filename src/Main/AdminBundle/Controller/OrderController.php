<?php

namespace Main\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Main\CatalogBundle\Entity\Order;
use Main\NuberBundle\Entity\User;

class OrderController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('MainCatalogBundle:Order')->findBy(array(), array('id' => 'DESC'));

        return $this->render('MainAdminBundle:Order:index.html.twig', array(
            'orders' => $orders,

        ));
    }
    
    public function getAddressAction(Order $order){
        return new Response(json_encode(), 200, array(
            'Content-Type' => 'application/json'
        ));        
    }
    
    public function getItemListAction(Order $order, User $user, Request $request){
        
        $items = $order->getItem();
        $res = '';
        foreach($items as $it){
            $mod = '';
            if($it->getProduct()->getType() != 'modifier'){
                foreach($items as $i){
                    if($i->getParent() == $it->getId()){
                        $mod .= '<tr class="item-list-mod"><td>'.$i->getProduct()->getName().'</td><td>'.$i->getCount().'</td><td>'.$i->getProduct()->getPrice().'</td></tr>';
                    }
                }
                $res .= "<tr>
                            <td>".$it->getProduct()->getName()."</td>
                            <td>".$it->getCount()."</td>
                            <td>".$it->getProduct()->getPrice()."</td>
                        </tr>".$mod;
                        
            }
        }
        
        $u = array(
          'name'=>$user->getName(),
          'address'=>'ул. '.$user->getStreet().', д.'.$user->getHouse().', корп.'.$user->getBuild().', кв.'.$user->getApartment(),
          'phone'=>$user->getPhone(),
          'createdAt'=>($user->getCreatedAt() != null)?$user->getCreatedAt()->format('Y-m-d H:i'):"-",
          'lastVisit'=>($user->getLastLogin() != null)?$user->getLastLogin()->format('Y-m-d H:i'):"-"
        );
        
        $a = array(
                  'city'=>'Махачкала',
                  'street'=>$order->getUser()->getStreet(),
                  'house' =>$order->getUser()->getHouse(), 
                  'build' =>$order->getUser()->getBuild(), 
                  'apartment' =>$order->getUser()->getApartment(),
                  'comment'=>$order->getComment()
        );

        return new Response(json_encode(array("list"=>$res,"u"=>$u,"a"=>$a)), 200, array(
            'Content-Type' => 'application/json'
        ));        
    }
    
	public function handPushAction(Order $order){
		
		$this->um =  $this->container->get('update_manager');
		$this->um->push($order);
		echo 'ok';exit;
		
	}
    
    public function changeStatusAction(Order $order,Request $request){
        $em = $this->getDoctrine()->getManager();
        $id = $request->request->get('id');
        $order->setStatus($id);
        $em->persist($order);
        $em->flush();
        
        return new Response(json_encode($id), 200, array(
            'Content-Type' => 'application/json'
        ));
    }
    
    
    public function getUserInfoAction(User $user){

        return new Response(json_encode(), 200, array(
            'Content-Type' => 'application/json'
        ));        
    }
}