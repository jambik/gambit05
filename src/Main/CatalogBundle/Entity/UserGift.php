<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_gift")
 * @ORM\HasLifecycleCallbacks()
 */
 
class UserGift {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;
    
    /**
      * @ORM\Column(type="integer")
    */
    protected $nuber_id;
 
    /**
      * @ORM\Column(type="integer")
    */
    protected $gtype;
    
        /**
      * @ORM\Column(type="string")
    */
    protected $title;
    
    /**
      * @ORM\Column(type="integer")
    */
    protected $quantity;

    
    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product", inversedBy="gift")
    * @ORM\JoinColumn(name="product_id", referencedColumnName="id",nullable=true)
    */
    protected $product;
    
    /**
      * @ORM\Column(type="boolean",nullable=true)
    */     
    protected $byReg;
    
    /**
      * @ORM\Column(type="boolean",nullable=true)
    */     
    protected $delivery;
   
    /**
      * @ORM\Column(type="integer",nullable=true)
    */
    protected $price;
    
    /**
      * @ORM\Column(type="string",nullable=true)
    */
    protected $body;
    
    /**
      * @ORM\Column(type="string",nullable=true)
    */
    protected $body_final;
    
    /**
      * @ORM\Column(type="datetime")
    */
    protected $createAt;
    
    
    /**
      * @ORM\Column(type="datetime",nullable=true)
    */
    protected $synx;

    /**
      * @ORM\Column(type="string",nullable=true)
    */
    protected $img;    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getImg()
    {
        return $this->img;
    }
    
    public function setImg($img)
    {
        $this->img = $img;
        return $this;
    }    
    
    /**
     * Set nuberId
     *
     * @param integer $nuberId
     *
     * @return UserGift
     */
    public function setNuberId($nuberId)
    {
        $this->nuber_id = $nuberId;

        return $this;
    }

    /**
     * Get nuberId
     *
     * @return integer
     */
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

    /**
     * Set gtype
     *
     * @param integer $gtype
     *
     * @return UserGift
     */
    public function setGtype($gtype)
    {
        $this->gtype = $gtype;

        return $this;
    }

    /**
     * Get gtype
     *
     * @return integer
     */
    public function getGtype()
    {
        return $this->gtype;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
    
    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return UserGift
     */
    public function setProductId($productId)
    {
        $this->product_id = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return UserGift
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return UserGift
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set bodyFinal
     *
     * @param string $bodyFinal
     *
     * @return UserGift
     */
    public function setBodyFinal($bodyFinal)
    {
        $this->body_final = $bodyFinal;

        return $this;
    }

    /**
     * Get bodyFinal
     *
     * @return string
     */
    public function getBodyFinal()
    {
        return $this->body_final;
    }

    /**
     * Set createAt
     *
     * @param \DateTime $createAt
     *
     * @return UserGift
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set synx
     *
     * @param \DateTime $synx
     *
     * @return UserGift
     */
    public function setSynx($synx)
    {
        $this->synx = $synx;

        return $this;
    }

    /**
     * Get synx
     *
     * @return \DateTime
     */
    public function getSynx()
    {
        return $this->synx;
    }

    /**
     * Set delivery
     *
     * @param boolean $delivery
     *
     * @return UserGift
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return boolean
     */
    public function getDelivery()
    {
        return $this->delivery;
    }
    
    public function setByReg($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getByReg()
    {
        return $this->delivery;
    }
}
