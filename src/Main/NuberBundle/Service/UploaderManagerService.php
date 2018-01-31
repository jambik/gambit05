<?php
namespace Main\NuberBundle\Service;

use Doctrine\ORM\EntityManager;
use Main\CatalogBundle\Entity\ProductImage;


class UploaderManagerService{

    private $em;
    
    function __construct(EntityManager $em){
        $this->em = $em;

    }
    
    public function checkImg(){
        $qb = $this->em->getRepository('MainCatalogBundle:ProductImage')
                        ->createQueryBuilder('i');
        $qb->where(
            $qb->expr()->andx(
                $qb->expr()->isNull('i.urlNuberUpload'),
                $qb->expr()->isNotNull('i.urlOriginal')  
            )
        );
        $qb->setMaxResults(20);                      
        $img = $qb->getQuery()->getResult();
        
        
        foreach($img as $i){
            $dir = "/var/www/gambit/web/img/product/".$i->getNuberId();
            if(is_dir($dir)){
                $this->removeDirectory($dir);
            }
            mkdir($dir);
            
            $fPath = explode("/",$i->getUrlOriginal());
            $fName = $fPath[count($fPath) - 1];
            copy($i->getUrlOriginal(),$dir."/".$fName);
            list($w,$h) = getimagesize($dir."/".$fName);
            $this->img_resize($dir."/".$fName, $dir."/".$fName, $w,$h,0xFFFFFF,40);
            $i->setUrlNuberUpload("/img/product/".$i->getNuberId()."/".$fName);
            
            $this->em->persist($i);
            $this->em->flush(); 
        }
        
        return count($img);
    }   
    //UPDATE `client_product_image` SET `url_nuber_upload` = NULL WHERE `client_product_image`.`url_original` <> '' 
    protected function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
           foreach($objs as $obj) {
             is_dir($obj) ? removeDirectory($obj) : unlink($obj);
           }
        }
        rmdir($dir);
    }
    
    protected function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100){
        if (!file_exists($src)) return false;

        $size = getimagesize($src);

        if ($size === false) return false;

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
        $icfunc = "imagecreatefrom" . $format;
        if (!function_exists($icfunc)) return false;

        $x_ratio = $width / $size[0];
        $y_ratio = $height / $size[1];

        $ratio       = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);

        $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
        $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
        $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
        $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

        $isrc = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);

        imagefill($idest, 0, 0, $rgb);
        imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, 
        $new_width, $new_height, $size[0], $size[1]);

        imagejpeg($idest, $dest, $quality);

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;

    }
    
}