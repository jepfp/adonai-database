<?php

	$refrain = $sf_data->getRaw('liedtext')->getRefrain()->getRefrain();
	if($refrain){
		echo '<div class="refrainText">';
		echo $refrain;
		echo '</div>';	
	}
?>

<div class="stropheText">
<?php
	echo $sf_data->getRaw('liedtext')->getStrophe();
?>
</div>
