<?php

/**
 * rubrik actions.
 *
 * @package    adonai
 * @subpackage rubrik
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rubrikActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->rubriks = Doctrine_Core::getTable('Rubrik')
      ->createQuery('a')
      ->orderBy("Reihenfolge")
      ->execute();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->rubrik = Doctrine_Core::getTable('Rubrik')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->rubrik);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new RubrikForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new RubrikForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($rubrik = Doctrine_Core::getTable('Rubrik')->find(array($request->getParameter('id'))), sprintf('Object rubrik does not exist (%s).', $request->getParameter('id')));
    $this->form = new RubrikForm($rubrik);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($rubrik = Doctrine_Core::getTable('Rubrik')->find(array($request->getParameter('id'))), sprintf('Object rubrik does not exist (%s).', $request->getParameter('id')));
    $this->form = new RubrikForm($rubrik);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($rubrik = Doctrine_Core::getTable('Rubrik')->find(array($request->getParameter('id'))), sprintf('Object rubrik does not exist (%s).', $request->getParameter('id')));
    $rubrik->delete();

    $this->redirect('rubrik/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $rubrik = $form->save();

      $this->redirect('rubrik/edit?id='.$rubrik->getId());
    }
  }
}
