<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Main\CatalogBundle\Entity\ProductImage;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_product")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\ProductRepository")
 */
 
class Product {
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
      * @ORM\Column(type="integer", nullable=true)
    */
    protected $revision;
	
    /** @ORM\Column(type="boolean") */
    protected $isIiko;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
    */ 
    protected $_id;    
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */    
    protected $additionalInfo;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\PageBundle\Entity\ProductByPage", mappedBy="product")
     */ 
    protected $page;  
        

    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */    
    protected $code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */    
    protected $description;
    
    /**
     * @ORM\Column(type="string", length=255)
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
    
    /** @ORM\Column(type="float", nullable=true) */
    protected $carbohydrateAmount;

    /** @ORM\Column(type="float", nullable=true) */
    protected $carbohydrateFullAmount;
    
    /**
     * @ORM\Column(type="string", length=200, nullable=true)
    */
    protected $differentPricesOn;
   
    /** @ORM\Column(type="boolean") */ 
    protected $doNotPrintInCheque;
    
    /** @ORM\Column(type="float", nullable=true) */
    protected $energyAmount;

    /** @ORM\Column(type="float", nullable=true) */
    protected $energyFullAmount;
    
    /** @ORM\Column(type="float", nullable=true) */ 
    protected $fatAmount;

	/** @ORM\Column(type="float", nullable=true) */ 
    protected $fatFullAmount;
	
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */         
    protected $alias;   
    
    /** @ORM\Column(type="float", nullable=true) */
    protected $fiberAmount;

    /** @ORM\Column(type="float", nullable=true) */
    protected $fiberFullAmount;
	
    /**
     * @ORM\Column(type="string", length=200, nullable=true)
    */     
    protected $groupId;
    
    /**
     * @ORM\ManyToMany(targetEntity="Main\CatalogBundle\Entity\ProductGroupModifier", inversedBy="product",cascade={"persist"})
     * @ORM\JoinTable(name="tbl_nuber_product_modifiers_group")
     */    
    protected $groupModifiers;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
    */ 
    protected $measureUnit;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductModifier", mappedBy="modProduct")
     */ 
    protected $modifiers;
   
    /** @ORM\Column(type="float", nullable=true) */ 
    protected $price;
    
    protected $minPrice = 0;
    
    /**
     * @ORM\Column(type="string", length=200, nullable=true)
    */    
    protected $productCategoryId;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
    */    
    protected $prohibitedToSaleOn;
    
    /**
     * @ORM\Column(type="string", length=200, nullable=true)
    */
    protected $type;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductDiscount", mappedBy="product")
     */
     
    protected $discount;
    
    protected $discountValue = 0;

    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $discountOrder;
    
    protected $priceWithOutDiscount;
    
    protected $timeUntilTheEnd;
    
    protected $remainderItem; 
    
    protected $isPriceDiscount = false;
    
    protected $maxBasketCount = 0;
    
    protected $href;
    
    /** @ORM\Column(type="float", nullable=true) */
    protected $weight;
    
    /** @ORM\Column(type="datetime",nullable=true) */
    protected $synx;  

    /** @ORM\Column(type="boolean") */
    protected $isDeleted = false;      

    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\ProductImage", mappedBy="product",cascade={"persist"})
    */     
    protected $images;
    
    /** @ORM\Column(type="boolean") */ 
    protected $isIncludedInMenu;
    
