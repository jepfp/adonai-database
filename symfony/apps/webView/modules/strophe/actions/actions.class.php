<?php

/**
 * strophe actions.
 *
 * @package    adonai
 * @subpackage strophe
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class stropheActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->liedtexts = Doctrine_Core::getTable('Liedtext')
      ->createQuery('a')
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
  	//Three different parameters can appear
  	//1. id --> normal case
  	//2. lied --> the first "strophe" shall be found for a given lied id
  	//3. bookNr --> the first "strophe" shall be found for a given book nr based
  	//              on the book in the session
  	
  	$liedParameter = $request->getParameter('lied');
  	$bookNrParameter = $request->getParameter('bookNr');
  	
  	if($bookNrParameter != null){
  		$liederbuchId = $this->getUser()->getAttribute("liederbuchId");
  		if($liederbuchId == null){
  			$this->redirect('liederbuch/index');
  		}
  		
  		$liedParameter = Doctrine_Core::getTable('Lied')
  			->getLied($bookNrParameter, $liederbuchId);
  	}
  	
  	if($liedParameter != null){
  		$lied = Doctrine_Core::getTable('Lied')
	      ->createQuery('a')
	      ->innerJoin('a.Liedtext s')
	      ->where("a.id = ?", $liedParameter)
	      ->orderBy("s.reihenfolge ASC")
	      ->execute();
	    
  		if($lied->count() < 1){
	  		$this->forward404();
	  	}
	      
	    $this->liedtext = $lied->get(0)->getLiedtext()->get(0);
	    $this->forward404Unless($this->liedtext);
  	}else{
	    $this->liedtext = Doctrine_Core::getTable('Liedtext')->find(array($request->getParameter('id')));
	    $this->forward404Unless($this->liedtext);
  	}
  }
}
