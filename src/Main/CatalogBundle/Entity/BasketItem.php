<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_basket")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\BasketItemRepository")
 */
 
class BasketItem {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id ;

    /**
     *  @ORM\ManyToOne(targetEntity="Main\NuberBundle\Entity\User", inversedBy="basket")
     *  @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */   
    protected $user;
    
    /**
     *  @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product")
     *  @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    
    protected $product;
    
     /**
      * @ORM\Column(type="boolean")
    */    
    protected $isDiscount = false;
    
    protected $trueDiscount;
    
    /**
      * @ORM\Column(type="integer",nullable=true)
    */    
    protected $baseId;    
    
    /**
      * @ORM\Column(type="integer")
    */    
    protected $count;
    
    /** @ORM\Column(type="boolean") */
    protected $isGift = false;    

    /**
      * @ORM\Column(type="integer",nullable=true)
    */ 
    protected $parent;
    
    /**
      * @ORM\Column(type="integer")
    */    
    protected $maxCount = 0;
    
    /**
     * @ORM\ManyToMany(targetEntity="Main\CatalogBundle\Entity\ProductDiscount", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="client_basket_item_discount",
     *      joinColumns={@ORM\JoinColumn(name="basket_item_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="discount_id", referencedColumnName="id")}
     *      )
    */
    protected $discount;

    /**
     *  @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Order", inversedBy="item",cascade={"persist"})
     *  @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */    
    protected $order;
    
     /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    /** @ORM\Column(type="integer") */ 
    protected $status = 0;  /*0 - new, 1 - complete, -1 - deleted */

    public function getIsGift()
    {
        return $this->isGift;
    }
    
    public function setIsGift($isGift)
    {
        $this->isGift = $isGift;
        return $this;
    }
    
    public function getIsDiscount()
    {
        return $this->isDiscount;
    }
    
    public function setIsDiscount($isDiscount)
    {
        $this->isDiscount = $isDiscount;
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }
    
    public function setParent( $parent = null )
    {
        $this->parent = $parent;
        return $this;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setMaxCount($count)
    {
        $this->maxCount = $count;

        return $this;
    }

    public function getMaxCount()
    {
        return $this->maxCount;
    }
    
    public function setBaseId($bid)
    {
        $this->baseId = $bid;

        return $this;
    }

    public function getBaseId()
    {
        return $this->baseId;
    }    
    
    /**
     * Set count
     *
     * @param integer $count
     *
     * @return BasketItem
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

     /**
     * @ORM\PrePersist
    */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();

        return $this;
    }
    
    

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param \Main\NuberBundle\Entity\User $user
     *
     * @return BasketItem
     */
    public function setUser(\Main\NuberBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Main\NuberBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set product
     *
     * @param \Main\CatalogBundle\Entity\Product $product
     *
     * @return BasketItem
     */
    public function setProduct(\Main\CatalogBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Main\CatalogBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set order
     *
     * @param \Main\CatalogBundle\Entity\Order $order
     *
     * @return BasketItem
     */
    public function setOrder(\Main\CatalogBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Main\CatalogBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->discount = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add discount
     *
     * @param \Main\CatalogBundle\Entity\ProductDiscount $discount
     *
     * @return BasketItem
     */
    public function addDiscount(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->discount[] = $discount;

        return $this;
    }

    /**
     * Remove discount
     *
     * @param \Main\CatalogBundle\Entity\ProductDiscount $discount
     */
    public function removeDiscount(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->discount->removeElement($discount);
    }

    /**
     * Get discount
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
