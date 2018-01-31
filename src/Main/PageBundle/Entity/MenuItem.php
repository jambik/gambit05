<?php
namespace Main\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_menu_item")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\PageBundle\Entity\Repository\MenuItemRepository") 
 */
 
class MenuItem {
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */     
    protected $id;
    
    /** @ORM\Column(type="integer") */
    protected $nuber_id;
    
    /**
     * @ORM\Column(type="string", length=255)
    */
    protected $title;
    
    /** @ORM\Column(type="integer") 
    * 
    *   0 - page
    *   1 - product
    *   2 - group
    *   3 - link
    *   4 - контейнер
    */
    protected $item_type = 0;
    
    /**
     * @ORM\ManyToOne(targetEntity="Main\PageBundle\Entity\MenuItem")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;
    
    /** @ORM\Column(type="integer",nullable=true) */
    protected $link_id;

    /**
     * @ORM\Column(type="string", length=255)
    */
    protected $link;
    
    /**
     * @ORM\ManyToOne(targetEntity="Main\PageBundle\Entity\Menu", inversedBy="item")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    protected $menu;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
    
    
    /** @ORM\Column(type="integer") */
    protected $order_by = 0;
    

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
     * @return MenuItem
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
     * Set title
     *
     * @param string $title
     *
     * @return MenuItem
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
     * Set itemType
     *
     * @param integer $itemType
     *
     * @return MenuItem
     */
    public function setItemType($itemType)
    {
        $this->item_type = $itemType;

        return $this;
    }

    /**
     * Get itemType
     *
     * @return integer
     */
    public function getItemType()
    {
        return $this->item_type;
    }

    /**
     * Set linkId
     *
     * @param integer $linkId
     *
     * @return MenuItem
     */
    public function setLinkId($linkId)
    {
        $this->link_id = $linkId;

        return $this;
    }

    /**
     * Get linkId
     *
     * @return integer
     */
    public function getLinkId()
    {
        return $this->link_id;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return MenuItem
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MenuItem
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
     * Set orderBy
     *
     * @param integer $orderBy
     *
     * @return MenuItem
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
     * Set parent
     *
     * @param \Main\PageBundle\Entity\MenuItem $parent
     *
     * @return MenuItem
     */
    public function setParent(\Main\PageBundle\Entity\MenuItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Main\PageBundle\Entity\MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set menu
     *
     * @param \Main\PageBundle\Entity\Menu $menu
     *
     * @return MenuItem
     */
    public function setMenu(\Main\PageBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \Main\PageBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
