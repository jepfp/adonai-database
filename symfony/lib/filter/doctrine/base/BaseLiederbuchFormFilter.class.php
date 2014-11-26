<?php

/**
 * Liederbuch filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLiederbuchFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'buchname'     => new sfWidgetFormFilterInput(),
      'beschreibung' => new sfWidgetFormFilterInput(),
      'lieder_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Lied')),
    ));

    $this->setValidators(array(
      'buchname'     => new sfValidatorPass(array('required' => false)),
      'beschreibung' => new sfValidatorPass(array('required' => false)),
      'lieder_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Lied', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('liederbuch_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addLiederListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.FkLiederbuchLied FkLiederbuchLied')
      ->andWhereIn('FkLiederbuchLied.lied_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Liederbuch';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'buchname'     => 'Text',
      'beschreibung' => 'Text',
      'lieder_list'  => 'ManyKey',
    );
  }
}
