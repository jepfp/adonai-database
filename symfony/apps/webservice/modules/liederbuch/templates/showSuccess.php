<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $liederbuch->getId() ?></td>
    </tr>
    <tr>
      <th>Buchname:</th>
      <td><?php echo $liederbuch->getBuchname() ?></td>
    </tr>
    <tr>
      <th>Beschreibung:</th>
      <td><?php echo $liederbuch->getBeschreibung() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('liederbuch/edit?id='.$liederbuch->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('liederbuch/index') ?>">List</a>
