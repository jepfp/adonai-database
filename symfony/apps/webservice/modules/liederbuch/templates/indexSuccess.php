<h1>Liederbuchs List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Buchname</th>
      <th>Beschreibung</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($liederbuchs as $liederbuch): ?>
    <tr>
      <td><a href="<?php echo url_for('liederbuch/show?id='.$liederbuch->getId()) ?>"><?php echo $liederbuch->getId() ?></a></td>
      <td><?php echo $liederbuch->getBuchname() ?></td>
      <td><?php echo $liederbuch->getBeschreibung() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('liederbuch/new') ?>">New</a>
