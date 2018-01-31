<?php
namespace Main\NuberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="tbl_transit_update")
 * @ORM\HasLifecycleCallbacks()
 */
 
class transitBase {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
    */    
    protected $id;
    
    /** @ORM\Column(type="datetime") */
    protected $createdAt;
	
	/** @ORM\Column(type="integer") */
    protected $status = 0;
	
	/** @ORM\Column(type="integer",nullable=true) */  
	protected $revision;
	
	/** @ORM\Column(type="datetime") */
	protected $uploadDate;
	
	/** @ORM\Column(type="datetime", nullable=true) */
	protected $sServerData; // false если транзитная база НЕ синхронизирована с рабочей, дата указывает на время синхронизации
	
	/** @ORM\Column(type="datetime", nullable=true) */
	protected $sIikoData; // false если данные с айки еще не получены, дата указывает на время завершения сохранения данных из айки в транзитные таблицы
	
	/** @ORM\Column(type="datetime", nullable=true) */
	protected $sClientData; //false если изменения данной ревизии не синхронизированы с клиентской базой, дата указывает на синхронизацию с клиентской базой проекта
    	
	
	public function setStatus($status){
        $this->status = $status;
		return $this;
    }
    
	public function getStatus(){
        return $this->status;    
    }
	
	public function getId()
    {
        return $this->id;
    }
	
	public function setSClientData($d)
    {
        $this->sClientData = $d;
    }

    public function getSClientData()
    {
        return $this->sClientData;
    }

	public function setSIikoData($d)
    {
        $this->sIikoData = $d;
    }

    public function getSIikoData()
    {
        return $this->sIikoData;
    }
	
	public function setSServerData($d)
    {
        $this->sServerData = $d;
    }

    public function getSServerData()
    {
        return $this->sServerData;
    }
	
	public function setUploadDate($upDate)
    {
        $this->uploadDate = $upDate;
		return $this;
    }

    public function getUploadDate()
    {
        return $this->uploadDate;
    }
	
	/**
     * @ORM\PrePersist
    */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
	
    public function setRevision($rev)
    {
        $this->revision = $rev;
    }

    public function getRevision()
    {
        return $this->revision;
    }
	
}