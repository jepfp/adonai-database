<?php

/**
 * Liedtext form base class.
 *
 * @method Liedtext getObject() Returns the current form's model object
 *
 * @package    adonai
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLiedtextForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'lied_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'add_empty' => true)),
      'ueberschrift'    => new sfWidgetFormInputText(),
      'ueberschrifttyp' => new sfWidgetFormInputText(),
      'strophe'         => new sfWidgetFormInputText(),
      'refrain_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Refrain'), 'add_empty' => true)),
      'reihenfolge'     => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'lied_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'required' => false)),
      'ueberschrift'    => new sfValidatorPass(array('required' => false)),
      'ueberschrifttyp' => new sfValidatorPass(array('required' => false)),
      'strophe'         => new sfValidatorPass(array('required' => false)),
      'refrain_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Refrain'), 'required' => false)),
      'reihenfolge'     => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('liedtext[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Liedtext';
  }

}
