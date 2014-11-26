<?php

/**
 * liederbuch actions.
 *
 * @package    adonai
 * @subpackage liederbuch
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class liederbuchActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->liederbuchs = Doctrine_Core::getTable('Liederbuch')
      ->createQuery('a')
      ->execute();
  }
  
  public function executeSelect(sfWebRequest $request){
  	$this->liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($request->getParameter('id')));
  	
    $this->forward404Unless($this->liederbuch);
    
    //save in session
    $this->getUser()->setAttribute('liederbuchId', $this->liederbuch->getId());
  	$this->getUser()->setAttribute('liederbuchBuchname', $this->liederbuch->getBuchname());
  }
}
