<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $refrain->getId() ?></td>
    </tr>
    <tr>
      <th>Refrainnr:</th>
      <td><?php echo $refrain->getRefrainnr() ?></td>
    </tr>
    <tr>
      <th>Refrain:</th>
      <td><?php echo $refrain->getRefrain() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('refrain/edit?id='.$refrain->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('refrain/index') ?>">List</a>
