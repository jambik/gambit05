<?php
namespace Main\PageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="client_page_property")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Main\PageBundle\Entity\Repository\PagePropertyRepository")
 */
 
class PageProperty {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Main\PageBundle\Entity\Page", mappedBy="property")
     */    
    protected $page;
    
     /** @ORM\Column(type="integer") */
    protected $type;
    
     /**
     * @ORM\Column(type="string", length=150)
    */   
    protected $name;
    
    /**
     * @ORM\Column(type="string", length=150)
    */    
    protected $value; 
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->page = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param integer $type
     *
     * @return PageProperty
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PageProperty
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
     * Set value
     *
     * @param string $value
     *
     * @return PageProperty
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add page
     *
     * @param \Main\PageBundle\Entity\Page $page
     *
     * @return PageProperty
     */
    public function addPage(\Main\PageBundle\Entity\Page $page)
    {
        $this->page[] = $page;

        return $this;
    }

    /**
     * Remove page
     *
     * @param \Main\PageBundle\Entity\Page $page
     */
    public function removePage(\Main\PageBundle\Entity\Page $page)
    {
        $this->page->removeElement($page);
    }

    /**
     * Get page
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPage()
    {
        return $this->page;
    }
}
