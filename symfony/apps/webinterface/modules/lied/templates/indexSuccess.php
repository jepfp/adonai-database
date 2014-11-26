<h1>Lieds List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Nr</th>
      <th>Titel</th>
      <th>Rubrik</th>
      <th>Stichwoerter</th>
      <th>Bemerkungen</th>
      <th>Created at</th>
      <th>Updated at</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($lieds as $lied): ?>
    <tr>
      <td><a href="<?php echo url_for('lied/show?id='.$lied->getId()) ?>"><?php echo $lied->getId() ?></a></td>
      <td><?php echo $lied->getNr() ?></td>
      <td><?php echo $lied->getTitel() ?></td>
      <td><?php echo $lied->getRubrikId() ?></td>
      <td><?php echo $lied->getStichwoerter() ?></td>
      <td><?php echo $lied->getBemerkungen() ?></td>
      <td><?php echo $lied->getCreatedAt() ?></td>
      <td><?php echo $lied->getUpdatedAt() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('lied/new') ?>">New</a>
