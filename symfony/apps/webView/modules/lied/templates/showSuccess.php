<?php
$webViewUrl = sfConfig::get('app_webviewurl');
?>

<div class="navigation">

<div class="strophen">
<?php
$i = 1;
$strophenLinks = Array();
foreach($lied->getLiedtext() as $strophe){
	echo '<input type="button" onclick="openStrophe(' . $i . ');" value="' . $i . '" />';
	$strophenLinks[] = "'" . url_for('strophe/show?id='.$strophe->getId()) . "'";
	$i++;
}
?>
</div>

<div class="space">&nbsp;</div>

<div class="nextPreviousStrophe">
<?php
echo '<input type="button" onclick="previous();" value="<" />';
echo '<input type="text" value="1" id="currentStrophe" readonly />';
echo '<input type="button" onclick="next();" value=">" />';
?>
</div>

<script type="text/javascript">
<!--
<?php
	$secondView = $sf_params->get("secondView");
	if($secondView == "true"){
		echo "var secondView = true;";
		$secondView = "true";
	}else{
		echo "var secondView = false;";
		$secondView = "false";	
	}
?>

var strophen = [
	<?php
		echo implode(",\n", $strophenLinks);
	?>
];

var refrain = [
	<?php
		foreach($lied->getAssignedRefrains() as $refrain){
			echo "//" . $refrain->getId() . "\n";
			echo "'" . url_for('refrain/show?id='.$refrain->getId()) . "',\n";
		}
	?>
]

function next(){
	var currentStrophe = document.getElementById('currentStrophe');
	var currentStropheVal = parseInt(currentStrophe.value);
	if(isNaN(currentStropheVal)) return;
	if(currentStropheVal < strophen.length){
		var newValue = currentStropheVal + 1
		openStrophe(newValue);
	}
}

function previous(){
	var currentStrophe = document.getElementById('currentStrophe');
	var currentStropheVal = parseInt(currentStrophe.value);
	if(isNaN(currentStropheVal)) return;
	if(currentStropheVal > 1){
		var newValue = currentStropheVal - 1
		openStrophe(newValue);
	}
}

function openStrophe(nr){
	document.getElementById('currentStrophe').value = nr;
	openLink(strophen[nr - 1]);
}

function openRefrain(nr){
	document.getElementById('currentStrophe').value = 'R' + nr;
	openLink(refrain[nr - 1]);
}

function openLink(link){
	window.open(link, 'anzeige');
	if(secondView == true){
		window.open(link, 'anzeige2');
	}
}

// -->
</script>


<div class="space">&nbsp;</div>

<div class="refrain">
<?php
$i = 1;
foreach($lied->getAssignedRefrains() as $refrain){
	echo '<input type="button" onclick="openRefrain('. $i . ');" value="R' . $i . '" />';
	$i++;
}
?>
</div>

<div class="space">&nbsp;</div>

<div class="liedwahl">
<form action="<?php echo $webViewUrl; ?>index.php" method="get" target="_top">
<input type="text" id="liedwahlInputBox" name="id" autocomplete="off"/>
<input type="hidden" name="secondView" id="secondView" value="<?php echo $secondView; ?>" />
<input type="submit" value="ok"/>
</form>
</div>

</div>
