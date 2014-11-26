<h1>Refrains List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Refrainnr</th>
      <th>Refrain</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($refrains as $refrain): ?>
    <tr>
      <td><a href="<?php echo url_for('refrain/show?id='.$refrain->getId()) ?>"><?php echo $refrain->getId() ?></a></td>
      <td><?php echo $refrain->getRefrainnr() ?></td>
      <td><?php echo $refrain->getRefrain() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('refrain/new') ?>">New</a>
