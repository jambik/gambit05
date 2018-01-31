<?php
namespace Main\NuberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_updater")
 * @ORM\HasLifecycleCallbacks()
 */
 
class Updater {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Main\NuberBundle\Entity\Project", inversedBy="UpdaterData")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
    */
    protected $project;

/** @ORM\Column(type="datetime") */    
    protected $createdAt;

    /** @ORM\Column(type="boolean") */ 
    protected $type; //0 - read, 1 - send
    
    /** @ORM\Column(type="string") */
    protected $secret;
    
    /** @ORM\Column(type="boolean") */ 
    protected $page;

    /**
     * @ORM\Column(type="integer")
    */     
    protected $client_id = 0;
    
   # protected $route;
   
   /** @ORM\Column(type="boolean",nullable=true) */ 
   protected $product;

   /** @ORM\Column(type="boolean",nullable=true) */ 
   protected $user_order;   
  
   # protected $user;
   
    /** @ORM\Column(type="boolean") */ 
    protected $status = 0;
 
 /** @ORM\Column(type="datetime",nullable=true) */   
    protected $uploadDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    
    public function genHash(){
        return mt_rand(1000000000000000, 9999999999999999);   
    }
    
  /**
     * @ORM\PrePersist
    */   
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
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
     * Set key
     *
     * @param string $key
     *
     * @return Updater
     */
     
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function getOrder()
    {
        return $this->user_order;
    }
    
    public function setOrder($order)
    {
        $this->user_order = $order;

        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }
         
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Updater
     */
     

    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     *
     * @return Updater
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return \DateTime
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set project
     *
     * @param \Main\NuberBundle\Entity\Project $project
     *
     * @return Updater
     */
    public function setProject(\Main\NuberBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Main\NuberBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add page
     *
     * @param \Main\NuberBundle\Entity\Page $page
     *
     * @return Updater
     */
    public function addPage(\Main\PageBundle\Entity\Page $page)
    {
        $this->page->add($page);

        return $this;
    }

    /**
     * Remove page
     *
     * @param \Main\NuberBundle\Entity\Page $page
     */
    public function removePage(\Main\PageBundle\Entity\Page $page)
    {
        $this->page->removeElement($page);
    }

    /**
     * Get page
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->page = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return Updater
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Updater
     */
    public function setClientId($clientId)
    {
        $this->client_id = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->client_id;
    }
}
