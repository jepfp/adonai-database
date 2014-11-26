<h1>Liedtexts List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Lied</th>
      <th>Ueberschrift</th>
      <th>Ueberschrifttyp</th>
      <th>Strophe</th>
      <th>Refrain</th>
      <th>Reihenfolge</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($liedtexts as $liedtext): ?>
    <tr>
      <td><a href="<?php echo url_for('strophe/show?id='.$liedtext->getId()) ?>"><?php echo $liedtext->getId() ?></a></td>
      <td><?php echo $liedtext->getLiedId() ?></td>
      <td><?php echo $liedtext->getUeberschrift() ?></td>
      <td><?php echo $liedtext->getUeberschrifttyp() ?></td>
      <td><?php echo $liedtext->getStrophe() ?></td>
      <td><?php echo $liedtext->getRefrainId() ?></td>
      <td><?php echo $liedtext->getReihenfolge() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('strophe/new') ?>">New</a>
