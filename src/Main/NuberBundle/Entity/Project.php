<?php
namespace Main\NuberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="client_nuber_project")
 * @ORM\HasLifecycleCallbacks()
 */

class Project 
{
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
     * @ORM\OneToMany(targetEntity="Main\NuberBundle\Entity\User", mappedBy="project")
    */    
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=true)
    */    
    protected $NuberSecret;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString(){
        return "project";
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
     * @return Project
     */
    public function setNuberId($nuberId)
    {
        $this->nuber_id = $nuberId;

        return $this;
    }

    public function getNuberId()
    {
        return $this->nuber_id;
    }
    
    public function setNuberSecret($nuber_secret)
    {
        $this->NuberSecret = $nuber_secret;

        return $this;
    }

    public function getNuberSecret()
    {
        return $this->NuberSecret;
    }
    /**
     * Add user
     *
     * @param \Main\NuberBundle\Entity\User $user
     *
     * @return Project
     */
    public function addUser(\Main\NuberBundle\Entity\User $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \Main\NuberBundle\Entity\User $user
     */
    public function removeUser(\Main\NuberBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }
}
