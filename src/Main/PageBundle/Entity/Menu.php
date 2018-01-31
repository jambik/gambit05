<?php
namespace Main\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_menu")
 * @ORM\HasLifecycleCallbacks()
 */
 
class Menu {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */     
    protected $id;
    
    /** @ORM\Column(type="integer") */
    protected $nuber_id;

    /**
     * @ORM\Column(type="string", length=150, nullable=false)
    */    
    protected $alias;

    /**
     * @ORM\OneToMany(targetEntity="Main\PageBundle\Entity\MenuItem", mappedBy="menu")
     */
    protected $item;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
   
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
    */    
    protected $typeId = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->item = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Menu
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
     * Set alias
     *
     * @param string $alias
     *
     * @return Menu
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getTypeId()
    {
        return $this->typeId;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Menu
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
     * Add item
     *
     * @param \Main\PageBundle\Entity\MenuItem $item
     *
     * @return Menu
     */
    public function addItem(\Main\PageBundle\Entity\MenuItem $item)
    {
        $this->item[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \Main\PageBundle\Entity\MenuItem $item
     */
    public function removeItem(\Main\PageBundle\Entity\MenuItem $item)
    {
        $this->item->removeElement($item);
    }

    /**
     * Get item
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItem()
    {
        return $this->item;
    }
}
