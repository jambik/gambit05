<?php
namespace Main\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_product_image")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\CatalogBundle\Entity\Repository\ImageRepository")
 */
 
class ProductImage {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;

    /**
      * @ORM\Column(type="integer", nullable=true)
    */
    protected $revision;
	
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
    
    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product", inversedBy="images",cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
    */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroup", inversedBy="images")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
    */    
    protected $group;
 
    /** @ORM\Column(type="boolean") */
    protected $isDeleted = false;
 
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */    
    protected $urlOriginal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
    */    
    protected $urlNuberUpload;
    
    /** @ORM\Column(type="datetime") */
    protected $uploadDate;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;

     public function applyChanges($img, $item){
        $this->setUrlOriginal($img->url);
        $this->setUploadDate(new \DateTime($img->uploadDate));
        $this->setIikoId($img->iikoId);
        $this->setNuberId($img->nuberId);
        $this->setRevision($img->revision);
        $this->setProduct($item);
        $this->setIsIiko(true);
    
        return true;    
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

    public function setIikoId($_id)
    {
        $this->_id = $_id;
        return $this;
    }
    
    public function getIikoId()
    {
        return $this->_id;
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
     * @return ProductImage
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
     * Set urlOriginal
     *
     * @param string $urlOriginal
     *
     * @return ProductImage
     */
    public function setUrlOriginal($urlOriginal)
    {
        $this->urlOriginal = $urlOriginal;

        return $this;
    }

    /**
     * Get urlOriginal
     *
     * @return string
     */
    public function getUrlOriginal()
    {
        return $this->urlOriginal;
    }
    
    public function getSrc(){
        if($this->urlNuberUpload !=""){
            return $this->urlNuberUpload;
        }elseif($this->urlOriginal !=""){
            return $this->urlOriginal;
        } else {
            return "/img/product/no-photo.png";   
        }
    }

    /**
     * Set urlNuberUpload
     *
     * @param string $urlNuberUpload
     *
     * @return ProductImage
     */
    public function setUrlNuberUpload($urlNuberUpload)
    {
        $this->urlNuberUpload = $urlNuberUpload;

        return $this;
    }

    /**
     * Get urlNuberUpload
     *
     * @return string
     */
    public function getUrlNuberUpload()
    {
        return $this->urlNuberUpload;
    }

    /**
     * Set uploadDate
     *
     * @param \DateTime $uploadDate
     *
     * @return ProductImage
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
     * Set product
     *
     * @param \Main\NuberBundle\Entity\Product $product
     *
     * @return ProductImage
     */
    public function setProduct(\Main\CatalogBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Main\NuberBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set group
     *
     * @param \Main\NuberBundle\Entity\ProductGroup $group
     *
     * @return ProductImage
     */
    public function setGroup(\Main\CatalogBundle\Entity\ProductGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Main\NuberBundle\Entity\ProductGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductImage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
