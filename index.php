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

<style type="text/css">
.loadingMessage {
	position: absolute;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 20000;
	background-color: rgb(50, 83, 100);
}

.loadingMessage div {
	padding-top: 100px; font-family : helvetica, arial, verdana, sans-serif;
	font-size: 20px;
	color: white;
	text-align: center;
	font-family: helvetica, arial, verdana, sans-serif;
	line-height: 30px;
}
</style>

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
		<div style="margin: 0 auto; width: 500px;">
			<img src="resources/images/loading.gif" /><br /> <br /> Im Moment
			wird die Applikation geladen.<br />Bitte hab einen Moment Geduld...
		</div>
	</div>
</body>
</html>
