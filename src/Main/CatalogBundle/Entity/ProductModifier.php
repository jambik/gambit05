<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_product_modifier")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\ModifierRepository")
 */
 
class ProductModifier {
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
    
    /** @ORM\Column(type="boolean") */
    protected $isIiko;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $maxAmount;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $minAmount;
 
    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product", inversedBy="modifiers")
     * @ORM\JoinColumn(name="mod_product_id", referencedColumnName="id")
     */    
    protected $modProduct;
 
     /** @ORM\Column(type="integer",nullable=true) */ 
    protected $revision;
 
     /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */    
    protected $modifier;
    
        /** @ORM\Column(type="boolean") */ 
    protected $required;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $defAmount;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
    */ 
    protected $_id;
	
    /** @ORM\Column(type="boolean") */ 
    protected $isDeleted = false;	
	
    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroupModifier", inversedBy="childModifiers",cascade={"persist"})
     * @ORM\JoinColumn(name="product_mod_group_id", referencedColumnName="id")
     */    
    protected $modGroup; 

    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }  
	
    public function applyChanges($mod, $product, $modifier, $group){
        
        $this->setMaxAmount($mod->maxAmount);
        $this->setMinAmount($mod->minAmount);
        $this->setRequired($mod->required);
        $this->setDefaultAmount($mod->defaultAmount);
        $this->setModProduct($product);
        $this->setModifier($modifier);
        $this->setNuberId($mod->nuberId);
        $this->setRevision($mod->revision);
        $this->setModGroup($group);
        $this->setIsIiko(true);
        $this->setIikoId($mod->iikoId);

        return true;
    }

    public function setModGroup(\Main\CatalogBundle\Entity\ProductGroupModifier $modGroup = null)
    {
        $this->modGroup = $modGroup;
        return $this;
    }

	public function setIikoId($_id)
    {
        $this->_id = $_id;
        return $this;
    }
    
    public function getIikoId()
    {
        return $this->_id;
    }
	
    public function getModGroup()
    {
        return $this->modGroup;
    }
	
	public function setRevision($revision)
    {
        $this->revision = $revision;
        return $this;
    }

    public function getRevision()
    {
        return $this->revision;
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
     * @return ProductModifier
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
     * @ORM\PrePersist
    */
    public function setCreateAt($createAt)
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
     * Set maxAmount
     *
     * @param integer $maxAmount
     *
     * @return ProductModifier
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
     * @return ProductModifier
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
     * Set requared
     *
     * @param boolean $requared
     *
     * @return ProductModifier
     */
    public function setRequared($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get requared
     *
     * @return boolean
     */
    public function getRequared()
    {
        return $this->required;
    }

    /**
     * Set defaultAmount
     *
     * @param integer $defaultAmount
     *
     * @return ProductModifier
     */
    public function setDefaultAmount($defAmount)
    {
        $this->defAmount = $defAmount;

        return $this;
    }

    /**
     * Get defaultAmount
     *
     * @return integer
     */
    public function getDefaultAmount()
    {
        return $this->defAmount;
    }

    /**
     * Set modProduct
     *
     * @param \Main\NuberBundle\Entity\Product $modProduct
     *
     * @return ProductModifier
     */
    public function setModProduct(\Main\CatalogBundle\Entity\Product $modProduct = null)
    {
        $this->modProduct = $modProduct;

        return $this;
    }

    /**
     * Get modProduct
     *
     * @return \Main\NuberBundle\Entity\Product
     */
    public function getModProduct()
    {
        return $this->modProduct;
    }

    /**
     * Set modifier
     *
     * @param \Main\NuberBundle\Entity\Product $modifier
     *
     * @return ProductModifier
     */
    public function setModifier(\Main\CatalogBundle\Entity\Product $modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }

    /**
     * Get modifier
     *
     * @return \Main\NuberBundle\Entity\Product
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductModifier
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return ProductModifier
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
     * Set nuberId
     *
     * @param integer $nuberId
     *
     * @return Page
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
}
