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

  public function executeShow(sfWebRequest $request)
  {
    $this->liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->liederbuch);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new LiederbuchForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new LiederbuchForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($request->getParameter('id'))), sprintf('Object liederbuch does not exist (%s).', $request->getParameter('id')));
    $this->form = new LiederbuchForm($liederbuch);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($request->getParameter('id'))), sprintf('Object liederbuch does not exist (%s).', $request->getParameter('id')));
    $this->form = new LiederbuchForm($liederbuch);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($liederbuch = Doctrine_Core::getTable('Liederbuch')->find(array($request->getParameter('id'))), sprintf('Object liederbuch does not exist (%s).', $request->getParameter('id')));
    $liederbuch->delete();

    $this->redirect('liederbuch/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $liederbuch = $form->save();

      $this->redirect('liederbuch/edit?id='.$liederbuch->getId());
    }
  }
}
