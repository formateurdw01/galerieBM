<?php 
	require_once('configuration.php');
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Galerie V5</title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
<link rel="stylesheet" type="text/css" href="css/galerie_gbm_v2.0.css">
</head>

<body class="pagePublique">
<div id="enveloppe">
<header id="entetePage">
	<h1><a href="index.php">Galerie V5.0</a></h1>
  <h2><a href="index.php">Galerie PHP-Javascript sans base de données</a></h2>
</header>

<nav id="navigationPrincipale">
	<ul>
  	<li><a href="index.php">Accueil</a></li>
  	<li><a href="administration.php">Administration</a></li>
    <li>
    	<ul>
<?php // création de la liste des repertoires-galeries
$pointeurGaleries = opendir($repertoireGaleries);
while($nomEntree=readdir($pointeurGaleries))
{
	if( is_dir("$repertoireGaleries/$nomEntree") && $nomEntree!='.' && $nomEntree!='..' )
	{
		echo "<li><a href=\"?galerie=$nomEntree\">$nomEntree</a></li>" ;
 	} 
}
?>
			</ul>
    </li>
  </ul>
</nav>

<section id="sectionPrincipale">
<?php
if(isset($_GET['galerie']))
{
	$galerieCourante = $_GET['galerie'];
?>
	<div class="galerie" id="galeriePrincipale">
  	<h1 class="titreGalerie"><?php echo $galerieCourante ; ?></h1>
    <ul class="listeImages">
    <?php
			$pointeurGalerie=opendir("$repertoireGaleries/$galerieCourante");
			while ( $nomEntree = readdir($pointeurGalerie) )
			{
				if ( preg_match( '#\.(jpg|jpeg|gif|png|jpe)$#i' , $nomEntree ) )
				{
					$adresseImage = "$repertoireGaleries/$galerieCourante/$nomEntree";
					
					echo "<li><a href=\"$repertoireGaleries/$galerieCourante/$nomEntree\">";
					echo "<img src=\"reducteur_image.php?hauteur=$hauteurVignettes&image=$adresseImage\">";
					echo "</a></li>" ;
				}
			}
		?>
    </ul>
	</div><!-- /.galerie#galeriePrincipale -->
<?php
}
?>
</section>
<footer id="piedPage">
	<div id="credit">Exercice IMM Melun</div>
</footer>
</div><!-- /#enveloppe -->
<script src="js/galerie_gbm_v2.0.js"></script>
<script>creerGalerieGBM( document.getElementById('galeriePrincipale') )</script>
</body>
</html>