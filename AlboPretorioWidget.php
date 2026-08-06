<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Widget utilizzato per la pubblicazione degli atti da inserire nell'albo pretorio dell'ente.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- il plugin opera su tabelle custom proprie: nessuna API core equivalente, il caching non si applica alle query amministrative e di scrittura.

class AlboPretorioWidget extends WP_Widget
{
	public function __construct()
	{
	   parent::__construct( 'AlboPretorio', 'Albo On Line', array('description' => __('Grazie a questo widget è possibile visualizzare sulla sidebar le ultime pubblicazioni dell Albo Pretorio','albo-pretorio-considera'),array( 'width' => 300, 'height' => 350)));
	 }
    
	public function form($instance)
    {
    
	 $defaults = array(
 		'titolo_statistiche' => __('Dati Atti','albo-pretorio-considera'),
        'titolo_elenco' => __('Atti Correnti','albo-pretorio-considera'),
        'numero_atti' => 5,
        'pagina_albo' => NULL,
        'ordine_campo' => NULL,
		'ordinamento' => 'C'
        );
        $instance = wp_parse_args( (array) $instance, $defaults );?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'titolo' )); ?>">
                <?php echo esc_html__('Titolo widget','albo-pretorio-considera');?>:
            </label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'titolo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'titolo' )); ?>" value="<?php echo esc_attr($instance['titolo']); ?>" size="30" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'titolo_statistiche' )); ?>">
                <?php echo esc_html__('Titolo cartella dati atti correnti','albo-pretorio-considera');?>:
            </label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'titolo_statistiche' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'titolo_statistiche' )); ?>" value="<?php echo esc_attr($instance['titolo_statistiche']); ?>" size="30" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'titolo_elenco' )); ?>">
                <?php echo esc_html__('Titolo lista atti correnti','albo-pretorio-considera');?>:
            </label>
             <input type="text" id="<?php echo esc_attr($this->get_field_id( 'titolo_elenco' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'titolo_elenco' )); ?>" value="<?php echo esc_attr($instance['titolo_elenco']); ?>" size="30" />
        </p>        
		<p>
            <label for="<?php echo esc_attr($this->get_field_id( 'numero_atti' )); ?>">
                <?php echo esc_html__('Numero Atti da visualizzare','albo-pretorio-considera');?>:
            </label>
            <input type="text" id="<?php echo esc_attr($this->get_field_id( 'numero_atti' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'numero_atti' )); ?>" value="<?php echo esc_attr($instance['numero_atti']); ?>" size="2"/>

        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'pagina_albo' )); ?>">
               <?php echo esc_html__('Pagina Albo','albo-pretorio-considera');?>:
            </label>
		<select id="<?php echo esc_attr($this->get_field_id( 'pagina_albo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'pagina_albo' )); ?>"> 
		 <option value=""><?php echo esc_attr( __( 'Seleziona la pagina', 'albo-pretorio-considera' ) ); ?></option> 
		 <?php 
		  $pages = get_pages(); 
		  foreach ( $pages as $pagg ) {
		    if (get_page_link( $pagg->ID ) == $instance['pagina_albo'] ) 
				$Selezionato= 'selected="selected"';
			else
				$Selezionato="";
		  	$option = '<option '.$Selezionato.' value="' . esc_url( get_page_link( $pagg->ID ) ) . '">';
			$option .= esc_html( $pagg->post_title );
			$option .= '</option>';
			echo $option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <option> con url/titolo gia' escapati
		  }
		 ?>
		</select>
        </p>
		<h3><?php echo esc_html__('Ordine Elenco','albo-pretorio-considera');?></h3>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'ordine_campo' )); ?>">
               <?php echo esc_html__('In base a','albo-pretorio-considera');?>:
            </label>
		<select id="<?php echo esc_attr($this->get_field_id( 'ordine_campo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ordine_campo' )); ?>"> 
		 <option value="Pubblicazione" <?php if ($instance['ordine_campo']=="Pubblicazione") echo 'selected="selected"'?> ><?php echo esc_html__('Data Pubblicazione','albo-pretorio-considera');?> </option>
		 <option value="Scadenza" <?php if ($instance['ordine_campo']=="Scadenza") echo 'selected="selected"'?> ><?php echo esc_html__('Data Scadenza','albo-pretorio-considera');?> </option>
		</select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'ordinamento' )); ?>">
               <?php echo esc_html__('Ordine','albo-pretorio-considera');?>:
            </label>
		<select id="<?php echo esc_attr($this->get_field_id( 'ordinamento' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ordinamento' )); ?>"> 
		 <option value="C" <?php if ($instance['ordinamento']=="C") echo 'selected="selected"'?> ><?php echo esc_html__('Crescente','albo-pretorio-considera');?> </option>
		 <option value="D" <?php if ($instance['ordinamento']=="D") echo 'selected="selected"'?> ><?php echo esc_html__('Decrescente','albo-pretorio-considera');?> </option>
		</select>
        </p>


       <?php
    }


