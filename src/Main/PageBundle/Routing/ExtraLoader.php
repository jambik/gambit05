<?php

namespace Main\PageBundle\Routing;

use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ExtraLoader implements LoaderInterface
{
    private $loaded = false;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add this loader twice');
        }

        $routes = new RouteCollection();
      
        $pattern = '/';
        $defaults = array(
            '_controller' => 'MainPageBundle:Page:getPage',
        );

        $route = new Route($pattern, $defaults);
        $routes->add('extraRoute', $route);
       
      //  $route4  = new Route('/catalog/{group}/{group1}', array('_controller' => 'MainPageBundle:Page:getCatalog')); 
        
        $route = new Route('/{first}/{alias}', array('_controller' => 'MainPageBundle:Page:getPage'));
        $route2 = new Route('/{alias}', array('_controller' => 'MainPageBundle:Page:getPage'));
        $route3 = new Route('/{first}/{second}/{alias}', array('_controller' => 'MainPageBundle:Page:getPage'));
        $route4 = new Route('/{first}/{second}/{third}/{alias}', array('_controller' => 'MainPageBundle:Page:getPage'));
        
       
      //  $routes->add('route_catalog', $route4);
        $routes->add('route_page1', $route2);
        $routes->add('route_page2', $route3);
        $routes->add('route_page3', $route4);
        $routes->add('route_page', $route);
        
        return $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'extra' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
