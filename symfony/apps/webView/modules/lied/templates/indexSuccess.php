<?php 
	$webViewUrl = sfConfig::get('app_webviewurl');
?>

<script type="text/javascript">
<!--
	<?php
		$secondView = $sf_params->get("secondView");
		if($secondView == "true"){
			echo "var secondView = true;";
		}else{
			echo "var secondView = false;";
			$secondView = "false";	
		}
	?>
	function addWindow(){
		window.open('black.html', 'anzeige2');
		document.getElementById("secondView").value = "true";
	}
-->
</script>

<div class="navigation">
<div class="liedwahl">
<form action="<?php echo $webViewUrl; ?>index.php" method="get" target="_top">
<input type="text" name="id" autocomplete="off" />
<input type="hidden" name="secondView" id="secondView" value="<?php echo $secondView; ?>" />
<input type="submit" value="ok" />
<input type="button" onclick="addWindow();" value="Zusätzliches Fenster" />
</form>
</div>
</div>