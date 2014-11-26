<?php

/**
 * lied actions.
 *
 * @package    adonai
 * @subpackage lied
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class liedActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    //we don't do anything else here, because it shall just display the emtpy text box.
  }

  public function executeShow(sfWebRequest $request)
  {
  	//there are to possibilities to search for a lied
  	//1. normal id
  	//2. by bookNr searches for the song based on the songbook in the session
  	$idParameter = $request->getParameter('id');
  	$bookNrParameter = $request->getParameter('bookNr');
  	
    if($bookNrParameter != null){
  		$liederbuchId = $this->getUser()->getAttribute("liederbuchId");
  		if($liederbuchId == null){
  			$this->redirectToIndex($request);
  		}
  		
  		$idParameter = Doctrine_Core::getTable('Lied')
  			->getLied($bookNrParameter, $liederbuchId);
  	}
  	
  	if($idParameter != null){
  		$this->lied = Doctrine_Core::getTable('Lied')
	      ->createQuery('a')
	      ->innerJoin("a.Liedtext s")
	      ->where("a.id = ?", $idParameter)
	      ->orderBy("s.Reihenfolge ASC")
	      ->execute();
  	}
  	
  	if(!$this->lied){
  		$this->redirectToIndex($request);
  	}
  	
  	if($this->lied->count() < 1){
  		$this->redirectToIndex($request);
  	}
  	
    $this->lied = $this->lied->get(0);
  }
  
  private function redirectToIndex(sfWebRequest $request){
  	$secondView = $request->getParameter("secondView");
  	if($secondView == true){
  		$this->redirect('lied/index?secondView=gugus');
  	}else{
  		$this->redirect('lied/index?secondView=false');
  	}
  }
}
