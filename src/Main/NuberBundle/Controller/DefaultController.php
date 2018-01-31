<?php

/* Nuber Updater Script v. 1.0.1 */

namespace Main\NuberBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;         
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
 
use Main\NuberBundle\Entity\transitBase;
use Main\CatalogBundle\Entity\ProductGroup;
use Main\CatalogBundle\Entity\Product;
use Main\CatalogBundle\Entity\ProductImage;
use Main\CatalogBundle\Entity\ProductModifier;
use Main\CatalogBundle\Entity\ProductGroupModifier;

class DefaultController extends Controller
{
	private $path = '/var/www/gambit/web/';
	
    public function indexAction()
    {
        return $this->render('MainNuberBundle:Default:index.html.twig');
    }
	
    public function checkTroubleAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        
		$orderError = $this->checkOrderTrouble($em);
		$freeSpace = $this->getFreeSpace();
		
        $response = new Response(json_encode(array('order'=>$orderError, 'freeSpace'=>$freeSpace)));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;        
    }
	protected function getFreeSpace(){
		$ds = disk_total_space("/");
		$df = disk_free_space("/");
		
		return $df*100/$ds;
	}
	
	protected function checkOrderTrouble($em){
		$errorOrder = $em->getRepository('MainCatalogBundle:Order')->findBy(array('sendError'=>true));
        $err = 0;
		if($errorOrder){
			foreach($errorOrder as $sE){
				$fromDate =  new \DateTime('-22 seconds'); 
				if($sE->getCreatedAt() < $fromDate) {
					$err += 1;
				}
			} 
		}
		return $err;
	}
    
	public function BaseCheckAction(Request $request){
		
		$um = $this->container->get('update_manager');

		$result = $um->checkBase();
		$result = ($result == "[true]")?1:0;
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;  	
	}
	
	private function setTransitData($sTransit){ 
		$em = $this->getDoctrine()->getManager();

		$trBase = $em->getRepository('MainNuberBundle:transitBase')->findOneBy(array('revision'=>$sTransit->revision));
		
		if(is_null($trBase)){
			$trBase = new transitBase();
		}
		$project = $em->getRepository('MainNuberBundle:Project')->findOneBy(array('nuber_id'=>$sTransit->project)); 
		
		$trBase->setRevision($sTransit->revision);
		$trBase->setUploadDate(new \DateTime($sTransit->uploadDate));
		$trBase->setSServerData((!is_null($sTransit->sServerDate))?new \DateTime($sTransit->sServerDate):null);
		$trBase->setSIikoData((!is_null($sTransit->sIikoDate))?new \DateTime($sTransit->sIikoDate):null);
		$trBase->setSClientData((!is_null($sTransit->sClientDate))?new \DateTime($sTransit->sClientDate):null);
		$trBase->setStatus($sTransit->status);
		//$trBase->setProject($project); 
		
        $em->persist($trBase);
        $em->flush();	

		
		return $project;
	}
	
	private function setProductGroup($sGroup, $project){
		$result = array();
		$em = $this->getDoctrine()->getManager();
		
		foreach($sGroup as $sG){
			$findGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array('_id'=>$sG->iikoId)); 
			if(isset($sG->isDeleted)){
				if($findGroup){
					$em->remove($findGroup);
					$em->flush();	
				}
			} else {
				if(!$findGroup){
					$findGroup = new ProductGroup(); 
				}
				$parentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array('_id'=>$sG->ParentIikoId));

				$findGroup->applyChanges($sG, $parentGroup, $project);	

				$em->persist($findGroup);
				$em->flush();	
			}
			$result[] = $sG->nuberId;  
		}
	
		return $result; 
	}
	
	private function killImage($img,$em){
		if($img->getUrlNuberUpload()){
			unlink($this->path.$img->getUrlNuberUpload());
		
			$em->remove($img);
			$em->flush();
		}
		return true; //запилить возврат фолс в случае ошибки и генерацию аларма
	}
	
	private function killModifiers($product, $em){ 
		$findModifier =  $product->getModifiers();
        $findGroupModifier = $product->getGroupModifiers();

        if($findModifier){
            foreach($findModifier as $fM){
               $em->remove($fM);
               $em->flush(); 
            }
	    }
        if($findGroupModifier){
            foreach($findGroupModifier as $fGM){
                $child = $fGM->getChildModifiers();
                if($child){
                    foreach($child as $ch){
                        $em->remove($ch);
                        $em->flush();                        
                    }    
                }
                $em->remove($fGM);
                $em->flush();
            }
        }
         
        return true; 
    }
	 
	private function setProduct($sProduct, $project){
		$result = $imgResult = $modResult = $modGroupResult = $modChildResult = array();
		$em = $this->getDoctrine()->getManager();
		//return $result; //block dev

		foreach($sProduct as $sP){
			$findProduct = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array('_id'=>$sP->iikoId)); 

			if(isset($sP->isDeleted)){
				if($findProduct){

					$findImage = $em->getRepository('MainCatalogBundle:ProductImage')->findBy(array('product'=>$findProduct));
					if($findImage){
						foreach($findImage as $fImg){
							$this->killImage($fImg, $em);
						}
					}
					$this->killModifiers($findProduct, $em);
					
					$em->remove($findProduct);
					$em->flush();	
				}
			} else {

				if(!$findProduct){
					$findProduct = new Product();
				
				}
                $parentGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array('_id'=>$sP->parentGroupIikoId));
                $findProduct->applyChanges($sP, $parentGroup, $project);    
                $em->persist($findProduct);
                $em->flush();   

                if(count($sP->image) > 0){
                    foreach($sP->image as $img){
                        $findImage = $em->getRepository('MainCatalogBundle:ProductImage')->findOneBy(array('_id'=>$img->iikoId));
                        if(isset($img->isDeleted)){
                            if($findImage){
                                $this->killImage($findImage, $em);
                            }
                        } else {
                            if(!$findImage){
                                $findImage = new ProductImage(); 
                            }
                            $findImage->applyChanges($img, $findProduct, $project);    
                            $em->persist($findImage);
                            $em->flush(); 
							
						//	$upm = $this->container->get('uploader_manager'); 
						//	$next = $upm->checkImg(); 
							
							//echo $next.'++++++++';							
                        }
                        $imgResult[] = $img->nuberId; 
                    }
                }
			 
				if(count($sP->modifiers) > 0){ 
					//var_dump($sP->modifiers);exit;
					foreach($sP->modifiers as $mod){
						$findMod = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array('nuber_id'=>$mod->nuberId));
					
						if(isset($mod->isDeleted)){
							if($findMod){
								$em->remove($findMod);
								$em->flush();		
							}
							$modResult[] = $mod->nuberId; 
						} else {
							$modifierProduct = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array('_id'=>$mod->modifierIikoId));
							if($modifierProduct){
								if(!$findMod){
									$findMod = new ProductModifier(); 
								}	
								$findMod->applyChanges($mod, $findProduct, $modifierProduct, NULL, $project);
								$em->persist($findMod);
								$em->flush(); 
								$modResult[] = $mod->nuberId; 
                            } else {
								//модификатор для продукта не найден, добавить запись невозможно. необходимо создать аларм
								//проверить и повесить алармы на все подобные места
							}
						}
					}
				}
				
				if(count($sP->groupModifiers) > 0){

					foreach($sP->groupModifiers as $gMod){ 
					$findGroupMod = $em->getRepository('MainCatalogBundle:ProductGroupModifier')->findOneBy(array('nuber_id'=>$gMod->nuberId)); 
						if(isset($gMod->isDeleted)){
							
							if($findGroupMod){ 
								$child = $findGroupMod->getChildModifiers();
								$modChildResult = array();
								if($child){
									foreach($child as $ch){
										$em->remove($ch);
										$em->flush();
										
										$modChildResult[] = $ch->getNuberId();
									}
								}
								$em->remove($findGroupMod);
								$em->flush();
								$modGroupResult[] = array('gMod'=>$findGroupMod->getNuberId(), 'child'=>$modChildResult);
							}
						} else { 
							$modifierProductGroup = $em->getRepository('MainCatalogBundle:ProductGroup')->findOneBy(array('_id'=>$gMod->groupId));
						
							$isNewModGroup = false;

							if(!$findGroupMod){

								$findGroupMod = new ProductGroupModifier();
								$isNewModGroup = true;
							}
							$findGroupMod->applyChanges($gMod, $project);
							$findGroupMod->setProductGroup($modifierProductGroup);
							
							$em->persist($findGroupMod);
							$em->flush(); 

							if(count($gMod->childModifiers) > 0){
								
								$modChildResult = array();
								foreach($gMod->childModifiers as $chMod){
									$findChildMod = $em->getRepository('MainCatalogBundle:ProductModifier')->findOneBy(array('nuber_id'=>$chMod->nuberId));
									if(isset($chMod->isDeleted)){
										if($findChildMod){
											$em->remove($findChildMod);
											$em->flush();
										}
									} else {
										$modifierProduct = $em->getRepository('MainCatalogBundle:Product')->findOneBy(array('_id'=>$chMod->modifierIikoId));
										if($modifierProduct){
											if(!$findChildMod){
												$findChildMod = new ProductModifier(); 
											}
											
											$findChildMod->applyChanges($chMod, $findProduct, $modifierProduct, $findGroupMod, $project);
											$em->persist($findChildMod);
											$em->flush();
										}
									}
									$modChildResult[] = $chMod->nuberId; 
								}
							}
							if($isNewModGroup){
								$findProduct->addGroupModifier($findGroupMod);
								$em->persist($findProduct);
								$em->flush();
							}
							$modGroupResult[] = array('id'=>$findGroupMod->getNuberId(), 'child'=>$modChildResult);
						}
					}
				}
				
            }
            $result[] = array('product' => $sP->nuberId, 'img' => $imgResult, 'mod' => $modResult, 'gMod' => $modGroupResult); 
		}
		return $result;
	}
    
    public function putAction(Request $request){
		
		$content = $request->getContent();
        $arr = json_decode($content);

		$sTransit = $arr->sTransit;
		$sGroup = $arr->sGroup;
		$sProduct = $arr->sProduct; 
		
		$project = $this->setTransitData($sTransit);

        $response = new Response(json_encode(array('group'=>$this->setProductGroup($sGroup, $project),'product'=>$this->setProduct($sProduct, $project))));
        $response->headers->set('Content-Type','application/json') ;   
            
        return $response;  
    }
}
