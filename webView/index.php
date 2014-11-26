<?php
	session_name('symfony');
	session_start();
	
	//check if a liederbuch is selected
	$liederbuch = @$_SESSION["symfony/user/sfUser/attributes"]
					["symfony/user/sfUser/attributes"]
					["liederbuchId"];
	
	if($liederbuch > 0){
		$anzeige = "black.html";
	}else{
		$anzeige = "../sfWeb/webView.php/liederbuch";
	}
	
	$auswahl = "../sfWeb/webView.php/lied";
	
	//check for second view
	if(isset($_GET['secondView'])){
		$secondView = "?secondView=" . $_GET['secondView'];
	}else{
		$secondView = "";
	}
	
	if(isset($_GET['id']) && $_GET['id'] != "" && $_GET['id'] != "0"){
		$id = $_GET['id'];
		$anzeige = "../sfWeb/webView.php/strophe/show/bookNr/" . $id;
		$auswahl = "../sfWeb/webView.php/lied/show/bookNr/" . $id;
	}
	
	$auswahl .= $secondView;
?>

<html>

<head>
  <title>adonai Liederbuch</title>
  <script type="text/javascript">
	  <?php
			if(isset($_GET['secondView']) && $_GET['secondView'] == "true"){
				echo "window.open('" . $anzeige . "', 'anzeige2');";
			}
		?>
	</script>
</head>
<frameset framespacing="0" border="0" frameborder="0" rows="*,40px" style="background-color: black;">
  <frame name="anzeige" src="<?php echo $anzeige; ?>" scrolling="auto" noresize style="background-color: black;">
  <frame name="auswahl" src="<?php echo $auswahl; ?>" scrolling="no" noresize style="background-color: black;">
  <noframes>
  <body style="background-color:#000000;">

  <p>Diese Seite verwendet Frames. Frames werden von Ihrem Browser aber nicht unterstützt.</p>

  </body>
  </noframes>
</frameset>

</html>