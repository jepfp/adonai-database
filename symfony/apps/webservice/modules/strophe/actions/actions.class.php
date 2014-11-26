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
		$this->liedtext = Doctrine_Core::getTable('Liedtext')->find(array($request->getParameter('id')));
		$this->forward404Unless($this->liedtext);
	}

	public function executeNew(sfWebRequest $request)
	{
		$this->form = new LiedtextForm();
	}

	public function executeCreate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST));

		$this->form = new LiedtextForm();

		$this->processForm($request, $this->form);
		
		$this->setTemplate('new');
	}

	public function executeEdit(sfWebRequest $request)
	{
		$this->forward404Unless($liedtext = Doctrine_Core::getTable('Liedtext')->find(array($request->getParameter('id'))), sprintf('Object liedtext does not exist (%s).', $request->getParameter('id')));
		$this->form = new LiedtextForm($liedtext);
	}

	public function executeUpdate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
		$this->forward404Unless($liedtext = Doctrine_Core::getTable('Liedtext')->find(array($request->getParameter('id'))), sprintf('Object liedtext does not exist (%s).', $request->getParameter('id')));
		$this->form = new LiedtextForm($liedtext);

		$this->processForm($request, $this->form);

		$this->setTemplate('edit');
	}

	public function executeDelete(sfWebRequest $request)
	{
		$request->checkCSRFProtection();

		$this->forward404Unless($liedtext = Doctrine_Core::getTable('Liedtext')->find(array($request->getParameter('id'))), sprintf('Object liedtext does not exist (%s).', $request->getParameter('id')));
		$this->updateSongUpdatedAtField($liedtext->getLied());
		
		$dbLogger = Logger::getLogger("dbLogger");
		$valuesBefore = $this->getLogInfoFor($liedtext);
		$messageToLog = "strophe->delete strophe from song '" . $liedtext->getLied()->getTitel() . "'; id=" . $liedtext->getLiedId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")";
		$messageToLog .= "\nValues:\n" . $valuesBefore;
		
		$liedtext->delete();
		
		$dbLogger->info($messageToLog);

		$this->setTemplate('delete');
	}

	protected function processForm(sfWebRequest $request, sfForm $form)
	{
		
		$valuesBefore = "{}";
		$actionString = "created";
		if(!$form->getObject()->isNew()){
			$actionString = "updated";
			$valuesBefore = $this->getLogInfoFor($form->getObject());
		}
		
		$json_form = json_decode($request->getContent(), true);
		$valuesToSave = array(
				"strophe" => $json_form["verse"],
				//Ext sends 0 if nothing is assigned but we need "null"
				"refrain_id" => $json_form["refrainId"] == 0 ? null : $json_form["refrainId"],
				//"rubrik_id" => $json_form["categoryId"]
				"lied_id" => $json_form["songId"],
				//get the not edited values just to make sure they don't become empty.
				"ueberschrift" => $form["ueberschrift"]->getValue(),
				"ueberschrifttyp" => $form["ueberschrifttyp"]->getValue(),
				"reihenfolge" => $this->normalizeOrder($form["reihenfolge"]->getValue(), $json_form["songId"]),
		);
			
		$form->bind($valuesToSave, $request->getFiles($form->getName()));
		if ($form->isValid())
		{
			$liedtext = $form->save();
				
			$this->updateSongUpdatedAtField($liedtext->getLied());
			
			$dbLogger = Logger::getLogger("dbLogger");
			$messageToLog = "strophe->processForm " . $actionString . " strophe from song '" . $liedtext->getLied()->getTitel() . "'; id=" . $liedtext->getLiedId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")\n";
			$messageToLog .= "\nOld values:\n" . $valuesBefore;
			$messageToLog .= "\nNew values:\n" . $this->getLogInfoFor($liedtext);
			$dbLogger->info($messageToLog);
			
			$request->setParameter("id", $liedtext->getId());

			$this->forward('strophe', 'show');
			//$this->redirect('strophe/show?id='.$liedtext->getId());
		}else{
			echo "das Form ist NICHT valid";
		}
	}
	
	private function getLogInfoFor(Liedtext $liedtext){
		$data = $liedtext->getShowArray();
	
		return json_encode($data, JSON_PRETTY_PRINT );
	}

	protected function updateSongUpdatedAtField(Lied $lied){
		$date = new DateTime();
		$lied->set("updated_at", $date->format('Y-m-d H:i:s'));
		$lied->save();
	}

	public function executeMoveUp(sfWebRequest $request)
	{
		$liedtextIdToMove = $request->getParameter('id');
		$this->moveLiedtext($liedtextIdToMove, true);
	}

	public function executeMoveDown(sfWebRequest $request)
	{
		$liedtextIdToMove = $request->getParameter('id');
		$this->moveLiedtext($liedtextIdToMove, false);
	}
	
	/**
	 * Changes the order of the given liedtext inside of the song.
	 * @param unknown_type $liedtextIdToMove Id of liedtext which needs to be moved
	 * @param unknown_type $up True to move the verse up, false to move it down.
	 */
	private function moveLiedtext($liedtextIdToMove, $up){
		$this->liedtext = Doctrine_Core::getTable('Liedtext')->find(array($liedtextIdToMove));
		$this->forward404Unless($this->liedtext);
		
		$otherLiedtexts = $this->getOrderedLiedtextsBySong($this->liedtext->getLiedId());
		
		$skipNextAssignment = false;
		for($i = 0; $i < $otherLiedtexts->count(); $i++){
			if(!$skipNextAssignment) $otherLiedtexts[$i]->setReihenfolge($i);
			$skipNextAssignment = false;
			if($otherLiedtexts[$i]->getId() == $liedtextIdToMove){
				if($up === true){
					if($i == 0) break;
					$otherLiedtexts[$i]->setReihenfolge($i - 1);
					$otherLiedtexts[$i - 1]->setReihenfolge($i);
					$otherLiedtexts[$i - 1]->save();
				}else{
					if($i == $otherLiedtexts->count() - 1) break;
					$otherLiedtexts[$i]->setReihenfolge($i + 1);
					$otherLiedtexts[$i + 1]->setReihenfolge($i);
					$skipNextAssignment = true;
				}
			}
			$otherLiedtexts[$i]->save();
		}
		
		$this->updateSongUpdatedAtField($this->liedtext->getLied());
		
		$otherLiedtexts = $this->getOrderedLiedtextsBySong($this->liedtext->getLiedId());
		$this->newOrder = array();
		foreach($otherLiedtexts as $l){
			$this->newOrder[] = $l->getId();
		}
	}
	
	private function getOrderedLiedtextsBySong($liedId){
		return Doctrine_Core::getTable('Liedtext')
		->createQuery('a')
		->addWhere("lied_id = ?", array($liedId))
		->addOrderBy('Reihenfolge ASC')
		->execute();
	}
	
	/**
	 * Takes the given $rank and returns it, if it is not null.
	 * If it is null, it determines the highest rank from this
	 * song and adds the highest rank + 1.
	 * @param unknown_type $rank
	 * @param unknown_type $songId Id of the assigned song.
	 */
	private function normalizeOrder($rank, $songId){
		if($rank == null){
			$lastLiedtext = Doctrine_Core::getTable('Liedtext')
			->createQuery('a')
			->addWhere("lied_id = ?", array($songId))
			->addOrderBy('Reihenfolge DESC')
			->execute();
			
			if($lastLiedtext->count() < 1) return 1;
			
			return $lastLiedtext[0]->getReihenfolge() + 1;
		}
		return $rank;
	}
}
