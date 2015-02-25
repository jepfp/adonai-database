<?php
require_once 'src/bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php
echo $projectConfiguration->getProjectTitle();
echo " (" . $projectConfiguration->getProjectName() . ")";
?>
</title>

<script type="text/javascript">
SCOTTY_CLIENT_CONFIGURATION = <?php echo $projectConfiguration->getClientConfigurationJson(); ?>;
</script>

<?php if($projectConfiguration->isProductiveMode()){ ?>
<!-- PRODUCTIVE MODE -->
<!-- In order to test the productive mode you need to build and deploy
to the local test folder (build/all). To do so, execute the
deployLocalTesting target in the buidAndDeploy.xml Ant file. -->
<link rel="stylesheet" href="resources/Songserver-all.css" />
<script type="text/javascript" src="app.js"></script>
<?php
} else {
    ?>
	<script id="microloader" type="text/javascript" src="bootstrap.js"></script>
	
<?php
}
?>
<script src="src/ext-direct-api.php"></script>
<link rel="stylesheet" type="text/css" href="resources/css/style.css" />

</head>
<body>
	<div class="loadingMessage" id="appLoadingMessage">
		<div class="applicationHeader">Adoray | Adonai Datenbank - Herzlich
			Willkommen</div>
		<br>
		<p>
			<img src="resources/images/loading.gif"
				style="float: left; padding-right: 5px;" /> Im Moment wird die
			Applikation geladen. Bitte hab einen Moment Geduld...
		</p>
	</div>
</body>
</html>
