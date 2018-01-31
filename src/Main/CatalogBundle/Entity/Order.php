<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user_order")
 * @ORM\HasLifecycleCallbacks()
 */
 
class Order {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id ;

    /**
     *  @ORM\ManyToOne(targetEntity="Main\NuberBundle\Entity\User", inversedBy="order")
     *  @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */   
    protected $user;
    
    /** @ORM\Column(type="integer",nullable=true) */
    protected $summ;

    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\BasketItem", mappedBy="order")
     */    
    protected $item;
    
     /** @ORM\Column(type="text",nullable=true) */   
    protected $comment;
    
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $synx;
    
    /** @ORM\Column(type="integer") */
    
    //0 - new, 1 - complete, 2 - cancel
    protected $status = 0; 
       
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $sendIiko;
    
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $sendMail;

    /** @ORM\Column(type="boolean") */
    protected $isPickUp  = false;    
    
     /** @ORM\Column(type="datetime") */
    protected $createdAt;

    /** @ORM\Column(type="boolean") */
    protected $sendError  = false;

    public function setSendError($err)
    {
        $this->sendError = $err;
        return $this;
    }

    public function getSendError()
    {
        return $this->sendError;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->item = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setIsPickUp($is)
   {
        $this->isPickUp = $is;
        return $this;
   }

   public function getIsPickUp()
   {
        return $this->isPickUp ;
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
     * @ORM\PrePersist
    */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function setSumm($sum)
    {
        $this->summ = $sum;
        return $this;
    }

    public function getSumm()
    {
        return $this->summ;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function setSynx($synx)
    {
        $this->synx = $synx;

        return $this;
    }
    
    public function setSendMail($send_mail)
    {
        $this->sendMail = $send_mail;

        return $this;
    }
    
    public function setSendIiko($send_iiko)
    {
        $this->sendIiko = $send_iiko;

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
    
    public function getSynx()
    {
        return $this->synx;
    }
    public function getSendIiko()
    {
        return $this->sendIiko;
    }
    public function getSendMail()
    {
        return $this->sendMail;
    }
    /**
     * Set user
     *
     * @param \Main\NuberBundle\Entity\User $user
     *
     * @return Order
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
     * Add item
     *
     * @param \Main\CatalogBundle\Entity\BasketItem $item
     *
     * @return Order
     */
    public function addItem(\Main\CatalogBundle\Entity\BasketItem $item)
    {
        $this->item[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \Main\CatalogBundle\Entity\BasketItem $item
     */
    public function removeItem(\Main\CatalogBundle\Entity\BasketItem $item)
    {
        $this->item->removeElement($item);
    }

    /**
     * Get item
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItem()
    {
        return $this->item;
    }
    
    public function getComment()
    {
        return $this->comment;
    }
    
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }
}
