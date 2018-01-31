<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_discount_type")
 * @ORM\HasLifecycleCallbacks()                                                       
 */
 
class DiscountType {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id ;
    
            /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductDiscount", mappedBy="DiscountType")
     */
    protected $discount;
    
        
    /** @ORM\Column(type="integer", nullable=true) */
    protected $nuber_id;
    
    /** @ORM\Column(type="string") */ 
    protected $title;
    
    public function __construct()
    {
        $this->discount = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getDiscount()
    {
        return $this->discount;
    }    
    
    public function getId()
    {
        return $this->id;
    }

    public function setNuberId($id)
    {
        $this->nuber_id = $id;

        return $this;
    }

    public function getNuberId()
    {
        return $this->nuber_id;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }
}