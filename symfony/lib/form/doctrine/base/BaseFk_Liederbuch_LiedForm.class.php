<?php

/**
 * Fk_Liederbuch_Lied form base class.
 *
 * @method Fk_Liederbuch_Lied getObject() Returns the current form's model object
 *
 * @package    adonai
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseFk_Liederbuch_LiedForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'liederbuch_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Liederbuch'), 'add_empty' => true)),
      'lied_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'add_empty' => true)),
      'liednr'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'liederbuch_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Liederbuch'), 'required' => false)),
      'lied_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'required' => false)),
      'liednr'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fk_liederbuch_lied[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Fk_Liederbuch_Lied';
  }

}
