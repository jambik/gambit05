<?php
namespace Main\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_product_by_page")
 * @ORM\HasLifecycleCallbacks()
 */
 
class ProductByPage {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;
    
    /** @ORM\Column(type="integer") */
    protected $nuber_id;
    
    /**
     *  @ORM\ManyToOne(targetEntity="Main\PageBundle\Entity\Page", inversedBy="productRecommended")
     *  @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     */
    
    protected $page;
    
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
    */
    protected $where;
    
    
    /**
     *  @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\Product", inversedBy="page")
     *  @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true)
     */
    protected $product;
    
    /**
     *  @ORM\ManyToOne(targetEntity="Main\CatalogBundle\Entity\ProductGroup", inversedBy="page")
     *  @ORM\JoinColumn(name="product_group_id", referencedColumnName="id", nullable=true)
     */
    
    protected $group;
    
    /** @ORM\Column(type="integer") */
    protected $func;
    
     /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    
    

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
     * Set nuberId
     *
     * @param integer $nuberId
     *
     * @return ProductByPage
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
     * Set func
     *
     * @param integer $func
     *
     * @return ProductByPage
     */
    public function setFunc($func)
    {
        $this->func = $func;

        return $this;
    }

    /**
     * Get func
     *
     * @return integer
     */
    public function getFunc()
    {
        return $this->func;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ProductByPage
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * Set page
     *
     * @param \Main\PageBundle\Entity\Page $page
     *
     * @return ProductByPage
     */
    public function setPage(\Main\PageBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Main\PageBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set product
     *
     * @param \Main\CatalogBundle\Entity\Product $product
     *
     * @return ProductByPage
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
     * Set group
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $group
     *
     * @return ProductByPage
     */
    public function setGroup(\Main\CatalogBundle\Entity\ProductGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Main\CatalogBundle\Entity\ProductGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set where
     *
     * @param string $where
     *
     * @return ProductByPage
     */
    public function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * Get where
     *
     * @return string
     */
    public function getWhere()
    {
        return $this->where;
    }
}