public function widget( $args, $instance )
    {
		global $wpdb;

        extract( $args );

        $titolo = apply_filters('widget_title', $instance['titolo'] );
 		if ($titolo=='')
			$titolo="Albo Pretorio";
		$n_atti_attivi = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= CURDATE() And DataFine>= CURDATE() And Numero>0;");
		$n_atti_attivi_annullati = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->table_name_Atti Where DataInizio <= now() And DataFine>= now() And Numero>0 And DataAnnullamento<>'0000-00-00';");
        echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup wrapper del widget fornito dalla sidebar/tema
        echo $before_title . esc_html($titolo) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $before_title/$after_title sono markup wrapper del tema; $titolo escapato
        echo "<div>";

    if ($instance['ordine_campo']=="Pubblicazione")
    	$Ordinamento="DataInizio";
    else
    	$Ordinamento="DataFine";
	
    if ($instance['ordinamento']=="C") 
    	$Ordinamento.=" ASC";
    else
    	$Ordinamento.=" DESC";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$lista=albopc_get_all_atti(1,0,0,0,'',0,0,$Ordinamento,0,$instance['numero_atti']); 

	$HtmlW='<ul>';
	$CeAnnullato=FALSE;
	if ($lista){
		foreach($lista as $riga){
			if($riga->DataAnnullamento!='0000-00-00'){
				$Annullato='style="background-color: '.esc_attr($coloreAnnullati).';"';
				$CeAnnullato=true;
			}else
				$Annullato='';
			if (strpos($instance['pagina_albo'],"?")>0)
				$sep="&";
			else
				$sep="?";
			$HtmlW.= '<li '.$Annullato.'> <a href="'.esc_url($instance['pagina_albo'].$sep.'action=visatto&id='.$riga->IdAtto).'">'.esc_html(stripcslashes($riga->Oggetto)) .'</a><br />
				</li>';
		}
	} else {
			$HtmlW.= '<li>
					'. esc_html__('Nessun Atto Codificato','albo-pretorio-considera').'
				  </li>';
	}
	$HtmlW.= '</ul>';
	if ($CeAnnullato)
		$HtmlW.= '<p>'. esc_html__('Le righe evidenziate con questo sfondo','albo-pretorio-considera').' <span style="background-color: '.esc_attr($coloreAnnullati).';">&nbsp;&nbsp;&nbsp;</span> '. esc_html__('indicano Atti Annullati','albo-pretorio-considera').'</p>';
$HtmlW.= '</div>';
?>
			<div id="pp-tabs-container">
				<ul>
					<li><a href="#pp-tab-1"><?php echo esc_html($instance["titolo_statistiche"]); ?></a></li>
					<li><a href="#pp-tab-2"><?php echo esc_html($instance["titolo_elenco"]); ?></a></li>
				</ul>
				<div id="pp-tab-1">
                    <p>
				        <?php echo esc_html__('Atti Correnti','albo-pretorio-considera');?> <?php echo (int)$n_atti_attivi; ?><br />
				        <?php echo esc_html__('di cui Annullati','albo-pretorio-considera');?> <?php echo (int)$n_atti_attivi_annullati; ?>
				    </p>
                </div>
				<div id="pp-tab-2">
                      <?php echo $HtmlW; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup lista widget con url/oggetto/colori gia' escapati ?>
				</div>			
			</div>
<?php
	   echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup wrapper del widget fornito dalla sidebar/tema
    }

	public function update( $new_instance, $old_instance )
	{
			$instance = $old_instance;
	
	        $instance['titolo'] = wp_strip_all_tags( $new_instance['titolo'] );
	        $instance['titolo_statistiche'] = wp_strip_all_tags( $new_instance['titolo_statistiche'] );
	        $instance['titolo_elenco'] = wp_strip_all_tags( $new_instance['titolo_elenco'] );
	        $instance['numero_atti'] = wp_strip_all_tags( $new_instance['numero_atti'] );
	        $instance['pagina_albo'] = wp_strip_all_tags( $new_instance['pagina_albo'] );
	        $instance['ordine_campo'] = wp_strip_all_tags( $new_instance['ordine_campo'] );
	        $instance['ordinamento'] = wp_strip_all_tags( $new_instance['ordinamento'] );
	        
			return $instance;
	}
}	


