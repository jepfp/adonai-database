<?php

/**
 * versioncontrol actions.
 *
 * @package    adonai
 * @subpackage versioncontrol
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class versioncontrolActions extends sfActions
{
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request)
	{
		$this->newestLied = $this->getNewest();
	}
	
	public function executePlain(sfWebRequest $request)
	{
		$this->newestLied = $this->getNewest();
		
		return $this->renderText($this->newestLied["updated_at"]);
	}
	
	private function getNewest(){
		$q = Doctrine_Core::getTable('Lied')
		->createQuery('l')
		->where('l.updated_at = (SELECT MAX(updated_at) FROM Lied)');
		return $q->execute()->getFirst();
	}
}
