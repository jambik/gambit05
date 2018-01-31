<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_product_group")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\GroupRepository")
 */
 
class ProductGroup {
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
     * @ORM\Column(type="string", length=100, nullable=true)
    */ 
    protected $_id;
        
    /** @ORM\Column(type="boolean") */
    protected $isIiko;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;

    
    /**
     * @ORM\ManyToMany(targetEntity="Main\CatalogBundle\Entity\ProductGroup", mappedBy="me_recommended")
     */
    protected $i_recommended; // ссылки на группы для которых текущая группа рекомендована

    /**
     * @ORM\ManyToMany(targetEntity="Main\CatalogBundle\Entity\ProductGroup", inversedBy="i_recommended", indexBy="id")
     * @ORM\JoinTable(name="client_group_by_group_recommended",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="recommended_group_id", referencedColumnName="id")}
     *      )
     */    
    protected $me_recommended; // ссылки на группы рекомендованные для текущей
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */
    protected $additionalInfo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */        
    protected $code;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\PageBundle\Entity\ProductByPage", mappedBy="group")
     */ 
    protected $page;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\Product", mappedBy="parentGroup")
    */
    protected $product;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */        
    protected $description;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $alias;
    
    /** @ORM\Column(type="boolean") */
    protected $hideAliasByURL = false;    
  
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $seoDescription;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */        
    protected $seoKeywords;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $seoText;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $seoTitle;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */        
    protected $tags;
        
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductImage", mappedBy="group")
    */ 
    protected $images;
    
    /** @ORM\Column(type="boolean") */
    protected $isDeleted = false;     
    
    /** @ORM\Column(type="boolean") */     
    protected $isIncludedInMenu;
    
    /** @ORM\Column(type="integer",nullable=true) */    
    protected $order_by;

    /**
      * @ORM\Column(type="integer",nullable=true) 
    */
    protected $revision;

    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductDiscount", mappedBy="productGroup")
    */
    protected $modifier;
	
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $synx;    

    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroup")
     * @ORM\JoinColumn(name="parent_group_id", referencedColumnName="id")
     */    
    protected $parentGroup;

    
    
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $workTime_start = null;
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $workTime_stop = null; 
    
	public function applyChanges($sGroup, $parentGroup){ 
        $this->setAdditionalInfo( $sGroup->additionalInfo);
        $this->setCode($sGroup->code);
        $this->setDescription($sGroup->description);
        $this->setRevision($sGroup->revision);
        $this->setIikoId($sGroup->iikoId);

        $this->alias = ($this->alias != '')?$this->alias:$this->translit($sGroup->name);
        
        $this->setName($sGroup->name);
        $this->setSeoDescription($sGroup->seoDescription);
        $this->setSeoKeywords($sGroup->seoKeywords);
        $this->setSeoText($sGroup->seoText);
        $this->setSeoTitle($sGroup->seoTitle);
        $this->setTags($sGroup->tags);
		
		if($sGroup->name == "Акции"){
			$this->setIsIncludedInMenu(false);
		} else {
            $this->setIsIncludedInMenu($sGroup->isIncludedInMenu);
        }
        //$this->setOrderBy($sGroup->order);
        $this->setParentGroup($parentGroup);
        $this->setNuberId($sGroup->nuberId);
        $this->setIsIiko(true);
        
        return true;
    }
    
    public function getWorkTime_start()
    {
        return $this->workTime_start;
    }

    public function getWorkTime_stop()
    {
        return $this->workTime_stop;
    }
	
	public function setIikoId($_id)
    {
        $this->_id =  $_id;

        return $this;
    }
    public function getIikoId()
    {
        return $this->_id;
    }
    
    public function addModifier(\Main\CatalogBundle\Entity\ProductDiscount $modifier)
    {
        $this->modifier[] = $modifier;
        return $this;
    }

    public function removeModifier(\Main\CatalogBundle\Entity\ProductDiscount $modifier)
    {
        $this->modifier->removeElement($modifier);
    }

    public function getModifier()
    {
        return $this->modifier;
    }
	
    public function __toString(){
        
        return $this->name;
    }
    
    public function setData($g){
        
        $this->nuber_id =           $g->id;
        $this->_id =                $g->_id;
        $this->alias =              ($this->alias != '')?$this->alias:$this->translit($g->name);
        $this->isIiko =             $g->is_iiko;
        $this->additionalInfo =     $g->additional_info;
        $this->code =               $g->code;
        $this->description =        $g->description;
        $this->name =               $g->name;
        //$this->seoDescription =     $g->seo_description;
        //$this->seoKeywords =        $g->seo_keywords;
        //$this->seoText =            $g->seo_text;
        //$this->seoTitle =           $g->seo_title;
        $this->tags =               $g->tags;
        $this->isIncludedInMenu =   $g->is_included_in_menu;
        $this->order_by =           $g->order_by;

    }   
    
    public function translit($str){
        $alphavit = array(
            /*--*/
            "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
            "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l", "м"=>"m",
            "н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t",
            "у"=>"y","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch", "ш"=>"sh","щ"=>"sh",
            "ы"=>"i","э"=>"e","ю"=>"u","я"=>"ya",
            /*--*/
            " "=>"_","*"=>"_","/"=>"_","|"=>"_", "!"=>"_", "@"=>"_", "#"=>"_",
            "$"=>"_", "%"=>"_", "&"=>"and", "?"=>"_", "№"=>"_",'"'=>"",
            /*--*/
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E", "Ё"=>"Yo",
            "Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"I","К"=>"K", "Л"=>"L","М"=>"M",
            "Н"=>"N","О"=>"O","П"=>"P", "Р"=>"R","С"=>"S","Т"=>"T","У"=>"Y",
            "Ф"=>"F", "Х"=>"H","Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh",
            "Ы"=>"I","Э"=>"E","Ю"=>"U","Я"=>"Ya",
            "ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>""
        );
        return strtolower(strtr($str, $alphavit));
    }  
     