    /** @ORM\Column(type="integer",nullable=true) */ 
    protected $order_by = 0;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\CatalogBundle\Entity\UserGift", mappedBy="product")
    */
    protected $gift;
    
    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroup", inversedBy="product")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id",nullable=true)
    */
    protected $parentGroup;
    
    protected $trueDiscount;

	public function applyChanges($sProd, $parentGroup){
        $this->setAdditionalInfo( $sProd->additionalInfo);
        $this->setCode($sProd->code);
        $this->setRevision($sProd->revision);
        $this->setDescription($sProd->description);
        $this->setName($sProd->name);
        $this->setSeoDescription($sProd->seoDescription);
        $this->setSeoKeywords($sProd->seoKeywords);
        $this->setSeoText($sProd->seoText);
        $this->setSeoTitle($sProd->seoTitle);
        $this->setTags($sProd->tags);
        $this->setIsIncludedInMenu($sProd->isIncludedInMenu);
        $this->setOrderBy($sProd->order);
        $this->setParentGroup($parentGroup);
        $this->setCarbohydrateAmount($sProd->carbohydrateAmount);
        $this->setCarbohydrateFullAmount($sProd->carbohydrateFullAmount);
        $this->setDoNotPrintInCheque($sProd->doNotPrintInCheque);
        $this->setEnergyAmount($sProd->energyAmount);
        $this->setEnergyFullAmount($sProd->energyFullAmount);
        $this->setFatAmount($sProd->fatAmount);
        $this->setFatFullAmount($sProd->fatFullAmount);
        $this->setFiberAmount($sProd->fiberAmount);
        $this->setFiberFullAmount($sProd->fiberFullAmount);
        //$this->setGroupId($sProd->groupId);
        $this->setMeasureUnit($sProd->measureUnit);
        $this->setPrice($sProd->price);
        $this->setProductCategoryId($sProd->productCategoryId);
        $this->setType($sProd->type);
        $this->setWeight($sProd->weight);
        $this->setIsIiko(true);
        $this->setNuberId($sProd->nuberId);
        $this->setIikoId($sProd->iikoId);
        
        $this->alias = ($this->alias != '')?$this->alias:$this->translit($sProd->name);
        return true;
    }
 
    public function setFiberFullAmount($fiberFullAmount)
    {
        $this->fiberFullAmount = $fiberFullAmount;
        return $this;
    }

    public function getFiberFullAmount()
    {
        return $this->fiberFullAmount;
    }
    
    public function setCarbohydrateFullAmount($carbohydrateFullAmount)
    {
        $this->carbohydrateFullAmount = $carbohydrateFullAmount;
        return $this;
    }

    public function setEnergyFullAmount($energyFullAmount)
    {
        $this->energyFullAmount = $energyFullAmount;
        return $this;
    }

    public function getEnergyFullAmount()
    {
        return $this->energyFullAmount;
    }

    public function setFatFullAmount($fatFullAmount)
    {
        $this->fatFullAmount = $fatFullAmount;
        return $this;
    }

    public function getFatFullAmount()
    {
        return $this->fatFullAmount;
    }
    
    public function getCarbohydrateFullAmount()
    {
        return $this->carbohydrateFullAmount;
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
 
    public function setSynx($synx)
    {
        $this->synx = $synx;

        return $this;
    }


    public function getSynx()
    {
        return $this->synx;
    }
    
    public function setMinPrice($min)
    {
        $this->minPrice = $min;
        return $this;
    }


    public function getMinPrice()
    {
        return $this->minPrice;
    }    
 
     public function getDiscount()
    {
        return $this->discount;
    }   
    
    public function setDiscount($val)
    {
        $this->discount = $val;
        return $this;
    }
    
    
    public function setDiscountValue($val)
    {
        $this->discountValue += $val;
        return $this;
    }

    public function getDiscountValue()
    {
        return $this->discountValue;
    }
    
    
    public function setMaxBasketCount($count)
    {
        $this->maxBasketCount = $count;

        return $this;
    }

    public function getMaxbasketCount()
    {
        return $this->maxBasketCount;
    }
    
    public function setData($data){
       
       $this->nuber_id =            $data->id;
       $this->_id =                 $data->_id;
       $this->alias =               ($this->alias == '')?$this->translit($data->name):$this->alias;
       $this->additionalInfo =      $data->additional_info;
       $this->code =                $data->code;
       $this->description =         $data->description;
       $this->name =                $data->name;
       //$this->seoDescription =      $data->seo_description;
       //$this->seoKeywords =         $data->seo_keywords;
       //$this->seoText =             $data->seo_text;
       //$this->seoTitle =            $data->seo_title;
       $this->tags =                $data->tags;
       $this->isIiko =             $data->is_iiko;
       $this->carbohydrateAmount =  $data->carbohydrate_amount;
       $this->differentPricesOn =   (count($data->different_prices_on) > 0)?"yes":"no";
       $this->doNotPrintInCheque =  $data->doNotPrintInCheque;
       $this->energyAmount =        $data->energyAmount;
       $this->fatAmount =           $data->fatAmount;
       $this->fiberAmount =         $data->fiberAmount;
       $this->measureUnit =         $data->measureUnit;
       $this->price =               $data->price;
       // $this->productCategoryId =   $data["productCategoryId"];
       //$this->prohibitedToSaleOn =  $data["prohibitedToSaleOn"];
       $this->type =                $data->type;
       $this->weight =              $data->weight;
       $this->isIncludedInMenu =    $data->isIncludedInMenu;
       $this->order_by =            $data->order_by;
       
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
        return mb_strtolower(strtr($str, $alphavit));
    }
    
    public function setRemainderItem($remainderItem)
    {
        $this->remainderItem = $remainderItem;

        return $this;
    }

    public function getRemainderItem()
    {
        return $this->remainderItem;
    } 
    
    public function setTimeUntilTheEnd($timeUntilTheEnd)
    {
        $this->timeUntilTheEnd = $timeUntilTheEnd;

        return $this;
    }

    public function getTimeUntilTheEnd()
    {
        $time = ($this->timeUntilTheEnd > 3600)?gmdate("H:i:s", $this->timeUntilTheEnd):gmdate("i:s", $this->timeUntilTheEnd);
        return $time;
    }

    public function setHref($href)
    {
        $this->href = $href;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    } 
    
    
    public function setIsPriceDiscount($isPriceDiscount)
    {
        $this->isPriceDiscount = $isPriceDiscount;

        return $this;
    }

    public function getIsPriceDiscount()
    {
        return $this->isPriceDiscount;
    } 
    
    
    
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
   /*   
   public function setData($data){
       
       $this->additionalInfo =      $data["additionalInfo"];
       $this->code =                $data["code"];
       $this->description =         $data["description"];
       $this->isDeleted =           $data["isDeleted"];
       $this->name =                $data["name"];
       $this->seoDescription =      $data["seoDescription"];
       $this->seoKeywords =         $data["seoKeywords"];
       $this->seoText =             $data["seoText"];
       $this->seoTitle =            $data["seoTitle"];
       $this->tags =                $data["tags"];
       $this->carbohydrateAmount =  $data["carbohydrateAmount"];
       $this->differentPricesOn =   (count($data["differentPricesOn"]) > 0)?"yes":"no";
       $this->doNotPrintInCheque =  $data["doNotPrintInCheque"];
       $this->energyAmount =        $data["energyAmount"];
       $this->fatAmount =           $data["fatAmount"];
       $this->fiberAmount =         $data["fiberAmount"];
       $this->groupId =             $data["groupId"];
       $this->measureUnit =         $data["measureUnit"];
       $this->price =               $data["price"];
      // $this->productCategoryId =   $data["productCategoryId"];
       //$this->prohibitedToSaleOn =  $data["prohibitedToSaleOn"];
       $this->type =                $data["type"];
       $this->weight =              $data["weight"];
       $this->isIncludedInMenu =    $data["isIncludedInMenu"];
       $this->order_by =            $data["order"];
       
   }   */
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
        $this->discount = new \Doctrine\Common\Collections\ArrayCollection();
        $this->trueDiscount = new \Doctrine\Common\Collections\ArrayCollection();
		$this->groupModifiers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Product
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
     * Set additionalInfo
     *
     * @param string $additionalInfo
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Product
     */

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
     */
    public function setSeoText($seoText)
    {
        $this->seoText = $seoText;

        return $this;
    }


    public function getSeoText()
    {
        return $this->seoText;
    }


    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }


    public function getSeoTitle()
    {
        return $this->seoTitle;
    }


    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }


    public function getTags()
    {
        return $this->tags;
    }


    public function setCarbohydrateAmount($carbohydrateAmount)
    {
        $this->carbohydrateAmount = $carbohydrateAmount;

        return $this;
    }


    public function getCarbohydrateAmount()
    {
        return $this->carbohydrateAmount;
    }

    public function setDifferentPricesOn($differentPricesOn)
    {
        $this->differentPricesOn = $differentPricesOn;

        return $this;
    }


    public function getDifferentPricesOn()
    {
        return $this->differentPricesOn;
    }

 
    public function setDoNotPrintInCheque($doNotPrintInCheque)
    {
        $this->doNotPrintInCheque = $doNotPrintInCheque;

        return $this;
    }


    public function getDoNotPrintInCheque()
    {
        return $this->doNotPrintInCheque;
    }


    public function setEnergyAmount($energyAmount)
    {
        $this->energyAmount = $energyAmount;

        return $this;
    }


    public function getEnergyAmount()
    {
        return $this->energyAmount;
    }


    public function setFatAmount($fatAmount)
    {
        $this->fatAmount = $fatAmount;

        return $this;
    }


    public function getFatAmount()
    {
        return $this->fatAmount;
    }


    public function setFiberAmount($fiberAmount)
    {
        $this->fiberAmount = $fiberAmount;

        return $this;
    }


    public function getFiberAmount()
    {
        return $this->fiberAmount;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }


    public function getGroupId()
    {
        return $this->groupId;
    }

	public function getGroupModifiers()
    {
        return $this->groupModifiers;
    }
    
    public function addGroupModifier(\Main\CatalogBundle\Entity\ProductGroupModifier $groupModifier)
    {
        $this->groupModifiers[] = $groupModifier;

        return $this;
    }

    public function removeGroupModifier(\Main\CatalogBundle\Entity\ProductGroupModifier $groupModifier)
    {
        $this->groupModifiers->removeElement($groupModifier);
    }

    public function setMeasureUnit($measureUnit)
    {
        $this->measureUnit = $measureUnit;

        return $this;
    }

    public function getMeasureUnit()
    {
        return $this->measureUnit;
    }

    
        /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPriceWithOutDiscount($price)
    {
        $this->priceWithOutDiscount= $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPriceWithOutDiscount()
    {
        return $this->priceWithOutDiscount;
    }
    
    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }
    
    protected $alreadyDisc; 
  

    
    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
       
        if(count($this->getTrueDiscount()) > 0){
         
            $discount = $this->getTrueDiscount();
            foreach($discount as $d){
                $list = $this->getAlreadyDisc();
                $next = true;
                if($list){
                    foreach($list as $l){
                        if($l == $d){
                            $next = false;
                        }
                    }
                }
                if($next){ 
                    $curDisc = $d->getPercent() ;
                    $this->price = $this->price - round($this->price*($curDisc/100),0);   
                    $this->addAlreadyDisc($d); 
                }
            }
        }
       
        /* if($this->discountValue > 0){
            return $this->price - round($this->price*($this->discountValue/100),0);    
        }      */
        
        return $this->price;
    }

    /**
     * Set productCategoryId
     *
     * @param string $productCategoryId
     *
     * @return Product
     */
    public function setProductCategoryId($productCategoryId)
    {
        $this->productCategoryId = $productCategoryId;

        return $this;
    }

    /**
     * Get productCategoryId
     *
     * @return string
     */
    public function getProductCategoryId()
    {
        return $this->productCategoryId;
    }

    /**
     * Set prohibitedToSaleOn
     *
     * @param string $prohibitedToSaleOn
     *
     * @return Product
     */
    public function setProhibitedToSaleOn($prohibitedToSaleOn)
    {
        $this->prohibitedToSaleOn = $prohibitedToSaleOn;

        return $this;
    }

    /**
     * Get prohibitedToSaleOn
     *
     * @return string
     */
    public function getProhibitedToSaleOn()
    {
        return $this->prohibitedToSaleOn;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Product
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set weight
     *
     * @param float $weight
     *
     * @return Product
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set isIncludedInMenu
     *
     * @param boolean $isIncludedInMenu
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set modifiers
     *
     * @param \Main\NuberBundle\Entity\ProductModifier $modifiers
     *
     * @return Product
     */
    public function setModifiers(\Main\CatalogBundle\Entity\ProductModifier $modifiers = null)
    {
        $this->modifiers = $modifiers;

        return $this;
    }

    /**
     * Get modifiers
     *
     * @return \Main\NuberBundle\Entity\ProductModifier
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * Add image
     *
     * @param \Main\NuberBundle\Entity\ProductImage $image
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * @return Product
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

    public function setDiscountOrder($discountOrder)
    {
        $this->discountOrder = $discountOrder;

        return $this;
    }


    public function getDiscountOrder()
    {
        return $this->discountOrder;
    }
    
    
    public function addTrueDiscount(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->trueDiscount[] = $discount;

        return $this;
    }

    /**
     * Remove discount
     *
     * @param \Main\CatalogBundle\Entity\ProductDiscount $discount
     */
    public function removeTrueDiscount(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->trueDiscount->removeElement($discount);
    }

    /**
     * Get discount
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTrueDiscount()
    {
        return $this->trueDiscount;
    }
 
 
    public function addAlreadyDisc(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->alreadyDisc[] = $discount;

        return $this;
    }

    public function removeAlreadyDisc(\Main\CatalogBundle\Entity\ProductDiscount $discount)
    {
        $this->alreadyDisc->removeElement($discount);
    }

    public function getAlreadyDisc()
    {
        return $this->alreadyDisc;
    }   
}
