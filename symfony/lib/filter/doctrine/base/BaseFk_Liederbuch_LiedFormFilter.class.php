<?php

/**
 * Fk_Liederbuch_Lied filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseFk_Liederbuch_LiedFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'liederbuch_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Liederbuch'), 'add_empty' => true)),
      'lied_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'add_empty' => true)),
      'liednr'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'liederbuch_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Liederbuch'), 'column' => 'id')),
      'lied_id'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lied'), 'column' => 'id')),
      'liednr'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('fk_liederbuch_lied_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Fk_Liederbuch_Lied';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'liederbuch_id' => 'ForeignKey',
      'lied_id'       => 'ForeignKey',
      'liednr'        => 'Text',
    );
  }
}