    public function setSynx($synx)
    {
        $this->synx = $synx;

        return $this;
    }


    public function getSynx()
    {
        return $this->synx;
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
     * @return ProductGroup
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
    public function setCreateAt()
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
     * Set additionalInfo
     *
     * @param string $additionalInfo
     *
     * @return ProductGroup
     */
    public function setAdditionalInfo($additionalInfo)
    {
        $this->additionalInfo = $additionalInfo;

        return $this;
    }

    /**
     * Get additionalInfo
     *
     * @return string
     */
    public function getAdditionalInfo()
    {
        return $this->additionalInfo;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return ProductGroup
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ProductGroup
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    
    public function setHideAliasByURL($hideAliasByURL)
    {
        $this->hideAliasByURL = $hideAliasByURL;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getHideAliasByURL()
    {
        return $this->hideAliasByURL;
    }
    
    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return ProductGroup
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductGroup
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }    
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set seoDescription
     *
     * @param string $seoDescription
     *
     * @return ProductGroup
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * Get seoDescription
     *
     * @return string
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * Set seoKeywords
     *
     * @param string $seoKeywords
     *
     * @return ProductGroup
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    /**
     * Get seoKeywords
     *
     * @return string
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * Set seoText
     *
     * @param string $seoText
     *
     * @return ProductGroup
     */
    public function setSeoText($seoText)
    {
        $this->seoText = $seoText;

        return $this;
    }

    /**
     * Get seoText
     *
     * @return string
     */
    public function getSeoText()
    {
        return $this->seoText;
    }

    /**
     * Set seoTitle
     *
     * @param string $seoTitle
     *
     * @return ProductGroup
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    /**
     * Get seoTitle
     *
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return ProductGroup
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set isIncludedInMenu
     *
     * @param boolean $isIncludedInMenu
     *
     * @return ProductGroup
     */
    public function setIsIncludedInMenu($isIncludedInMenu)
    {
        $this->isIncludedInMenu = $isIncludedInMenu;

        return $this;
    }

    /**
     * Get isIncludedInMenu
     *
     * @return boolean
     */
    public function getIsIncludedInMenu()
    {
        return $this->isIncludedInMenu;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return ProductGroup
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set revision
     *
     * @param integer $revision
     *
     * @return ProductGroup
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
     * Add product
     *
     * @param \Main\NuberBundle\Entity\Product $product
     *
     * @return ProductGroup
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

    /**
     * Get product
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add image
     *
     * @param \Main\NuberBundle\Entity\ProductImage $image
     *
     * @return ProductGroup
     */
    public function addImage(\Main\CatalogBundle\Entity\ProductImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Main\NuberBundle\Entity\ProductImage $image
     */
    public function removeImage(\Main\CatalogBundle\Entity\ProductImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set parentGroup
     *
     * @param \Main\NuberBundle\Entity\ProductGroup $parentGroup
     *
     * @return ProductGroup
     */
    public function setParentGroup(\Main\CatalogBundle\Entity\ProductGroup $parentGroup = null)
    {
        $this->parentGroup = $parentGroup;

        return $this;
    }

    /**
     * Get parentGroup
     *
     * @return \Main\NuberBundle\Entity\ProductGroup
     */
    public function getParentGroup()
    {
        return $this->parentGroup;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductGroup
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set orderBy
     *
     * @param integer $orderBy
     *
     * @return ProductGroup
     */
    public function setOrderBy($orderBy)
    {
        $this->order_by = $orderBy;

        return $this;
    }

    /**
     * Get orderBy
     *
     * @return integer
     */
    public function getOrderBy()
    {
        return $this->order_by;
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->i_recommended = new \Doctrine\Common\Collections\ArrayCollection();
        $this->me_recommended = new \Doctrine\Common\Collections\ArrayCollection();
        $this->product = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add iRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $iRecommended
     *
     * @return ProductGroup
     */
    public function addIRecommended(\Main\CatalogBundle\Entity\ProductGroup $iRecommended)
    {
        $this->i_recommended[] = $iRecommended;

        return $this;
    }

    /**
     * Remove iRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $iRecommended
     */
    public function removeIRecommended(\Main\CatalogBundle\Entity\ProductGroup $iRecommended)
    {
        $this->i_recommended->removeElement($iRecommended);
    }

    /**
     * Get iRecommended
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIRecommended()
    {
        return $this->i_recommended;
    }

    /**
     * Add meRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $meRecommended
     *
     * @return ProductGroup
     */
    public function addMeRecommended(\Main\CatalogBundle\Entity\ProductGroup $meRecommended)
    {
        $this->me_recommended[] = $meRecommended;

        return $this;
    }

    /**
     * Remove meRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $meRecommended
     */
    public function removeMeRecommended(\Main\CatalogBundle\Entity\ProductGroup $meRecommended)
    {
        $this->me_recommended->removeElement($meRecommended);
    }

    /**
     * Get meRecommended
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeRecommended()
    {
        return $this->me_recommended;
    }
}
