<?php
namespace PageBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_numeric', function ($str) {
                return is_numeric($str);
            })
        );
    }

    public function getName()
    {
        return 'twig_extension';
    }
}