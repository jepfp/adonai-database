<?php

/**
 * Liedtext filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLiedtextFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'lied_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Lied'), 'add_empty' => true)),
      'ueberschrift'    => new sfWidgetFormFilterInput(),
      'ueberschrifttyp' => new sfWidgetFormFilterInput(),
      'strophe'         => new sfWidgetFormFilterInput(),
      'refrain_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Refrain'), 'add_empty' => true)),
      'reihenfolge'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'lied_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Lied'), 'column' => 'id')),
      'ueberschrift'    => new sfValidatorPass(array('required' => false)),
      'ueberschrifttyp' => new sfValidatorPass(array('required' => false)),
      'strophe'         => new sfValidatorPass(array('required' => false)),
      'refrain_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Refrain'), 'column' => 'id')),
      'reihenfolge'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('liedtext_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Liedtext';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'lied_id'         => 'ForeignKey',
      'ueberschrift'    => 'Text',
      'ueberschrifttyp' => 'Text',
      'strophe'         => 'Text',
      'refrain_id'      => 'ForeignKey',
      'reihenfolge'     => 'Number',
    );
  }
}
