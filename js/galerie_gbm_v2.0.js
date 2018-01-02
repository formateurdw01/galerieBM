function creerGalerieGBM(unElement) 
{
	function clickSurLien()
	{ 
		imageAffichage.src = this.href ;
		return false ; 
	}

	var blocAffichage=document.createElement('div');
	blocAffichage.className="blocAffichageGBM";
	unElement.appendChild(blocAffichage);	

	var blocAffichageInterieur=document.createElement('div');
	blocAffichageInterieur.className="blocAffichageGBMint";
	blocAffichage.appendChild(blocAffichageInterieur);	

	var imageAffichage=document.createElement('img');
	blocAffichageInterieur.appendChild(imageAffichage);
	
	var liens = unElement.getElementsByTagName('a');
	var nombreLiens = liens.length;
	
	for ( var i=0 ; i<nombreLiens ; i++ ) 
	{
		liens[i].onclick = clickSurLien;
	}
	if ( liens[0] != undefined ) 
	{ 
		liens[0].onclick();
	}
}