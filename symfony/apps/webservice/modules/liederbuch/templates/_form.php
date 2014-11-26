<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('liederbuch/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('liederbuch/index') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'liederbuch/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['buchname']->renderLabel() ?></th>
        <td>
          <?php echo $form['buchname']->renderError() ?>
          <?php echo $form['buchname'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['beschreibung']->renderLabel() ?></th>
        <td>
          <?php echo $form['beschreibung']->renderError() ?>
          <?php echo $form['beschreibung'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['lieder_list']->renderLabel() ?></th>
        <td>
          <?php echo $form['lieder_list']->renderError() ?>
          <?php echo $form['lieder_list'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
