<?php

/**
 * Refrain filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRefrainFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'refrainnr' => new sfWidgetFormFilterInput(),
      'refrain'   => new sfWidgetFormFilterInput(),
      'lied_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'refrainnr' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'refrain'   => new sfValidatorPass(array('required' => false)),
      'lied_id'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lied'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('refrain_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Refrain';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'refrainnr' => 'Number',
      'refrain'   => 'Text',
      'lied_id'   => 'ForeignKey',
    );
  }
}
