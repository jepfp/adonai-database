<?php

/**
 * refrain actions.
 *
 * @package    adonai
 * @subpackage refrain
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class refrainActions extends sfActions
{
	public function executeIndex(sfWebRequest $request)
	{
		$query = Doctrine_Core::getTable('Refrain')
		->createQuery('a');

		//append any filters
		/*WARNING: SQL Injection possible here!*/
		$filters = json_decode($request->getParameter('filter'));
		if(isset($filters)){
			foreach($filters as $filter){
				$property = SQLite3::escapeString($filter->{"property"});
				$value = SQLite3::escapeString($filter->{"value"});
				$query->andWhere("$property = ?", array($value));
			}
		}

		//append any sorting
		/*WARNING: SQL Injection possible here!*/
		$sorters = json_decode($request->getParameter('sort'));
		if(isset($sorters)){
			foreach($sorters as $sort){
				$property = SQLite3::escapeString($sort->{"property"});
				$direction = SQLite3::escapeString($sort->{"direction"});
				$query->addOrderBy($property . " " . $direction);
			}
		}

		$this->refrains = $query
		->execute();
	}

	public function executeShow(sfWebRequest $request)
	{
		$this->refrain = Doctrine_Core::getTable('Refrain')->find(array($request->getParameter('id')));
		$this->forward404Unless($this->refrain);
	}

	public function executeNew(sfWebRequest $request)
	{
		$this->form = new RefrainForm();
	}

	public function executeCreate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST));

		$this->form = new RefrainForm();

		$this->processForm($request, $this->form);
		
		$this->setTemplate('new');
	}

	public function executeEdit(sfWebRequest $request)
	{
		$this->forward404Unless($refrain = Doctrine_Core::getTable('Refrain')->find(array($request->getParameter('id'))), sprintf('Object refrain does not exist (%s).', $request->getParameter('id')));
		$this->form = new RefrainForm($refrain);
	}

	public function executeUpdate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
		$this->forward404Unless($refrain = Doctrine_Core::getTable('Refrain')->find(array($request->getParameter('id'))), sprintf('Object refrain does not exist (%s).', $request->getParameter('id')));
		$this->form = new RefrainForm($refrain);

		$this->processForm($request, $this->form);
		
		$this->setTemplate('edit');
	}

	public function executeDelete(sfWebRequest $request)
	{
		$request->checkCSRFProtection();

		$this->forward404Unless($refrain = Doctrine_Core::getTable('Refrain')->find(array($request->getParameter('id'))), sprintf('Object refrain does not exist (%s).', $request->getParameter('id')));

		//check, if any verses refer to this refrain
		//if so ==> abort!
		if($this->doVerseReferToRefrain($refrain)){
			//maybe this can be improved. For now it's just a standard 404 error message.
			$this->forward404();
		}else{
			$this->updateSongUpdatedAtField($refrain->getLied());
			
			$dbLogger = Logger::getLogger("dbLogger");
			$valuesBefore = $this->getLogInfoFor($refrain);
			$messageToLog = "refrain->delete refrain from song '" . $refrain->getLied()->getTitel() . "'; id=" . $refrain->getLiedId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")";
			$messageToLog .= "\nValues:\n" . $valuesBefore;
			
			$refrain->delete();
			
			$dbLogger->info($messageToLog);

			$this->redirect('refrain/index');
		}
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
				"refrain" => $json_form["refrain"],
				"lied_id" => $json_form["songId"],
				//get the not edited values just to make sure they don't become empty.
				"refrainnr" => $this->normalizeOrder($form["refrainnr"]->getValue(), $json_form["songId"]),
		);
			
		$form->bind($valuesToSave, $request->getFiles($form->getName()));
		if ($form->isValid())
		{
			$liedtext = $form->save();
		
			$this->updateSongUpdatedAtField($liedtext->getLied());
			
			$dbLogger = Logger::getLogger("dbLogger");
			$messageToLog = "refrain->processForm " . $actionString . " refrain from song '" . $liedtext->getLied()->getTitel() . "'; id=" . $liedtext->getLiedId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")\n";
			$messageToLog .= "\nOld values:\n" . $valuesBefore;
			$messageToLog .= "\nNew values:\n" . $this->getLogInfoFor($liedtext);
			$dbLogger->info($messageToLog);
			
			$request->setParameter("id", $liedtext->getId());
		
			$this->forward('refrain', 'show');
			//$this->redirect('refrain/show?id='.$liedtext->getId());
		}else{
			echo "das Form ist NICHT valid";
		}
	}
	
	private function getLogInfoFor(Refrain $refrain){
		$data = $refrain->getShowArray();
	
		return json_encode($data, JSON_PRETTY_PRINT );
	}

	public function executeMoveUp(sfWebRequest $request)
	{
		$idToMove = $request->getParameter('id');
		$this->moveRefrain($idToMove, true);
	}

	public function executeMoveDown(sfWebRequest $request)
	{
		$idToMove = $request->getParameter('id');
		$this->moveRefrain($idToMove, false);
	}

	/**
	 * Changes the order of the given refrain inside of the song.
	 * @param unknown_type $idToMove Id of refrain which needs to be moved
	 * @param unknown_type $up True to move the refrain up, false to move it down.
	 */
	private function moveRefrain($idToMove, $up){
		$this->refrain = Doctrine_Core::getTable('Refrain')->find(array($idToMove));
		$this->forward404Unless($this->refrain);

		$otherRefrains = $this->getOrderedRefrainsBySong($this->refrain->getLiedId());

		$skipNextAssignment = false;
		for($i = 0; $i < $otherRefrains->count(); $i++){
			if(!$skipNextAssignment) $otherRefrains[$i]->setRefrainNr($i);
			$skipNextAssignment = false;
			if($otherRefrains[$i]->getId() == $idToMove){
				if($up === true){
					if($i == 0) break;
					$otherRefrains[$i]->setRefrainNr($i - 1);
					$otherRefrains[$i - 1]->setRefrainNr($i);
					$otherRefrains[$i - 1]->save();
				}else{
					if($i == $otherRefrains->count() - 1) break;
					$otherRefrains[$i]->setRefrainNr($i + 1);
					$otherRefrains[$i + 1]->setRefrainNr($i);
					$skipNextAssignment = true;
				}
			}
			$otherRefrains[$i]->save();
		}

		$this->updateSongUpdatedAtField($this->refrain->getLied());

		$otherRefrains = $this->getOrderedRefrainsBySong($this->refrain->getLiedId());
		$this->newOrder = array();
		foreach($otherRefrains as $l){
			$this->newOrder[] = $l->getId();
		}
	}

	private function getOrderedRefrainsBySong($liedId){
		return Doctrine_Core::getTable('Refrain')
		->createQuery('a')
		->addWhere("lied_id = ?", array($liedId))
		->addOrderBy('RefrainNr ASC')
		->execute();
	}

	protected function updateSongUpdatedAtField(Lied $lied){
		$date = new DateTime();
		$lied->set("updated_at", $date->format('Y-m-d H:i:s'));
		$lied->save();
	}

	/**
	 * Checks if there are any verses which refer to the given refrain.
	 * If so, this function returns true, false otherwise.
	 * @param Refrain $refrain
	 */
	protected function doVerseReferToRefrain(Refrain $refrain){
		$verses = $refrain->getLied()->getLiedtext();

		foreach($verses as $verse){
			if($verse->getRefrainId() == $refrain->getId()){
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Takes the given $rank and returns it, if it is not null.
	 * If it is null, it determines the highest rank from this
	 * song and adds the highest rank + 1.
	 * @param unknown_type $rank
	 * @param unknown_type $songId Id of the assigned song.
	 */
	private function normalizeOrder($rank, $songId){
		$this->logMessage('help me!', 'info');
		if($rank == null){
			$lastLiedtext = Doctrine_Core::getTable('Refrain')
			->createQuery('a')
			->addWhere("lied_id = ?", array($songId))
			->addOrderBy('RefrainNr DESC')
			->execute();
			
			$this->logMessage('jep count: ' . $lastLiedtext->count(), 'info');
			
			if($lastLiedtext->count() < 1) return 1;
				
			return $lastLiedtext[0]->getRefrainNr() + 1;
		}
		return $rank;
	}
}
