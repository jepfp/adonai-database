<?php

/**
 * Lied form.
 *
 * @package    adonai
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LiedForm extends BaseLiedForm
{
  public function configure()
  {
  	//we remove this values from the form so that doctrine fills them
  	//automatically according to
  	//http://www.tech-recipes.com/rx/11248/symfony-1-4-add-and-update-created_at-and-updated_at-fields-automatically/
  	unset($this['created_at'], $this['updated_at']);
  }
}
