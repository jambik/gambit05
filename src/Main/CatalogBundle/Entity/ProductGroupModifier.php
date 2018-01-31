<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_nuber_product_group_modifier")
 * @ORM\HasLifecycleCallbacks()                                                      
 */
 
class ProductGroupModifier {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;
   
    /** @ORM\Column(type="integer") */ 
    protected $nuber_id;
   
    /** @ORM\Column(type="boolean") */
    protected $isIiko;
   
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $revision;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $maxAmount;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $minAmount; 
    
    /** @ORM\Column(type="boolean") */ 
    protected $required;
    
     /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroup",inversedBy="modifier")
     * @ORM\JoinColumn(name="product_group_id", referencedColumnName="id")
     */     
    protected $productGroup;
    
     
    /**
     * @ORM\ManyToMany(targetEntity="Main\CatalogBundle\Entity\Product", mappedBy="groupModifiers")
    */
    protected $product;             

    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductModifier", mappedBy="modGroup")
    */     
    protected $childModifiers;

    public function applyChanges($gMod){
        
        $this->setMaxAmount($gMod->maxAmount);
        $this->setMinAmount($gMod->minAmount);
        $this->setRequired($gMod->required);
        $this->setRevision($gMod->revision);
        $this->setNuberId($gMod->nuberId);

        $this->setIsIiko(true);
        
        
        return true;
    }

    public function setNuberId($nuberId)
    {
        $this->nuber_id = $nuberId;

        return $this;
    }

    public function getNuberId()
    {
        return $this->nuber_id;
    }    
    
    /**
     * @ORM\PrePersist
    */
    public function setCreateAt($createAt)
    {
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childModifiers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->product = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set isIiko
     *
     * @param boolean $isIiko
     *
     * @return ProductGroupModifier
     */
    public function setIsIiko($isIiko)
    {
        $this->isIiko = $isIiko;

        return $this;
    }

    /**
     * Get isIiko
     *
     * @return boolean
     */
    public function getIsIiko()
    {
        return $this->isIiko;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductGroupModifier
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set revision
     *
     * @param integer $revision
     *
     * @return ProductGroupModifier
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * Get revision
     *
     * @return integer
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set maxAmount
     *
     * @param integer $maxAmount
     *
     * @return ProductGroupModifier
     */
    public function setMaxAmount($maxAmount)
    {
        $this->maxAmount = $maxAmount;

        return $this;
    }

    /**
     * Get maxAmount
     *
     * @return integer
     */
    public function getMaxAmount()
    {
        return $this->maxAmount;
    }

    /**
     * Set minAmount
     *
     * @param integer $minAmount
     *
     * @return ProductGroupModifier
     */
    public function setMinAmount($minAmount)
    {
        $this->minAmount = $minAmount;

        return $this;
    }

    /**
     * Get minAmount
     *
     * @return integer
     */
    public function getMinAmount()
    {
        return $this->minAmount;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return ProductGroupModifier
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set project
     *
     * @param \Main\NuberBundle\Entity\Project $project
     *
     * @return ProductGroupModifier
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
     * Set productGroup
     *
     * @param \Main\NuberBundle\Entity\ProductGroup $productGroup
     *
     * @return ProductGroupModifier
     */
    public function setProductGroup(\Main\CatalogBundle\Entity\ProductGroup $productGroup = null)
    {
        $this->productGroup = $productGroup;

        return $this;
    }

    /**
     * Get productGroup
     *
     * @return \Main\NuberBundle\Entity\ProductGroup
     */
    public function getProductGroup()
    {
        return $this->productGroup;
    }

    /**
     * Set product
     *
     * @param \Main\NuberBundle\Entity\ProductGroup $product
     *
     * @return ProductGroupModifier
     */

    /**
     * Get product
     *
     * @return \Main\NuberBundle\Entity\ProductGroup
     */
    public function getProduct()
    {
        return $this->product;
    }


    public function addChildModifier(\Main\CatalogBundle\Entity\ProductModifier $childModifier)
    {
        $this->childModifiers[] = $childModifier;

        return $this;
    }

    /**
     * Remove childModifier
     *
     * @param \Main\NuberBundle\Entity\ProductModifier $childModifier
     */
    public function removeChildModifier(\Main\CatalogBundle\Entity\ProductModifier $childModifier)
    {
        $this->childModifiers->removeElement($childModifier);
    }

    /**
     * Get childModifiers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildModifiers()
    {
        return $this->childModifiers;
    }

    /**
     * Add product
     *
     * @param \Main\NuberBundle\Entity\Product $product
     *
     * @return ProductGroupModifier
     */
    public function addProduct(\Main\CatalogBundle\Entity\Product $product)
    {
        $this->product[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Main\NuberBundle\Entity\Product $product
     */
    public function removeProduct(\Main\CatalogBundle\Entity\Product $product)
    {
        $this->product->removeElement($product);
    }
}
