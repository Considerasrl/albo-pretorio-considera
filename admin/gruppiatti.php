<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }

$albopc_ret=albopc_Lista_AttiGruppo($Parametri);								  
function albopc_Lista_AttiGruppo($Parametri){
	ob_start();
	$lista=albopc_get_GruppiAtti($Parametri['meta'],$Parametri['valore']); 
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$colorePari=get_option('opt_AP_ColorePari');
	$coloreDispari=get_option('opt_AP_ColoreDispari');
    $FEColsOption=get_option('opt_AP_ColonneFE',array(
									"Ente"=>0,
									"Riferimento"=>0,
									"Oggetto"=>0,
									"Validita"=>0,
									"Categoria"=>0,
									"Note"=>0,
									"DataOblio"=>0));
  	$PaginaAttiCor=get_option('opt_AP_PAttiCor');
  	$PaginaAttiSto=get_option('opt_AP_PAttiSto');
	if(!is_array($FEColsOption)){
		$FEColsOption=json_decode($FEColsOption,TRUE);
	}	
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- template di rendering: markup fisso intercalato a valori dinamici escapati singolarmente (esc_html sui testi DB, esc_url sugli href, esc_attr sui colori-opzione). Le variabili $classe/$Link contengono solo markup fisso + dati gia' escapati.
	echo '	<div class="tabalbo" style="margin-bottom:10px;">
		<h3>'.esc_html($Parametri['titolo']).'</h3>
		<table id="elenco-atti-OldStyle" class="tabella-dati-albo" summary="'. esc_attr__("atti validi per riferimento, oggetto e categoria","albo-pretorio-on-line").'">
	    <caption>'. esc_html__("Atti","albo-pretorio-on-line").'</caption>
		<thead>
	    	<tr>
				<th scope="col">'. esc_html__("Stato","albo-pretorio-on-line").'</th>
	        	<th scope="col">'. esc_html__("Prog.","albo-pretorio-on-line").'</th>';
	foreach($FEColsOption as $Opzione => $Valore){
		if($Opzione=="Validita") $Opzione="Validità";
		if($Opzione=="DataOblio") $Opzione="Data Oblio";
		if($Valore==1){
			echo '			<th scope="col">'.esc_html($Opzione).'</th>';
		}
	}
	echo '	</tr></thead><tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		if (strpos(get_permalink(),"?")>0)
			$sep="&amp;";
		else
			$sep="?";
		foreach($lista as $riga){
			$categoria=albopc_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$NumeroAtto=albopc_get_num_anno($riga->IdAtto);
	//		Bonifica_Url();
			$classe='';
			if ($pari And $coloreDispari)
				$classe='style="background-color: '.esc_attr($coloreDispari).';"';
			if (!$pari And $colorePari)
				$classe='style="background-color: '.esc_attr($colorePari).';"';
			$pari=!$pari;
			if($riga->DataAnnullamento!='0000-00-00'){
				$classe='style="background-color: '.esc_attr($coloreAnnullati).';"';
				$CeAnnullato=true;
			}
			$Stato=__("Scaduto","albo-pretorio-on-line");
			if ($riga->DataFine>gmdate("Y-m-d")){
				$Stato=__("Corrente","albo-pretorio-on-line");
				$Link='<a href="'.esc_url($PaginaAttiCor.$sep.'action=visatto&id='.$riga->IdAtto).'"  style="text-decoration: underline;">';
			}else{
				$Link='<a href="'.esc_url($PaginaAttiSto.$sep.'action=visatto&id='.$riga->IdAtto).'"  style="text-decoration: underline;">';
			}
			echo '<tr >
					<td '.$classe.'>'.esc_html($Stato).'</td>
			        <td '.$classe.'>'.$Link.esc_html($NumeroAtto).'/'.esc_html($riga->Anno) .'</a>
					</td>';
			if (isset($FEColsOption['Data']) And $FEColsOption['Data']==1)
				echo '
					<td '.$classe.'>
						'.esc_html(albopc_VisualizzaData($riga->Data)) .'</a>
					</td>';
			if (isset($FEColsOption['Ente']) And $FEColsOption['Ente']==1)
				echo '
					<td '.$classe.'>
						'.$Link.$Link.esc_html(stripslashes(albopc_get_ente($riga->Ente)->Nome)) .'</a>
					</td>';
			if (isset($FEColsOption['Riferimento']) And $FEColsOption['Riferimento']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html(stripslashes($riga->Riferimento)) .'</a>
					</td>';
			if (isset($FEColsOption['Oggetto']) And $FEColsOption['Oggetto']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html(stripslashes($riga->Oggetto)) .'</a>
					</td>';
			if (isset($FEColsOption['Validita']) And $FEColsOption['Validita']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html(albopc_VisualizzaData($riga->DataInizio)) .'<br />'.esc_html(albopc_VisualizzaData($riga->DataFine)) .'</a>
					</td>';
			if (isset($FEColsOption['Categoria']) And $FEColsOption['Categoria']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html($cat) .'</a>
					</td>';
			if (isset($FEColsOption['Note']) And $FEColsOption['Note']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html(stripslashes($riga->Informazioni)) .'</a>
					</td>';
			if (isset($FEColsOption['DataOblio']) And $FEColsOption['DataOblio']==1)
				echo '
					<td '.$classe.'>
						'.$Link.esc_html(albopc_VisualizzaData($riga->DataOblio)) .'</a>
					</td>';
		echo '	
				</tr>'; 
			}
	} else {
			echo '<tr>
					<td colspan="6">'. esc_html__("Nessun Atto Codificato","albo-pretorio-on-line").'</td>
				  </tr>';
	}
	echo '
     </tbody>
    </table>';
echo '</div>';
	if ($CeAnnullato)
		echo '<p>'. sprintf(/* translators: %s: quadratino colorato di esempio */ esc_html__('Le righe evidenziate con questo sfondo %s indicano Atti Annullati','albo-pretorio-on-line'),' <span style="background-color: '.esc_attr($coloreAnnullati).';">&nbsp;&nbsp;&nbsp;</span>').'</p>';
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
return ob_get_clean();
}
?>