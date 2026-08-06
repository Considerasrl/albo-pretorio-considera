<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- shortcode/vista pubblica read-only: legge id e parametri di ricerca/filtro via GET per la sola visualizzazione (pre-sanitizzati), nessuna mutazione di stato.
/**
 * Gestione Filtri FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */
function albopc_get_FiltriParametri($Stato=1,$cat=0,$StatoFinestra="si"){
	$anni=albopc_get_dropdown_anni_atti('anno','anno','d-inline','',(isset($_REQUEST['anno'])?sanitize_text_field(wp_unslash($_REQUEST['anno'] ?? '')):0),$Stato); 
	$categorie=albopc_get_dropdown_ricerca_categorie('categoria','categoria','postform','',(isset($_REQUEST['categoria'])?sanitize_text_field(wp_unslash($_REQUEST['categoria'] ?? '')):0),$Stato); 
	albopc_Bonifica_Url();
	if (strpos(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? '')),"?")>0)
		$sep="&amp;";
	else
		$sep="?";
	$titFiltri=get_option('opt_AP_LivelloTitoloFiltri');
	if ($titFiltri=='')
		$titFiltri="h3";
	$HTML='<form id="filtro-atti" action="'.htmlentities(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''))).'" method="post">';
	if (strpos(htmlentities(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''))),'page_id')>0){
		$HTML.= '<input type="hidden" name="page_id" value="'.albopc_Estrai_PageID_Url().'" />';
	}	
	$HTML.= '<input type="hidden" name="categoria" value="'.$cat.'" />
		<div class="container">
        	<div class="row mb-2">
        		<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="ente" class="font-weight-bold">Ente</label>
				</div>
        		<div class="col-12 col-lg-8">				
					'.albopc_get_dropdown_enti("ente","ente","form-control","",(isset($_REQUEST['ente'])?sanitize_text_field(wp_unslash($_REQUEST['ente'] ?? '')):"")).'
				</div>
        	</div>
        	<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="numero" class="font-weight-bold">'.__("Atto", 'albo-pretorio-on-line').'</label>
				</div>
        		<div class="col-12 col-lg-8">				
					<input class="w-50 d-inline" placeholder="N&deg; Atto" type="number" id="numero" name="numero" value="'.(isset($_REQUEST['numero'])?sanitize_text_field(wp_unslash($_REQUEST['numero'] ?? '')):"").'" />
				</div>
			</div>

        	<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="anno" class="font-weight-bold">'.__("Anno", 'albo-pretorio-on-line').'</label> 
				</div>
				<div class="col-12 col-lg-8">				
					'.$anni.'
				</div>
        	</div>	

       		<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="riferimento" class="font-weight-bold">'.__("Riferimento", 'albo-pretorio-on-line').'</label>
				</div>
        		<div class="col-12 col-lg-8">				
					<input type="text" size="40" name="riferimento" id ="riferimento" value="'.(isset($_REQUEST['riferimento'])?sanitize_text_field(wp_unslash($_REQUEST['riferimento'] ?? '')):"").'"/>
				</div>
			</div>
       		<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="oggetto" class="font-weight-bold">'.__("Oggetto", 'albo-pretorio-on-line').'</label>
				</div>
        		<div class="col-12 col-lg-8">				
					<input type="text" size="40" name="oggetto" id ="oggetto" value="'.(isset($_REQUEST['oggetto'])?sanitize_text_field(wp_unslash($_REQUEST['oggetto'] ?? '')):"").'"/>
				</div>
			</div>
       		<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="DataInizio" class="font-weight-bold">'.__("da Data", 'albo-pretorio-on-line').'</label>
				</div>
        		<div class="col-12 col-lg-8">				
					<input name="DataInizio" id="DataInizio" type="date" value="'.htmlentities((isset($_REQUEST['DataInizio'])?sanitize_text_field(wp_unslash($_REQUEST['DataInizio'] ?? '')):"")).'" size="10"/>
				</div>
			</div>
       		<div class="row mb-2">
       			<div class="col-12 col-lg-4 etichetta_filtri">
					<label for="DataFine" class="font-weight-bold">'.__("a Data", 'albo-pretorio-on-line').'</label>
				</div>
        		<div class="col-12 col-lg-8">				
					<input name="DataFine" id="DataFine" type="date" value="'.htmlentities((isset($_REQUEST['DataFine'])?sanitize_text_field(wp_unslash($_REQUEST['DataFine'] ?? '')):"")).'" size="10"/>
				</div>
			</div>
      		<div class="row mt-2">
       			<div class="col col-12 col-lg-6 d-flex justify-content-center">
			      <button type="submit" class="btn btn-primary" name="filtra" id="filtra" value="Filtra">'.__("Filtra", 'albo-pretorio-on-line').'</button>
			    </div>
       			<div class="col col-12 col-lg-6 d-flex justify-content-center">
			      <button type="submit" class="btn btn-outline-primary" name="annullafiltro" id="annullafiltro" value="Annulla Filtro">'.__("Annulla Filtro", 'albo-pretorio-on-line').'</button>
			    </div>
			</div>
 		</div>
	</form>';
	return $HTML;
}

function albopc_get_FiltriCategorie($Stato=1){
	$lista=albopc_get_categorie_gerarchica();
	$HTMLL='<div class="ricercaCategoria">
		<ul class="link-sublist" id="ListaCategorieAlbo">';
	if ($lista){
		foreach($lista as $riga){
		 	$shift=(((int)$riga[2])*15);
	   		$numAtti=albopc_num_atti_categoria($riga[0],$Stato);
		 	if (strpos(get_permalink(),"?")>0)
		  		$sep="&amp;";
	   		else
		   		$sep="?";
	   		if ($numAtti>0)
	      		$HTMLL.='               <li style="text-align:left;padding-left:'.$shift.'px;font-weight: bold;"><a href="'.get_permalink().$sep.'filtra=Filtra&amp;categoria='.$riga[0].'"  >'.$riga[1].'</a> '.$numAtti.'</li>'; 
		}
	}else{
		$HTMLL.= '                <li>'.__("Nessuna Categoria Codificata", 'albo-pretorio-on-line').'</li>';
	}
	$HTMLL.='             </ul>
	</div>';
	return $HTMLL;
}
?>