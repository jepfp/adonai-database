<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $rubrik->getId() ?></td>
    </tr>
    <tr>
      <th>Rubrik:</th>
      <td><?php echo $rubrik->getRubrik() ?></td>
    </tr>
    <tr>
      <th>Reihenfolge:</th>
      <td><?php echo $rubrik->getReihenfolge() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('rubrik/edit?id='.$rubrik->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('rubrik/index') ?>">List</a>
