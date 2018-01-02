<?php 
session_start(); // démarrage de la session
require_once('configuration.php');

// Vérification des droits administrateur
if (!isset($_SESSION['droitsAdministrateur'])) $_SESSION['droitsAdministrateur'] = false ;

// connexion si mot de passe fourni
if(isset($_POST['motDePasse']) && $_POST['motDePasse']==$motDePasse) $_SESSION['droitsAdministrateur'] = true ;

$droitsAdministrateur = $_SESSION['droitsAdministrateur'] ;

// récupération du paramètre
if (isset($_GET['galerie'])) $galerieCourante=trim($_GET['galerie']);
else $galerieCourante='';

if (isset($_GET['action'])) $action = $_GET['action'] ;
else $action = '';

$messageRetour = '' ;

if ($droitsAdministrateur)
{	
	switch($action)  
	{
		case '' :
			if ($galerieCourante=='') $messageRetour = 'Veuillez choisir une galerie' ;
			break;
		
		case 'connecter':
			$messageRetour = 'Connexion réussie' ;
			break;

		case 'deconnecter': // Déconnexion
			$droitsAdministrateur = false ; 
			session_destroy(); 
			// Destruction de la session $_SESSION['droitsAdministrateur'] n'est plus définie
			$messageRetour = 'Deconnexion réussie' ;
			break;

		case 'creerGalerie':  // Création d'un nouveau répertoire
			// Vérification de la validité du nom de répertoire
			if(preg_match('#^[\w \-\'ÀÂÄÇÈÉÊËÎÏÔÖÙÛÜàâäçèéêëîïôöûüÿ]+$#i',$galerieCourante))
			{
				@mkdir("$repertoireGaleries/$galerieCourante");
				$messageRetour =  "Création de la galerie \"$galerieCourante\" réussie" ;
			}
			else 
			{
				$messageRetour = "La galerie \"$galerieCourante\" n'a pas été créée. Son nom comporte des caractères refusés" ;
				$galerieCourante = '';
			}
			break;

		case 'supprimerGalerie' :
			// suppression du repertoire correspondant à $_GET['galerie'] (ne marche qui si le repertoire est vide)
			@rmdir("$repertoireGaleries/$galerieCourante");
			$messageRetour = "La galerie \"$galerieCourante\" a été supprimée" ;
			$galerieCourante='' ;
			break;

		case 'supprimerImage' :
			$nomImage=$_GET['image'];
			@unlink("$repertoireGaleries/$galerieCourante/$nomImage");
			$messageRetour = "L'image \"$nomImage\" a été supprimée" ;
			break;

		case 'ajoutImage' :		
			$nomFichier = basename($_FILES['fichier']['name']) ;
			if ( preg_match( '#\.(jpg|jpeg|gif|png|jpe)$#i' , $nomFichier ) )
			{
				$destination = "$repertoireGaleries/$galerieCourante/$nomFichier" ;
				move_uploaded_file( $_FILES['fichier']['tmp_name'], $destination ) ; 
				$messageRetour = "L'image \"$nomFichier\" a été ajoutée" ;
			}
			else
			{
				$messageRetour = "Le fichier \"$nomFichier\" n'est pas un fichier image valide" ;
			}
			break;

		default :
			$messageRetour = 'Cette action est inconnue' ;
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Galerie V5 - Administration</title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body class="pagePrivee">
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
if ($messageRetour)
	echo "<div class=\"blocMessageRetour\">$messageRetour</div>";

if ($droitsAdministrateur) 
{
	if ($galerieCourante) // Si une galerie est sélectionnée
	{
		echo '<div class="galerie" id="galeriePrincipale">';
		echo "<h1 class=\"titreGalerie\">$galerieCourante</h1>";

		// Création de la liste des vignettes de la galerie
		echo '<ul class="listeImages">';
		$pointeurGalerie=opendir("$repertoireGaleries/$galerieCourante");
		$compteurImages = 0 ;
		while ( $nomEntree = readdir($pointeurGalerie) )
		{
			if ( preg_match( '#\.(jpg|jpeg|gif|png|jpe)$#i' , $nomEntree ) )
			{
				$adresseImage = "$repertoireGaleries/$galerieCourante/$nomEntree";
				
				echo '<li>';
				echo 		"<img src=\"reducteur_image.php?hauteur=$hauteurVignettes&image=$adresseImage\">";
				echo 		"<a href=\"?action=supprimerImage&galerie=$galerieCourante&image=$nomEntree\"".
					' class="lienSupprimerImage">';
				echo 			'[X]';
				echo 		'</a>';
				echo '</li>' ;
				$compteurImages++ ;
			}
		}
		echo '</ul>';

		if( $compteurImages == 0 ) // Message
		{
			echo '<div class="blocGalerieVide">' ;
			echo 'La galerie est vide' ;
			echo '</div>' ;
		}
		
		// Formulaire d'ajout d'image dans la galerie courante
		echo '<div class="blocAjoutFichier">';
		echo 		'<form method="post" '.
								"action=\"?action=ajoutImage&galerie=$galerieCourante\" ".
		 						'enctype="multipart/form-data" >' ;
		echo 			'<label for="fichier">Choisissez un fichier image (jpg, gif ou png) </label>';
		echo 			'<input type="hidden" name="MAX_FILE_SIZE" value="4000000">';
		echo 			'<input type="file" name="fichier">';
		echo 			'<input type="submit" value="Envoyer">';
		echo 		'</form>';
		echo '</div>';
		
		if( $compteurImages == 0 ) // Lien de supression de galerie que lorsqu'elle est vide
		{
			echo '<div class="blocSupprimerGalerie">' ;
			echo 		"<a href=\"?action=supprimerGalerie&galerie=$galerieCourante\">";
			echo 			'Supprimer cette galerie';
			echo 		'</a>';
			echo '</div>' ;
		}
		echo '</div><!-- /.galerie#galeriePrincipale -->';
	}

	// Formulaire de création d'une nouvelle galerie
	echo '<div class="blocCreerGalerie">';
	echo 		'<form action="" method="get">';
	echo 			'<input type="hidden" name="action" value="creerGalerie">';
	echo 			'<input type="text" name="galerie"> ';
	echo 			'<input type="submit" value="Créer une nouvelle galerie">';
	echo 		'</form>';
	echo '</div>';


	// lien de deconnexion
	echo '<div id="blocDeconnexion">';
	echo 		'<a href="?action=deconnecter">Deconnexion</a>';
	echo '</div><!-- /#blocDeconnexion -->' ;
}
else // si pas droits d'administrateur, affichage formulaire de connexion
{
	echo '<div id="blocConnexion">';
	echo 		'<form action="?action=connecter" method="post">' ;
	echo 			'<label for="motDePasse">Veuillez entrer le mot de passe</label> ' ;
	echo 			'<input type="password" name="motDePasse" />' ;
	echo 		'</form>' ;
	echo '</div>';
}
?>
</section>

<footer id="piedPage">
	<div id="credit">Exercice IMM Melun</div>
</footer>
</div><!-- /#enveloppe -->
</body>
</html>