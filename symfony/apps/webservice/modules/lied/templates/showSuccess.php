<table>
  <tbody>
    <tr>
      <th>Id:</th>
      <td><?php echo $lied->getId() ?></td>
    </tr>
    <tr>
      <th>Nr:</th>
      <td><?php echo $lied->getNr() ?></td>
    </tr>
    <tr>
      <th>Titel:</th>
      <td><?php echo $lied->getTitel() ?></td>
    </tr>
    <tr>
      <th>Rubrik:</th>
      <td><?php echo $lied->getRubrikId() ?></td>
    </tr>
    <tr>
      <th>Stichwoerter:</th>
      <td><?php echo $lied->getStichwoerter() ?></td>
    </tr>
    <tr>
      <th>Bemerkungen:</th>
      <td><?php echo $lied->getBemerkungen() ?></td>
    </tr>
    <tr>
      <th>Created at:</th>
      <td><?php echo $lied->getCreatedAt() ?></td>
    </tr>
    <tr>
      <th>Updated at:</th>
      <td><?php echo $lied->getUpdatedAt() ?></td>
    </tr>
  </tbody>
</table>

<hr />

<a href="<?php echo url_for('lied/edit?id='.$lied->getId()) ?>">Edit</a>
&nbsp;
<a href="<?php echo url_for('lied/index') ?>">List</a>
