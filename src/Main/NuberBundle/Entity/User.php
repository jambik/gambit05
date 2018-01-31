<?php
namespace Main\NuberBundle\Entity;;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /** @ORM\Column(type="string",nullable=true) */ 
    protected $name;

    /** @ORM\Column(type="string",nullable=true) */    
    protected $phone;
    /** @ORM\Column(type="string",nullable=true) */
    protected $street;
    /** @ORM\Column(type="string",nullable=true) */
    protected $house;
    /** @ORM\Column(type="string",nullable=true) */
    protected $build;
    /** @ORM\Column(type="integer",nullable=true) */
    protected $apartment;
    
    /** @ORM\Column(type="string",nullable=true) */
    protected $ip;
    
    /**
     * @ORM\ManyToOne(targetEntity="Main\NuberBundle\Entity\Project", inversedBy="user")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
    */
    protected $project;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\BasketItem", mappedBy="user")
     */    
    protected $basket;
    
     /** @ORM\Column(type="datetime",nullable=true) */
    protected $createdAt;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\Order", mappedBy="user")
     */    
    protected $order;
    
    /** @ORM\Column(type="datetime",nullable=true) */    
    protected $lastStep;
    
    /** @ORM\Column(type="boolean") */
    protected $isReg = false;
   
     /** @ORM\Column(type="datetime",nullable=true) */ 
    protected $regDate;
    
    public function getRegDate()
    {
        return $this->regDate;
    }
    
    public function setRegDate($d)
    {
        $this->regDate = $d;
        return $this;
    }
       
    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    public function getIp()
    {
        return $this->ip;
    }
    
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }
    
    public function getLastStep()
    {
        return $this->lastStep;
    }
    
    public function setLastStep($lastStep)
    {
        $this->lastStep = $lastStep;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @ORM\PrePersist
    */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime();
        return $this;
    }
    
    public function getIsReg(){
        return $this->isReg;
    }
    public function setIsReg($isReg){
        $this->isReg = $isReg;
        return $this;
    }    

    public function getId(){
        return $this->id;
    }
    public function setID($id){
        $this->id = $id;
        return $this;
    }     
    
    public function getPhone(){
        return $this->phone;
    }
    
    public function setPhone($phone){
        $this->phone = $phone;
        return $this;
    }    
    
    public function getName(){
        return $this->name;
    }
    
    public function setName($name){
        $this->name = $name;
        return $this;
    }    
    public function getStreet(){
        return $this->street;
    }

    public function setStreet($street){
        $this->street = $street;
        return $this;
    }
        
    public function getHouse(){
        return $this->house;
    }
    
    public function setHouse($house){
        $this->house = $house;
        return $this;
    }
        
    public function getBuild(){
        return $this->build;
    }

    public function setBuild($build){
        $this->build = $build;
        return $this;
    }
    
    public function getApartment(){
        return $this->apartment;
    }

    public function setApartment($apartment){
        $this->apartment = $apartment;
        return $this;
    }
    
    public function getUserName(){
        return $this->username;
    }
    
 
    
    public function __toString(){
        return $this->username;
    }

    /**
     * Set project
     *
     * @param \Main\NuberBundle\Entity\Project $project
     *
     * @return User
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
     * Add basket
     *
     * @param \Main\CatalogBundle\Entity\BasketItem $basket
     *
     * @return User
     */
    public function addBasket(\Main\CatalogBundle\Entity\BasketItem $basket)
    {
        $this->basket[] = $basket;

        return $this;
    }

    /**
     * Remove basket
     *
     * @param \Main\CatalogBundle\Entity\BasketItem $basket
     */
    public function removeBasket(\Main\CatalogBundle\Entity\BasketItem $basket)
    {
        $this->basket->removeElement($basket);
    }

    /**
     * Get basket
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBasket()
    {
        return $this->basket;
    }

    /**
     * Add order
     *
     * @param \Main\CatalogBundle\Entity\Order $order
     *
     * @return User
     */
    public function addOrder(\Main\CatalogBundle\Entity\Order $order)
    {
        $this->order[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \Main\CatalogBundle\Entity\Order $order
     */
    public function removeOrder(\Main\CatalogBundle\Entity\Order $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Get order
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrder()
    {
        return $this->order;
    }
}
