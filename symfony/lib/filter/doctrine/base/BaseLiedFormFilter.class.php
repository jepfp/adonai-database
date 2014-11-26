<?php

/**
 * Lied filter form base class.
 *
 * @package    adonai
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLiedFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'titel'             => new sfWidgetFormFilterInput(),
      'rubrik_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Rubrik'), 'add_empty' => true)),
      'stichwoerter'      => new sfWidgetFormFilterInput(),
      'bemerkungen'       => new sfWidgetFormFilterInput(),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'liederbucher_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Liederbuch')),
    ));

    $this->setValidators(array(
      'titel'             => new sfValidatorPass(array('required' => false)),
      'rubrik_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Rubrik'), 'column' => 'id')),
      'stichwoerter'      => new sfValidatorPass(array('required' => false)),
      'bemerkungen'       => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'liederbucher_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Liederbuch', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('lied_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addLiederbucherListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('FkLiederbuchLied.liederbuch_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Lied';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'titel'             => 'Text',
      'rubrik_id'         => 'ForeignKey',
      'stichwoerter'      => 'Text',
      'bemerkungen'       => 'Text',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
      'liederbucher_list' => 'ManyKey',
    );
  }
}
