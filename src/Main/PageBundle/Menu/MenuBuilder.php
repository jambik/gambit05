<?php
 
namespace Main\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
class MenuBuilder implements ContainerAwareInterface
{
    
    use ContainerAwareTrait;
    
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('id', 'navmenu');
        
        $menu->addChild('Projects', array('route' => 'updater_new'))
            ->setAttribute('icon', 'icon-list');
            
        $menu->addChild('Employees', array('route' => 'updater_req'))
            ->setAttribute('icon', 'icon-group');
            
        return $menu;
    }
    
    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('id', 'navmenu');

        $em = $this->container->get('doctrine')->getManager();
        $cpage = $em->getRepository('MainPageBundle:Page')->findOneBy(array("page_type"=>2)); 
        $item = $em->getRepository('MainPageBundle:MenuItem')->getMenuData(array("menu"=>1,"parent"=>null),$cpage);
        
        
        foreach($item as $it){
            $menu->addChild('Item'.$it["id"], array('label' => $it["title"],'childrenAttributes' => array('class' => 'dropdown')));
            $child = $em->getRepository('MainPageBundle:MenuItem')->getMenuData(array("menu"=>1,"parent"=>$it["id"]),$cpage);
            
            if($child){
                $menu->getChild('Item'.$it["id"])
                     ->setAttribute('dropdown', false)
                     ->setAttribute('img', '/img/arrow.png');  
                foreach($child as $ch){
                    $menu['Item'.$it["id"]]->addChild($ch["title"], array('route' => 'updater_new'))     
                    ->setAttribute('icon', 'icon-edit')
                    ->setAttribute('dropdown', false)
                    ->setUri($ch["link"]); 
                }       
                     
            } else {
                $menu->getChild('Item'.$it["id"])
                     ->setAttribute('dropdown', false)
                     ->setUri($it["link"])
                     ;    
            }
                            
        } 
       /*
            
        $menu->addChild('User2', array('label' => 'Hi visitor','childrenAttributes' => array('class' => 'dropdown12')))
            ->setAttribute('dropdown', true)  
            ->setAttribute('icon', 'icon-user');
            
        $menu->addChild('User3', array('label' => 'Hi visitor','childrenAttributes' => array('class' => 'dropdown12')))
            ->setAttribute('dropdown', true)  
            ->setAttribute('icon', 'icon-user'); 
                                  
        
                 */
                
        return $menu;
    }
}