<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_discount")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\ProductDiscount")
 */
 
class ProductDiscount {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id ;
    
    /** @ORM\Column(type="string",nullable=true) */ 
    protected $title;
    
    

        /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\DiscountType", inversedBy="discount")
     * @ORM\JoinColumn(name="discount_type_id", referencedColumnName="id")
     */ 
    protected $DiscountType;
    
  
     /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product", inversedBy="discount")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;
    
    /** @ORM\Column(type="integer") */ 
    protected $top_in_list = 0;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    /** @ORM\Column(type="float", nullable=true) */ 
    protected $percent;
    
    /** @ORM\Column(type="float", nullable=true) */ 
    protected $num;
    
    /** @ORM\Column(type="boolean") */
    protected $active = true;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\DiscountCondition", mappedBy="discount")
     */
    protected $condition;
    
    /** @ORM\Column(type="integer", nullable=true) */
    protected $nuber_id;
    
    
    public function setData($disc){
        $this->setNuberId($disc->id);
        $this->setActive($disc->active);
        $this->setNum($disc->num);
        $this->setPercent($disc->percent);
        $this->setTopInList($disc->top_in_list);
        $this->setTitle($disc->title);
        
    }
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->condition = new \Doctrine\Common\Collections\ArrayCollection();
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
    /**
     * Set title
     *
     * @param string $title
     *
     * @return ProductDiscount
     */
    public function setNuberId($id)
    {
        $this->nuber_id = $id;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getNuberId()
    {
        return $this->nuber_id;
    }
    
    /**
     * Set title
     *
     * @param string $title
     *
     * @return ProductDiscount
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set topInList
     *
     * @param integer $topInList
     *
     * @return ProductDiscount
     */
    public function setTopInList($topInList)
    {
        $this->top_in_list = $topInList;

        return $this;
    }

    /**
     * Get topInList
     *
     * @return integer
     */
    public function getTopInList()
    {
        return $this->top_in_list;
    }

     /**
     * @ORM\PrePersist
    */
    public function setCreatedAt($createdAt)
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
     * Set percent
     *
     * @param float $percent
     *
     * @return ProductDiscount
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set num
     *
     * @param float $num
     *
     * @return ProductDiscount
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return float
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ProductDiscount
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set product
     *
     * @param \Main\CatalogBundle\Entity\Product $product
     *
     * @return ProductDiscount
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
     * Add condition
     *
     * @param \Main\CatalogBundle\Entity\DiscountCondition $condition
     *
     * @return ProductDiscount
     */
    public function addCondition(\Main\CatalogBundle\Entity\DiscountCondition $condition)
    {
        $this->condition[] = $condition;

        return $this;
    }

    /**
     * Remove condition
     *
     * @param \Main\CatalogBundle\Entity\DiscountCondition $condition
     */
    public function removeCondition(\Main\CatalogBundle\Entity\DiscountCondition $condition)
    {
        $this->condition->removeElement($condition);
    }

    /**
     * Get condition
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
