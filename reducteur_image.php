<?php 

$adresseImage=$_GET['image']; // on recupère l'URL absolue ou relative de l'image

if ( isset( $_GET['largeur'] ) ) $largeurMaximum=$_GET['largeur']; 
// on recupère la largeur maxi si elle est fournie

if ( isset( $_GET['hauteur'] ) ) $hauteurMaximum=$_GET['hauteur'];
// on recupère la hauteur maxi si elle est fournie

$dimensionsNatives = getimagesize($adresseImage) ;
// on recupère les dimensions de l'image dans un tableau (array) à 2 entrées

$largeurNative = $dimensionsNatives[0];
$hauteurNative = $dimensionsNatives[1];

if (isset($hauteurMaximum) && !isset($largeurMaximum))
	$echelle = $hauteurMaximum/$hauteurNative ; // calcul du rapport d'échelle
elseif ( !isset($hauteurMaximum) && isset($largeurMaximum))
	$echelle = $largeurMaximum/$largeurNative ; // calcul du rapport d'échelle
else
	$echelle = min( $largeurMaximum/$largeurNative , $hauteurMaximum/$hauteurNative) ; // calcul du rapport d'échelle

// Empécher l'agrandissement
$echelle = min( $echelle , 1 ) ;

// calcul des dimensions de sortie en respectant les proportions de l'image d'entrée
$largeurSortie = $largeurNative * $echelle ; 
$hauteurSortie = $hauteurNative * $echelle ;

// creation d'une image à partir du fichier image d'entrée
if(preg_match( '#\.(jpg|jpeg|jpe)$#i' , $adresseImage )) $imageEntree = imagecreatefromjpeg($adresseImage);
if(preg_match( '#\.png$#i' , $adresseImage )) $imageEntree = imagecreatefrompng($adresseImage);
if(preg_match( '#\.gif$#i' , $adresseImage )) $imageEntree = imagecreatefromgif($adresseImage);


// création d'un image de sortie vide en mode RVB
$imageSortie = imagecreatetruecolor($largeurSortie,$hauteurSortie);

// incorporation de l'image d'entrée dans l'image de sortie avec rechantillonage
imagecopyresampled(
	$imageSortie, // image destination
	$imageEntree, // image source
	0,0, // coordonnées angle supérieur gauche de destination
	0,0, // coordonnées angle supérieur gauche de départ
	$largeurSortie,$hauteurSortie, // dimensions de destination
	$largeurNative,$hauteurNative // dimensions de la source
);


// composition de la réponse http
header('content-type:image/jpeg'); // entete http spécifiant le type de donnée
imagejpeg($imageSortie); // envoi des données encodées en JPEG


