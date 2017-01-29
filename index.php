<?php
require_once 'src/bootstrap.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=10, user-scalable=yes">
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
	padding-top: 100px;
	font-family: helvetica, arial, verdana, sans-serif;
	font-size: 20px;
	color: white;
	text-align: center;
	font-family: helvetica, arial, verdana, sans-serif;
	line-height: 30px;
}
</style>
<script src="src/ext-direct-api.php"></script>

<script id="microloader" data-app="4535064e-a94c-4d8f-a350-27bf10b54277" type="text/javascript" src="bootstrap.js"></script>


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
