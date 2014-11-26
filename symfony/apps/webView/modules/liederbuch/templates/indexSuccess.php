<div class="chooseLiederbuch">

<h1>Liederbuch wählen</h1>

Bitte wähle das gewünschte Liederbuch aus der Liste.<br>
Dannach kannst du mit den Liednummern dieses Buches arbeiten.
<ul>
<?php foreach ($liederbuchs as $liederbuch): ?>
	<li>
		<a href="<?php echo url_for('liederbuch/select?id='.$liederbuch->getId()) ?>">
		<?php echo $liederbuch->getBuchname() ?>
		</a>
		<br>
		<font style="font-size: smaller;"><?php echo $liederbuch->getBeschreibung() ?></font>
	</li>
<?php endforeach; ?>
</ul>

</div>