<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('strophe/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('strophe/index') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'strophe/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['lied_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['lied_id']->renderError() ?>
          <?php echo $form['lied_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['ueberschrift']->renderLabel() ?></th>
        <td>
          <?php echo $form['ueberschrift']->renderError() ?>
          <?php echo $form['ueberschrift'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['ueberschrifttyp']->renderLabel() ?></th>
        <td>
          <?php echo $form['ueberschrifttyp']->renderError() ?>
          <?php echo $form['ueberschrifttyp'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['strophe']->renderLabel() ?></th>
        <td>
          <?php echo $form['strophe']->renderError() ?>
          <?php echo $form['strophe'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['refrain_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['refrain_id']->renderError() ?>
          <?php echo $form['refrain_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['reihenfolge']->renderLabel() ?></th>
        <td>
          <?php echo $form['reihenfolge']->renderError() ?>
          <?php echo $form['reihenfolge'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
