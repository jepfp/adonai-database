<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $liedtext->getId() ?></td>
    </tr>
    <tr>
      <th>Lied:</th>
      <td><?php echo $liedtext->getLiedId() ?></td>
    </tr>
    <tr>
      <th>Ueberschrift:</th>
      <td><?php echo $liedtext->getUeberschrift() ?></td>
    </tr>
    <tr>
      <th>Ueberschrifttyp:</th>
      <td><?php echo $liedtext->getUeberschrifttyp() ?></td>
    </tr>
    <tr>
      <th>Strophe:</th>
      <td><?php echo $liedtext->getStrophe() ?></td>
    </tr>
    <tr>
      <th>Refrain:</th>
      <td><?php echo $liedtext->getRefrainId() ?></td>
    </tr>
    <tr>
      <th>Reihenfolge:</th>
      <td><?php echo $liedtext->getReihenfolge() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('strophe/edit?id='.$liedtext->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('strophe/index') ?>">List</a>