class AlboPretorioElencoAttiCorrentiWidget extends WP_Widget
{
	public function __construct()
	{
	   parent::__construct( 'AlboOnLineAC', 'Albo On Line Atti Correnti', array('description' => __("Grazie a questo widget è possibile visualizzare gli atti correnti dell'Albo Pretorio","albo-pretorio-considera"),array( 'width' => 300, 'height' => 350)));
	 }
    
        public function form($instance)
        {
         $defaults = array(
             'titolo' => __("Albo On Line Ultimi Atti","albo-pretorio-considera"),
            'numero_atti' => 5,
            'pagina_albo' => NULL,
            'ordine_campo' => NULL,
            'ordinamento' => 'C'
            );
            $instance = wp_parse_args( (array) $instance, $defaults );?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'titolo' )); ?>">
                    <?php echo esc_html__("Titolo widget","albo-pretorio-considera"); ?>:
                </label>
                <input type="text" id="<?php echo esc_attr($this->get_field_id( 'titolo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'titolo' )); ?>" value="<?php echo esc_attr($instance['titolo']); ?>" size="30" />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'numero_atti' )); ?>">
                    <?php echo esc_html__("Numero Atti da visualizzare","albo-pretorio-considera"); ?>:
                </label>
                <input type="text" id="<?php echo esc_attr($this->get_field_id( 'numero_atti' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'numero_atti' )); ?>" value="<?php echo esc_attr($instance['numero_atti']); ?>" size="2"/>

            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'pagina_albo' )); ?>">
                   <?php echo esc_html__("Pagina Albo","albo-pretorio-considera"); ?>:
                </label>
            <select id="<?php echo esc_attr($this->get_field_id( 'pagina_albo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'pagina_albo' )); ?>">
             <option value=""><?php echo esc_attr( __( 'Seleziona la pagina', 'albo-pretorio-considera' )  ); ?></option>
             <?php
              $pages = get_pages();
              foreach ( $pages as $pagg ) {
                if (get_page_link( $pagg->ID ) == $instance['pagina_albo'] )
                    $Selezionato= 'selected="selected"';
                else
                    $Selezionato="";
                  $option = '<option '.$Selezionato.' value="' . esc_url( get_page_link( $pagg->ID ) ) . '">';
                $option .= esc_html( $pagg->post_title );
                $option .= '</option>';
                echo $option; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <option> con url/titolo gia' escapati
              }
             ?>
            </select>
            </p>
            <h3><?php echo esc_html__("Ordine Elenco","albo-pretorio-considera"); ?></h3>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'ordine_campo' )); ?>">
                   <?php echo esc_html__("In base a","albo-pretorio-considera"); ?>:
                </label>
            <select id="<?php echo esc_attr($this->get_field_id( 'ordine_campo' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ordine_campo' )); ?>">
             <option value="Pubblicazione" <?php if ($instance['ordine_campo']=="Pubblicazione") echo 'selected="selected"'?> ><?php echo esc_html__("Data Pubblicazione","albo-pretorio-considera"); ?> </option>
             <option value="Scadenza" <?php if ($instance['ordine_campo']=="Scadenza") echo 'selected="selected"'?> ><?php echo esc_html__("Data Scadenza","albo-pretorio-considera"); ?> </option>
            </select>
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'ordinamento' )); ?>">
                   <?php echo esc_html__("Ordine","albo-pretorio-considera"); ?>:
                </label>
            <select id="<?php echo esc_attr($this->get_field_id( 'ordinamento' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'ordinamento' )); ?>">
             <option value="C" <?php if ($instance['ordinamento']=="C") echo 'selected="selected"'?> ><?php echo esc_html__("Crescente","albo-pretorio-considera"); ?> </option>
             <option value="D" <?php if ($instance['ordinamento']=="D") echo 'selected="selected"'?> ><?php echo esc_html__("Decrescente","albo-pretorio-considera"); ?> </option>
            </select>
            </p>
           <?php
        }


    public function widget( $args, $instance )
        {
            global $wpdb;

            extract( $args );

            $titolo = apply_filters('widget_title', $instance['titolo'] );
             if ($titolo=='')
                $titolo=__("Albo OnLine","albo-pretorio-considera");
            echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup wrapper del widget fornito dalla sidebar/tema
            echo $before_title . esc_html($titolo) . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $before_title/$after_title sono markup wrapper del tema; $titolo escapato
            echo "<div>";

        if ($instance['ordine_campo']=="Pubblicazione")
            $Ordinamento="DataInizio";
        else
            $Ordinamento="DataFine";

        if ($instance['ordinamento']=="C")
            $Ordinamento.=" ASC";
        else
            $Ordinamento.=" DESC";
        $coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
        $lista=albopc_get_all_atti(1,0,0,0,'',0,0,$Ordinamento,0,$instance['numero_atti']);
        $HtmlW='<ul>';
        $CeAnnullato=false;
        if ($lista){
            foreach($lista as $riga){
                if($riga->DataAnnullamento!='0000-00-00'){
                    $Annullato='style="background-color: '.esc_attr($coloreAnnullati).';"';
                    $CeAnnullato=true;
                }else
                    $Annullato='';
                if (strpos($instance['pagina_albo'],"?")>0)
                    $sep="&";
                else
                    $sep="?";
                $HtmlW.= '<li><h3><span class="dataAtto">'.esc_html(date_i18n("j M y", strtotime($riga->DataInizio))).'</span> - <span class="dataAtto">'.esc_html(date_i18n("j M y", strtotime($riga->DataFine))).'</span> <a href="'.esc_url($instance['pagina_albo'].$sep.'action=visatto&id='.$riga->IdAtto).'"'.$Annullato.'>'.esc_html(stripcslashes($riga->Oggetto)) .'</a></h3>
                    </li>';
            }
        } else {
                $HtmlW.= '<li>
                        '. esc_html__("Nessun Atto Codificato","albo-pretorio-considera").'
                      </li>';
        }
        $HtmlW.= '</ul>';
        if ($CeAnnullato)
            $HtmlW.= '<p>'. esc_html__("Le righe evidenziate con questo sfondo","albo-pretorio-considera").' <span style="background-color: '.esc_attr($coloreAnnullati).';">&nbsp;&nbsp;&nbsp;</span> '. esc_html__("indicano Atti Annullati","albo-pretorio-considera").'</p>';
    $HtmlW.= '</div>';
    ?>
                <div>
                  <?php echo $HtmlW; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup lista widget con url/oggetto/colori gia' escapati ?>
                </div>
    <?php
           echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup wrapper del widget fornito dalla sidebar/tema
        }

        public function update( $new_instance, $old_instance )
        {
                $instance = $old_instance;

                $instance['titolo'] = wp_strip_all_tags( $new_instance['titolo'] );
                $instance['numero_atti'] = wp_strip_all_tags( $new_instance['numero_atti'] );
                $instance['pagina_albo'] = wp_strip_all_tags( $new_instance['pagina_albo'] );
                $instance['ordine_campo'] = wp_strip_all_tags( $new_instance['ordine_campo'] );
                $instance['ordinamento'] = wp_strip_all_tags( $new_instance['ordinamento'] );

                return $instance;
        }

}	

function albopc_AlboWidget_register()
{
    register_widget( 'AlboPretorioWidget' );
    register_widget( 'AlboPretorioElencoAttiCorrentiWidget');
}
function albopc_AlboWidget_required_scripts()
{
    // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter -- caricato in head: il markup del widget inizializza i tab jQuery UI inline prima del footer.
    wp_enqueue_script('AlboPretorio-tabs', Albo_URL . 'js/Albo.jquery.tabs.js', array('jquery-ui-tabs'), AP_VERSION);
    wp_enqueue_style('AlboPretorio-ui-style', Albo_URL . 'css/jquery-ui-custom.css', array(), AP_VERSION);
}
add_action('wp_enqueue_scripts', 'albopc_AlboWidget_required_scripts');
add_action( 'widgets_init', 'albopc_AlboWidget_register' );
?>