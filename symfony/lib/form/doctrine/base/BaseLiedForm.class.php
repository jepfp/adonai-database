<?php

/**
 * Lied form base class.
 *
 * @method Lied getObject() Returns the current form's model object
 *
 * @package    adonai
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLiedForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'titel'             => new sfWidgetFormInputText(),
      'rubrik_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Rubrik'), 'add_empty' => true)),
      'stichwoerter'      => new sfWidgetFormInputText(),
      'bemerkungen'       => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
      'liederbucher_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Liederbuch')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'titel'             => new sfValidatorPass(array('required' => false)),
      'rubrik_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Rubrik'), 'required' => false)),
      'stichwoerter'      => new sfValidatorPass(array('required' => false)),
      'bemerkungen'       => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
      'liederbucher_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Liederbuch', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('lied[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Lied';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['liederbucher_list']))
    {
      $this->setDefault('liederbucher_list', $this->object->Liederbucher->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveLiederbucherList($con);

    parent::doSave($con);
  }

  public function saveLiederbucherList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['liederbucher_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Liederbucher->getPrimaryKeys();
    $values = $this->getValue('liederbucher_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Liederbucher', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Liederbucher', array_values($link));
    }
  }

}
