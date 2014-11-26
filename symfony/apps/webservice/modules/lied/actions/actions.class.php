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
		//first check if there is a liederbuch set in the session or the request
		if($request->getParameter('liederbuchId') != null){
			$liederbuchId = $request->getParameter('liederbuchId');
			$this->getUser()->setAttribute("liederbuchId", $liederbuchId);
			$this->logMessage("jep: request for new liederbuch: id " . $liederbuchId);
		}else{
			$liederbuchId = $this->getUser()->getAttribute("liederbuchId");
		}

		//if no liederbuch is set, take the default one
		if($liederbuchId == null){
			$liederbuchId = Doctrine_Core::getTable('Liederbuch')
			->getDefaultLiederbuch()
			->getId();
			$this->getUser()->setAttribute("liederbuchId", $liederbuchId);
			$this->logMessage("jep: Liederbuch with id " . $liederbuchId . " was taken as the default liederbuch because it's the first one.");
		}
		//get the book
		$this->liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($liederbuchId));

		$query = new Doctrine_RawSql();
		$query->select("{l.ID}, {l.Titel},
		{fk.ID}, {fk.liederbuch_id}, {fk.lied_id}, {fk.Liednr}
		{r.Rubrik}")
		->from("Lied as l left Join (SELECT * from fkLiederbuchLied where liederbuch_id = " . $this->liederbuch->getId() . ") as fk ON (l.id = fk.lied_id)")
		->innerJoin("Rubrik as r ON l.rubrik_id = r.id")
		->addComponent("l", "Lied l")
		->addComponent("fk", "l.FkLiederbuchLied fk")
		->addComponent("r", "l.Rubrik r");

		//quicksearch
		$quicksearch = $request->getParameter('quicksearch');
		if(isset($quicksearch)){
			$query->where('fk.Liednr LIKE ? OR l.Titel LIKE ?', array($quicksearch, "%" . $quicksearch . "%"));
			$this->getResponse()->setSlot('quicksearch', $quicksearch);
		}

		//sorting for titel & rubrik
		/*WARNING: SQL Injection possible here!*/
		$sort = json_decode($request->getParameter('sort'));
		$sortDirection = "";
		if(isset($sort)){
			$sortColumn = SQLite3::escapeString($sort[0]->{'property'});
			$sortDirection = SQLite3::escapeString($sort[0]->{'direction'});
			if($sortColumn == "title"){
				$query->addOrderBy('titel ' . $sortDirection);
			}
			else if($sortColumn == 'category'){
				$query->addOrderBy('rubrik ' . $sortDirection);
			}
		}

		//order for Liednr
		//maybe the user has told whether to order ASC or DESC
		/*WARNING: SQL Injection possible here!*/
		$nrSortDirection = "ASC";
		if(isset($sortColumn)){
			if($sortColumn == "nr"){
				$nrSortDirection = $sortDirection;
			}
		}
		//~ is the very last possible character in the ascii table
		$query->addOrderBy("CASE WHEN fk.Liednr IS NULL THEN '~' WHEN fk.Liednr GLOB '[0-9]*' THEN '0000000000' + fk.Liednr ELSE fk.Liednr END " . $sortDirection);
			
		//paging
		$limit = $request->getParameter('limit', 50);
		$start = $request->getParameter('start', 1);
		$page = $request->getParameter('page', 1);
		if($start != 1){
			$page = ($start + $limit) / $limit;
		}

		$this->pager = new sfDoctrinePager('Lied', $limit);

		$this->pager->setQuery($query);
		$this->pager->setPage($page);
		$this->pager->init();
	}

	public function executeShow(sfWebRequest $request)
	{
		//if the id is null or 0 we assume that the user wants to create a new song
		if($request->getParameter('id') == 0 || $request->getParameter('id') == null){
			$this->forward('lied', 'new');
		}
		$id = $request->getParameter('id');
		$this->lied = Doctrine_Core::getTable('Lied')->find(array($id));

		$dbLogger = Logger::getLogger("dbLogger");
		$dbLogger->info("lied->show with title='" . $this->lied->getTitel() . "'; id=" . $id . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")");

		$this->forward404Unless($this->lied);
	}

	//additionally added method by jep
	public function executeShowOverview(sfWebRequest $request)
	{
		$this->lied = Doctrine_Core::getTable('Lied')->find(array($request->getParameter('id')));
		$this->liederbuchId = $this->getUser()->getAttribute("liederbuchId");
		$this->forward404Unless($this->lied);
	}

	/**
	 * This action is used to create a new empty song with all the available songbooks
	 * and return it to the client.
	 * @param sfWebRequest $request
	 */
	public function executeNew(sfWebRequest $request)
	{
		$this->lied = new Lied();
	}

	public function executeCreate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST));

		$this->form = new LiedForm();

		$this->processForm($request, $this->form);

		$this->setTemplate('new');
	}

	public function executeEdit(sfWebRequest $request)
	{
		$this->forward404Unless($lied = Doctrine_Core::getTable('Lied')->find(array($request->getParameter('id'))), sprintf('Object lied does not exist (%s).', $request->getParameter('id')));
		$this->form = new LiedForm($lied);
	}

	public function executeUpdate(sfWebRequest $request)
	{
		$this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
		$this->forward404Unless($lied = Doctrine_Core::getTable('Lied')->find(array($request->getParameter('id'))), sprintf('Object lied does not exist (%s).', $request->getParameter('id')));
		$this->form = new LiedForm($lied);

		//if it's just a quick update (sent from the overview table) do it right in here
		$json_form = json_decode($request->getContent(), true);
		if($request->getParameter("quickEdit")){
			//also empty strings are accepted which will remove the association!

			$lied->setSingleLiederbuchNr($this->getUser()->getAttribute("liederbuchId"), $json_form["nr"]);

			$request->setParameter("id", $lied->getId());

			$this->forward('lied', 'showOverview');
		}

		$this->processForm($request, $this->form);

		$this->setTemplate('edit');
	}

	public function executeDelete(sfWebRequest $request)
	{
		$request->checkCSRFProtection();

		$this->forward404Unless($lied = Doctrine_Core::getTable('Lied')->find(array($request->getParameter('id'))), sprintf('Object lied does not exist (%s).', $request->getParameter('id')));

		//delete all fk-references manually (they are not deleted by the framework or by the database)
		foreach($lied->getFkLiederbuchLied() as $ref){
			$ref->delete();
		}

		$dbLogger = Logger::getLogger("dbLogger");
		$messageToLog = "lied->delete song with title='" . $lied->getTitel() . "'; id=" . $lied->getId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")";

		$lied->delete();
		
		$dbLogger->info($messageToLog);

		$this->setTemplate('delete');
	}

	protected function processForm(sfWebRequest $request, sfForm $form)
	{
		$json_form = json_decode($request->getContent(), true);

		$valuesBefore = "{}";
		$actionString = "created";
		if(!$form->getObject()->isNew()){
			$actionString = "updated";
			$valuesBefore = $this->getLogInfoForSong($form->getObject());
		}

		$valuesToSave = array(
				"titel" => $json_form["title"],
				"rubrik_id" => $json_form["categoryId"],
				//get the not edited values just to make sure they don't become empty.
				"stichwoerter" => $form["stichwoerter"]->getValue(),
				"bemerkungen" => $form["bemerkungen"]->getValue()
		);

		//Die Songbooks zuweisen, damit die Zuordnung erhalten bleibt
		$liederbucher_list = array();
		$liederbucher_associations = array();
		foreach($json_form["songbooks"] as $liederbuch){
			if($liederbuch["number"]){
				$liederbucher_list[] = $liederbuch["id"];
				$liederbucher_associations[$liederbuch["id"]] = $liederbuch["number"];
			}
		}
		$valuesToSave["liederbucher_list"] = $liederbucher_list;


		$form->bind($valuesToSave, $request->getFiles($form->getName()));
		if ($form->isValid())
		{
			$lied = $form->save();
				
			//refresh the object afterwords because $valuesBefore = $form->getObject()->getShowArray(); is being cached
			$lied->refresh(true);

			//now add the associations
			$liederbucher = $lied->getFkLiederbuchLied();
			foreach($liederbucher as $fkLiederbuchLied){
				$liederbuchId = $fkLiederbuchLied->getLiederbuchId();
				if(in_array($liederbuchId, $liederbucher_list)){
					$fkLiederbuchLied->setLiednr($liederbucher_associations[$liederbuchId]);
					$fkLiederbuchLied->save();
				}
			}
				
			//refresh the object afterwords because $valuesBefore = $form->getObject()->getShowArray(); is being cached
			$lied->refresh(true);
				
			$dbLogger = Logger::getLogger("dbLogger");
			$messageToLog = "lied->processForm " . $actionString . " song with new title='" . $lied->getTitel() . "'; id=" . $lied->getId() . " (user=" . $_SESSION["email"] . ", id=" . $_SESSION["id"] . ")";
			$messageToLog .= "\nOld values:\n" . $valuesBefore;
			$messageToLog .= "\nNew values:\n" . $this->getLogInfoForSong($lied);
			$dbLogger->info($messageToLog);

			$request->setParameter("id", $lied->getId());

			//$this->redirect('lied/show?id='.$lied->getId());
			$this->forward('lied', 'show');
		}else{
			echo "das Form ist NICHT valid";
		}
	}
	private function getLogInfoForSong(Lied $song){
		$songData = $song->getShowArray();
		$songData["songbooks"] = $song->getAllSongbooksWithNumberArray();
		$songData["verses"] = $song->getAllVerseIds();

		return json_encode($songData, JSON_PRETTY_PRINT );
	}

}
