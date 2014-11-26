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
  public function executeShow(sfWebRequest $request)
  {
    $this->refrain = Doctrine_Core::getTable('Refrain')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->refrain);
  }
}
