<?php

/**
 * Liederbuch form base class.
 *
 * @method Liederbuch getObject() Returns the current form's model object
 *
 * @package    adonai
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLiederbuchForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'buchname'     => new sfWidgetFormInputText(),
      'beschreibung' => new sfWidgetFormInputText(),
      'lieder_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Lied')),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'buchname'     => new sfValidatorPass(array('required' => false)),
      'beschreibung' => new sfValidatorPass(array('required' => false)),
      'lieder_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Lied', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('liederbuch[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Liederbuch';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['lieder_list']))
    {
      $this->setDefault('lieder_list', $this->object->Lieder->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveLiederList($con);

    parent::doSave($con);
  }

  public function saveLiederList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['lieder_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Lieder->getPrimaryKeys();
    $values = $this->getValue('lieder_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Lieder', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Lieder', array_values($link));
    }
  }

}
