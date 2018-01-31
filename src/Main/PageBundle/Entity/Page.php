<?php
namespace Main\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_page", uniqueConstraints={@ORM\UniqueConstraint(name="alias_unique", columns={"alias", "parent_page_id"})})
 * @ORM\HasLifecycleCallbacks()
 */
 
class Page {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;
    
    /** @ORM\Column(type="integer") */
    protected $nuber_id;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\PageBundle\Entity\ProductByPage", mappedBy="page")
     */ 
    protected $productRecommended;
    
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
    */
    protected $alias;
    
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */
    protected $title;
    
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */
    protected $h1;
    
    /**
     * @ORM\Column(type="text",nullable=true)
    */
    protected $preview_text;
    
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */
    protected $preview_pic;
    
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */
    protected $img;

    /**
     * @ORM\Column(type="text",nullable=true,nullable=true)
    */      
    protected $content;

    /**
     * @ORM\Column(type="text",nullable=true)
    */    
    protected $head;
    
    /**
     * @ORM\OneToMany(targetEntity="Main\PageBundle\Entity\Page", mappedBy="parentPage")
     */
    protected $childrenPage;
     
    /**
     * @ORM\ManyToOne(targetEntity="Main\PageBundle\Entity\Page", inversedBy="childrenPage")
     * @ORM\JoinColumn(name="parent_page_id", referencedColumnName="id",nullable=true)
     */
    protected $parentPage;
    
    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */    
    protected $meta_keys;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */    
    protected $meta_description;
 
     /**
     * @ORM\Column(type="string", length=255,nullable=true)
    */   
    protected $template;
    
    
    /** @ORM\Column(type="integer") 0 - index page, 1 - other type, 2 - catalog page , 3 - detail product page, 4 - order complete , 5 - order send */
    protected $page_type  = 1;

    
    /**
     * @ORM\ManyToMany(targetEntity="Main\PageBundle\Entity\PageProperty", inversedBy="page")
     * @ORM\JoinTable(name="client_page_property_value")
     */
    protected $property;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->childrenPage = new \Doctrine\Common\Collections\ArrayCollection();
        $this->property = new \Doctrine\Common\Collections\ArrayCollection();
        $this->productRecommended = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function setMassPageData($data,$id){
        $this->nuber_id = $id;
        $this->alias = $data->alias;    
        $this->title = $data->title;    
        $this->h1 = $data->h1;    
        $this->preview_text = $data->preview_text;    
        $this->preview_pic = $data->preview_pic;    
        $this->img = $data->img;    
        $this->content = $data->content;    
        $this->head = $data->head;    
        $this->meta_keys = $data->meta_keys;    
        $this->meta_description = $data->meta_description;    
    }
    
    public function getPageType()
    {
        return $this->page_type;
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

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }
    
    
    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return Page
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set h1
     *
     * @param string $h1
     *
     * @return Page
     */
    public function setH1($h1)
    {
        $this->h1 = $h1;

        return $this;
    }

    /**
     * Get h1
     *
     * @return string
     */
    public function getH1()
    {
        return $this->h1;
    }

    /**
     * Set previewText
     *
     * @param string $previewText
     *
     * @return Page
     */
    public function setPreviewText($previewText)
    {
        $this->preview_text = $previewText;

        return $this;
    }

    /**
     * Get previewText
     *
     * @return string
     */
    public function getPreviewText()
    {
        return $this->preview_text;
    }

    /**
     * Set previewPic
     *
     * @param string $previewPic
     *
     * @return Page
     */
    public function setPreviewPic($previewPic)
    {
        $this->preview_pic = $previewPic;

        return $this;
    }

    /**
     * Get previewPic
     *
     * @return string
     */
    public function getPreviewPic()
    {
        return $this->preview_pic;
    }

    /**
     * Set img
     *
     * @param string $img
     *
     * @return Page
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get img
     *
     * @return string
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Page
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set head
     *
     * @param string $head
     *
     * @return Page
     */
    public function setHead($head)
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Get head
     *
     * @return string
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Set metaKeys
     *
     * @param string $metaKeys
     *
     * @return Page
     */
    public function setMetaKeys($metaKeys)
    {
        $this->meta_keys = $metaKeys;

        return $this;
    }

    /**
     * Get metaKeys
     *
     * @return string
     */
    public function getMetaKeys()
    {
        return $this->meta_keys;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return Page
     */
    public function setMetaDescription($metaDescription)
    {
        $this->meta_description = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    /**
     * Add childrenPage
     *
     * @param \Main\PageBundle\Entity\Page $childrenPage
     *
     * @return Page
     */
    public function addChildrenPage(\Main\PageBundle\Entity\Page $childrenPage)
    {
        $this->childrenPage[] = $childrenPage;

        return $this;
    }

    /**
     * Remove childrenPage
     *
     * @param \Main\PageBundle\Entity\Page $childrenPage
     */
    public function removeChildrenPage(\Main\PageBundle\Entity\Page $childrenPage)
    {
        $this->childrenPage->removeElement($childrenPage);
    }

    /**
     * Get childrenPage
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrenPage()
    {
        return $this->childrenPage;
    }

    /**
     * Set parentPage
     *
     * @param \Main\PageBundle\Entity\Page $parentPage
     *
     * @return Page
     */
    public function setParentPage(\Main\PageBundle\Entity\Page $parentPage = null)
    {
        $this->parentPage = $parentPage;

        return $this;
    }

    /**
     * Get parentPage
     *
     * @return \Main\PageBundle\Entity\Page
     */
    public function getParentPage()
    {
        return $this->parentPage;
    }

    /**
     * Add property
     *
     * @param \Main\PageBundle\Entity\PageProperty $property
     *
     * @return Page
     */
    public function addProperty(\Main\PageBundle\Entity\PageProperty $property)
    {
        $this->property[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \Main\PageBundle\Entity\PageProperty $property
     */
    public function removeProperty(\Main\PageBundle\Entity\PageProperty $property)
    {
        $this->property->removeElement($property);
    }

    /**
     * Get property
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set pageType
     *
     * @param integer $pageType
     *
     * @return Page
     */
    public function setPageType($pageType)
    {
        $this->page_type = $pageType;

        return $this;
    }

    /**
     * Add productRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $productRecommended
     *
     * @return Page
     */
    public function addProductRecommended(\Main\CatalogBundle\Entity\ProductGroup $productRecommended)
    {
        $this->productRecommended[] = $productRecommended;

        return $this;
    }

    /**
     * Remove productRecommended
     *
     * @param \Main\CatalogBundle\Entity\ProductGroup $productRecommended
     */
    public function removeProductRecommended(\Main\CatalogBundle\Entity\ProductGroup $productRecommended)
    {
        $this->productRecommended->removeElement($productRecommended);
    }

    /**
     * Get productRecommended
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductRecommended()
    {
        return $this->productRecommended;
    }
}
