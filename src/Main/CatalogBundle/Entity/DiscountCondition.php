<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_discount_condition")
 * @ORM\HasLifecycleCallbacks()
 */
 
class DiscountCondition {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id ;
    
    /**
     *  @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductDiscount", inversedBy="condition")
     *  @ORM\JoinColumn(name="discount_id", referencedColumnName="id")
     */    
    protected $discount;
    
    /** @ORM\Column(type="integer") */ 
    protected $func_id;
    
    /** @ORM\Column(type="datetime", nullable=true) */
    protected $time_start;
    
    /** @ORM\Column(type="datetime", nullable=true) */
    protected $time_stop;
    
    /** @ORM\Column(type="boolean", nullable=true) */ 
    protected $evRepeat;
    
    /** @ORM\Column(type="integer", nullable=true) */ 
    protected $buy_count;
    
    /** @ORM\Column(type="integer", nullable=true) */ 
    protected $full_basket_sum;
    
    /** @ORM\Column(type="integer", nullable=true) */ 
    protected $count_basket_item;
    
    /** @ORM\Column(type="integer") */ 
    protected $nuber_id = 0;
    
    public function setData($cond){
        $this->setNuberId($cond->id);
        $this->setCountBasketItem($cond->count_basket_item);
        $this->setFullBasketSum($cond->full_basket_sum);
        $this->setBuyCount($cond->buy_count);
        $this->setRepeat($cond->repeat);
        
        $this->setTimeStart(\DateTime::createFromFormat("j.n.Y H:i", $cond->time_start));
        $this->setTimeStop(\DateTime::createFromFormat("j.n.Y H:i", $cond->time_stop));
        
        $this->setFuncId($cond->func_id);
    }
    
    public function setNuberId($id = 0)
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set funcId
     *
     * @param integer $funcId
     *
     * @return DiscountCondition
     */
    public function setFuncId($funcId)
    {
        $this->func_id = $funcId;

        return $this;
    }

    /**
     * Get funcId
     *
     * @return integer
     */
    public function getFuncId()
    {
        return $this->func_id;
    }

    /**
     * Set timeStart
     *
     * @param \DateTime $timeStart
     *
     * @return DiscountCondition
     */
    public function setTimeStart($timeStart)
    {
        $this->time_start = $timeStart;

        return $this;
    }

    /**
     * Get timeStart
     *
     * @return \DateTime
     */
    public function getTimeStart()
    {
        return $this->time_start;
    }

    /**
     * Set timeStop
     *
     * @param \DateTime $timeStop
     *
     * @return DiscountCondition
     */
    public function setTimeStop($timeStop)
    {
        $this->time_stop = $timeStop;

        return $this;
    }

    /**
     * Get timeStop
     *
     * @return \DateTime
     */
    public function getTimeStop()
    {
        return $this->time_stop;
    }

    /**
     * Set repeat
     *
     * @param integer $repeat
     *
     * @return DiscountCondition
     */
    public function setRepeat($repeat)
    {
        $this->evRepeat = $repeat;

        return $this;
    }

    /**
     * Get repeat
     *
     * @return integer
     */
    public function getRepeat()
    {
        return $this->evRepeat;
    }

    /**
     * Set buyCount
     *
     * @param integer $buyCount
     *
     * @return DiscountCondition
     */
    public function setBuyCount($buyCount)
    {
        $this->buy_count = $buyCount;

        return $this;
    }

    /**
     * Get buyCount
     *
     * @return integer
     */
    public function getBuyCount()
    {
        return $this->buy_count;
    }

    /**
     * Set fullBasketSum
     *
     * @param integer $fullBasketSum
     *
     * @return DiscountCondition
     */
    public function setFullBasketSum($fullBasketSum)
    {
        $this->full_basket_sum = $fullBasketSum;

        return $this;
    }

    /**
     * Get fullBasketSum
     *
     * @return integer
     */
    public function getFullBasketSum()
    {
        return $this->full_basket_sum;
    }

    /**
     * Set countBasketItem
     *
     * @param integer $countBasketItem
     *
     * @return DiscountCondition
     */
    public function setCountBasketItem($countBasketItem)
    {
        $this->count_basket_item = $countBasketItem;

        return $this;
    }

    /**
     * Get countBasketItem
     *
     * @return integer
     */
    public function getCountBasketItem()
    {
        return $this->count_basket_item;
    }

    /**
     * Set discount
     *
     * @param \Main\CatalogBundle\Entity\ProductDiscount $discount
     *
     * @return DiscountCondition
     */
    public function setDiscount(\Main\CatalogBundle\Entity\ProductDiscount $discount = null)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return \Main\CatalogBundle\Entity\ProductDiscount
     */
    public function getDiscount()
    {
        return $this->discount;
    }
}
