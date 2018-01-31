<?php

namespace Main\PageBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{

    use ContainerAwareTrait;
    
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menuItems = $this->container->get('menu')->getMainMenu();
        $menu = $factory->createItem('root');

        $this->setCurrentItem($menu);

        $menu->setChildrenAttribute('class', 'nav');
        $menu->setExtra('currentElement', 'active');

        foreach($menuItems as $item) {
            $menu->addChild($item->getTitle(), array('uri' => $item->getRoute()));
        }

        return $menu;
    }

    protected function setCurrentItem(ItemInterface $menu)
    {
        $menu->setCurrentUri($this->container->get('request')->getPathInfo());
    }
}