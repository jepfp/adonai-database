<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form action="<?php echo url_for('lied/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('lied/index') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'lied/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['nr']->renderLabel() ?></th>
        <td>
          <?php echo $form['nr']->renderError() ?>
          <?php echo $form['nr'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['titel']->renderLabel() ?></th>
        <td>
          <?php echo $form['titel']->renderError() ?>
          <?php echo $form['titel'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['rubrik_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['rubrik_id']->renderError() ?>
          <?php echo $form['rubrik_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['stichwoerter']->renderLabel() ?></th>
        <td>
          <?php echo $form['stichwoerter']->renderError() ?>
          <?php echo $form['stichwoerter'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['bemerkungen']->renderLabel() ?></th>
        <td>
          <?php echo $form['bemerkungen']->renderError() ?>
          <?php echo $form['bemerkungen'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_at']->renderError() ?>
          <?php echo $form['created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['updated_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['updated_at']->renderError() ?>
          <?php echo $form['updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['liederbucher_list']->renderLabel() ?></th>
        <td>
          <?php echo $form['liederbucher_list']->renderError() ?>
          <?php echo $form['liederbucher_list'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
