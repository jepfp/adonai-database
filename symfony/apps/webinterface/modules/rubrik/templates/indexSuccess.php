<h1>Rubriks List</h1>

<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Rubrik</th>
      <th>Reihenfolge</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rubriks as $rubrik): ?>
    <tr>
      <td><a href="<?php echo url_for('rubrik/show?id='.$rubrik->getId()) ?>"><?php echo $rubrik->getId() ?></a></td>
      <td><?php echo $rubrik->getRubrik() ?></td>
      <td><?php echo $rubrik->getReihenfolge() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('rubrik/new') ?>">New</a>
