<?php

/**
 * Rubrik filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseRubrikFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'rubrik'      => new sfWidgetFormFilterInput(),
      'reihenfolge' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'rubrik'      => new sfValidatorPass(array('required' => false)),
      'reihenfolge' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('rubrik_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Rubrik';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'rubrik'      => 'Text',
      'reihenfolge' => 'Number',
    );
  }
}
