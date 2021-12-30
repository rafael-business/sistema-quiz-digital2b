<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/public
 * @author     Your Name <email@example.com>
 */
class sistema_assessment_digital2b_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sistema_assessment_digital2b    The ID of this plugin.
	 */
	private $sistema_assessment_digital2b;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $sistema_assessment_digital2b       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $sistema_assessment_digital2b, $version ) {

		$this->sistema_assessment_digital2b = $sistema_assessment_digital2b;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sistema_assessment_digital2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sistema_assessment_digital2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->sistema_assessment_digital2b, plugin_dir_url( __FILE__ ) . 'css/sistema-assessment-digital2b-public.css', array(), $this->version, 'all' );
		/* wp_enqueue_style('bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap-grid.min.css', array(), $this->version, 'all' ); */

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in sistema_assessment_digital2b_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The sistema_assessment_digital2b_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'sistema-assessment-digital2b', plugin_dir_url( __FILE__ ) . 'js/sistema-assessment-digital2b-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'sistema-assessment-digital2b-uploads', plugin_dir_url( __FILE__ ) . 'js/sistema-assessment-digital2b-uploads-public.js?v='.rand(), array( 'jquery' ), $this->version, false );

		wp_localize_script('sistema-assessment-digital2b', 'digital2b_scripts', array(
            'ajax' => admin_url('admin-ajax.php')
			)
		);
		
		wp_localize_script('sistema-assessment-digital2b-uploads', 'digital2b_upload', array(
            'ajax' => admin_url('admin-ajax.php')
			)
		);

		/*

		wp_enqueue_script( 'sistema-assessment-digital2b-frontend-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), '5.1.3', false );

		wp_enqueue_script( 'sistema-assessment-digital2b-frontend-dom', plugin_dir_url( __FILE__ ) . 'frontend/VTM-Wizard/assets/js/modules/constructors.js', array(), $this->version, false );

		wp_enqueue_script( 'sistema-assessment-digital2b-frontend-main', plugin_dir_url( __FILE__ ) . 'frontend/VTM-Wizard/assets/js/main.js', array(), $this->version, false );

		*/

	}

	public function build_assessment_content( $atts ){

		extract( shortcode_atts( array(
			'id'         => 0,
		), $atts ) );

		$manufacturer = $_SESSION["auth-user"]["fabricanteName"] ?? null;

		$assessment_id = $atts['id'];
		$url = get_rest_url( null, 'mount-frontend/'. $assessment_id .'/'. $manufacturer );
		$assessment = wp_remote_request( $url );

		if ( is_array( $assessment ) && ! is_wp_error( $assessment ) ) {

			$headers = $assessment['headers'];
			$body    = $assessment['body'];

			$json = json_decode( $body );

			echo 'TODO: Com essas informações, montar um novo formulários usando Vue.js';

			print '<pre>';
			print_r( $json );
			print '</pre>';

			echo '<p>Se o print acima não deu certo, visite: <br />' . $url . '</p>';

			$modelo = array(
				'ID da Pergunta' => 'Resposta'
			);

			echo 'Modelo de envio POST (answers)';

			print '<pre>';
			print_r( $modelo );
			print '</pre>';
		}

		/*
	
		$questions = 1;
	
		echo '<article class="assessment-digital col-12">
	
			<div class="container">
	
				<div class="assessment-presentation col-lg-8 col-12 offset-xl-2 active-step">
	
					<div class="assessment-box">
	
						<div class="assessment-box-content">
	
							<h1>'.get_the_title($atts['id']).'</h1>
	
							<p class="assessment-description">'.apply_filters( 'the_content', get_post_field( 'post_content', $atts['id'] ) ).'</p>
	
						</div>
	
						<div class="start-assessment assessment-digital-btn">Iniciar assessment</div>
					</div>
				
				</div>
	
				<div class="assessment-question-box"> 
	
					<div class="assessment-question-content steps col-xl-5 col-12">';
					
					$perguntas = carbon_get_post_meta($atts['id'], 'crb_perguntas' );
					$title_resposta = array();
					echo '<form method="POST" action="">';
					foreach($perguntas as $pergunta){
						echo 
								'<div class="step-'.$questions.' step-content" field-type="'.$pergunta['tipo_de_resposta'].'">
									<div class="heading-image">
										'.wp_get_attachment_image( $atts['id'], $pergunta['img'] ).'
									</div>';
									if($pergunta['tipo_de_resposta'] == 'radio_resposta'){
									echo
									'<div class="question-title">
										'.$questions.'. <span>'.$pergunta['titulo'].'</span>
									</div>
	
									<div class="answers-box">';
	
										$n = 0;
										foreach ($pergunta['crb_respostas'] as $resposta){
											//var_dump($resposta);
											echo '<li row-index="'.$n.'" step="'.$questions.'"><div class="customOpt"></div><p>' . $resposta['titulo_radio'] . '</p></li>';        
											$n++;
										}
										
	
									echo '
									</div>';
									}
									if($pergunta['tipo_de_resposta'] == 'checkbox_resposta'){
										echo
										'<div class="question-title">
											'.$questions.'. <span>'.$pergunta['titulo'].'</span>
										</div>
	
										<div class="answers-box">';
											'';
											foreach ($pergunta['crb_respostas'] as $resposta){
												//var_dump($resposta);
												echo '<li step="'.$questions.'"><div><input type="checkbox" name="'.$pergunta['titulo'].'" value="'.$resposta['titulo_checkbox'].'"></div><label>' . $resposta['titulo_checkbox'] . '</label></li>';        
											}
											echo '
										
										</div>';
	
									}
									if($pergunta['tipo_de_resposta'] == 'texto_resposta'){
										echo
										'<div class="question-title">
											'.$questions.'. <span>'.$pergunta['titulo'].'</span>
										</div>
										
											<div class="resposta-texto-class"><input type="text" name="'.$pergunta['titulo'].'" id="resposta_texto_id"></div>    
										';
									}
									if($pergunta['tipo_de_resposta'] == 'imagem_resposta'){
										echo
										'<div class="question-title">
											'.$questions.'. <span>'.$pergunta['titulo'].'</span>
										</div>
										
											<div class="resposta-img"><input type="file" name="'.$pergunta['titulo'].'" id="fileupload"></div>    
										';
									}
									echo
									'</div>';
							$questions++;
					}
	
	
						echo '<div class="finish-assessment-content">
								<h4>Finalizar!</h4>
							</div>
						</form>';
	
						echo '<div class="step-footer">
							<a href="" class="step-btn prev-step"> <img src="'.plugin_dir_url( __FILE__ ) .'/image/arrow.png" alt=""> </a>
							<a href="" class="step-btn next-step"> <img src="'.plugin_dir_url( __FILE__ ) .'/image/arrow.png" alt=""> </a>
							<a href="" class="step-btn finish-assessment" assessmentid="'.$atts['id'].'"> <img src="'.plugin_dir_url( __FILE__ ) .'/image/arrow.png" alt=""> </a>
						</div>
						
					</div>
				</div>
	
				<div class="success_box col-xl-6 col-10">
					<div class="success_content">
						<img src="" alt="">
						<div class="title"></div>
						<div class="description"></div>
					</div>
				</div>
	
				<div class="loading_mask">
				<h2>Aguarde...</h2>
				<p>Estamos gerando seu resultado.</p>
				</div>
			
			</div>
	
		</article>';
	
		echo '<div class="error-step">Selecione uma opção antes de continuar!</div>';
		*/
	
	}

	public function mount_frontend( $get ) {

		$front = array();
		
		$query_assessments = new WP_Query( array(
			'post_type'		=> 'assessment',
			'post_status' 	=> array( 'publish' ),
    		'perm'        	=> 'readable',
			'p'				=> $get['assessment']
		));
		
		if ( $query_assessments->have_posts() ) {

			while ( $query_assessments->have_posts() ) {

				$query_assessments->the_post();
				$front['assessment_title'] 			= get_the_title();
				$front['assessment_description'] 	= get_the_content();
				$front['assessment_type']			= carbon_get_the_post_meta( 'assessment_type' );
				$front['assessment_pontuacoes']		= carbon_get_the_post_meta( 'assessment_pontuacoes' );
				$front['assessment_style']			= array(
					'background'		=> array(
						'type'			=> 'linear-gradient',
						'angle' 		=> carbon_get_the_post_meta( 'back_grad_angle' ),
						'color_start' 	=> carbon_get_the_post_meta( 'back_grad_1' ),
						'color_finish' 	=> carbon_get_the_post_meta( 'back_grad_2' )
					)
				);
				$args_perguntas = array(
					'post_type'		=> 'perguntas',
					'post_status' 	=> array( 'publish' ),
					'fields' 		=> 'ids',
					'meta_query'	=> array(
						array(
							'key' 					=> 'pergunta_assessment',
							'value' 				=> $get['assessment'],
							'carbon_field_property' => 'id'
						),
					)
				);
				$perguntas = get_posts( $args_perguntas );
				if ( $perguntas ) {

					foreach ( $perguntas as $pergunta ) {

						// Pega somente o primeiro módulo, já que só pode ter um
						$modulo_id = carbon_get_post_meta( $pergunta, 'pergunta_modulo' )[0]['id'];
						$modulo = array(
							'id'	=> $modulo_id,
							'name' => get_term( $modulo_id, 'modulo_pergunta' )->name
						);
						
						$front['assessment_perguntas'][$pergunta]['pergunta_title'] = get_the_title( $pergunta );
						$front['assessment_perguntas'][$pergunta]['pergunta_description'] = get_post_field( 'post_content', $pergunta );
						$front['assessment_perguntas'][$pergunta]['pergunta_modulo'] = $modulo;
						$front['assessment_perguntas'][$pergunta]['pergunta_ordem'] = carbon_get_post_meta( $pergunta, 'pergunta_ordem' );
						$front['assessment_perguntas'][$pergunta]['pergunta_img'] = carbon_get_post_meta( $pergunta, 'pergunta_img' );
						$front['assessment_perguntas'][$pergunta]['pergunta_pontos_total'] = carbon_get_post_meta( $pergunta, 'pergunta_pontos_total' );
						$front['assessment_perguntas'][$pergunta]['tipo_de_resposta'] = carbon_get_post_meta( $pergunta, 'tipo_de_resposta' );
						$front['assessment_perguntas'][$pergunta]['respostas'] = carbon_get_post_meta( $pergunta, 'pergunta_resposta' );
						if ( isset( $get['manufacturer'] ) ) {

							$args_respostas = array(
								'post_type'		=> 'respostas',
								'post_status' 	=> array( 'publish' ),
								'fields' 		=> 'ids',
								'meta_query'	=> array(
									array(
										'key' 	=> 'user_manufacturer',
										'value' => $get['manufacturer']
									),
									array(
										'key' 	=> 'respostas/resposta_pergunta',
										'value' => $pergunta,
										'carbon_field_property' => 'id'
									),
								)
							);
							$respostas = get_posts( $args_respostas );

							if ( $respostas ) {

								foreach ( $respostas as $id_grupo_resposta ) {

									$front['assessment_perguntas'][$pergunta]['resposta_manufacturer'] = carbon_get_post_meta( $id_grupo_resposta, 'respostas/resposta' );
								}	
							}
						}
					}
				}
			}
		} else {
			
			
		}
		
		wp_reset_postdata();
		
		return $front;
	}

	public function add_rest_api_routes() {

		register_rest_route( 'mount-frontend', '/(?P<assessment>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'mount_frontend' ),
		));

		register_rest_route( 'mount-frontend', '/(?P<assessment>\d+)/(?P<manufacturer>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'mount_frontend' ),
		));
	}

}
		use Carbon_Fields\Container;
		use Carbon_Fields\Field;


		add_action( 'carbon_fields_register_fields', 'crb_attach_post_meta' );
		function crb_attach_post_meta() {

			
			Container::make( 'post_meta', __( 'Shortcode', 'crb' ) )
			->where( 'post_type', '=', 'assessment' )
		    ->add_fields( array(
		        Field::make( 'text', 'assessment_shortcode', __( 'Copiar shortcode' ) )
					->set_attribute( 'readOnly', true )
					->set_classes('shortcode_copy')
		    ));
			

		   	Container::make( 'post_meta', __( 'Assessment', 'crb' ) )
			->where( 'post_type', '=', 'assessment' )
		    ->add_fields( array(
				Field::make( 'separator', 'data_grad_separator', 'Informações Principais' ),
		        Field::make( 'radio', 'assessment_type', __( 'Tipo de Assessment' ) )
					->set_visible_in_rest_api( true )
					->set_options( array(
						'aderencia' => 'Aderência',
					) ),
				Field::make( 'complex', 'assessment_pontuacoes', 'Pontuações' )
					->set_visible_in_rest_api( true )
					->set_layout( 'tabbed-horizontal' )
					->add_fields( array(
						Field::make('text' , 'tendencia' , 'Tendência de respostas')
							-> set_conditional_logic(array(
								array(
									'field' => 'parent.assessment_type',
									'value' => 'aderencia',
									'compare' => '=',
								)
							)),
						Field::make('rich_text' , 'descricao' , 'Descrição'),
						Field::make( 'image', 'img_pontuacao', 'Imagem' )
							->set_value_type( 'url' )
				)),
				Field::make( 'separator', 'back_grad_separator', 'Estilos: Background - Gradiente Linear' ),
				Field::make( 'color', 'back_grad_1', 'Cor em 0%' )
					->set_visible_in_rest_api( true )
					->set_alpha_enabled( true )
    				->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) ),
				Field::make( 'color', 'back_grad_2', 'Cor em 100%' )
					->set_visible_in_rest_api( true )
					->set_alpha_enabled( true )
    				->set_palette( array( '#FF0000', '#00FF00', '#0000FF' ) ),
				Field::make( 'text', 'back_grad_angle', 'Ângulo do Gradiente' )
					->set_visible_in_rest_api( true )
					->set_attribute( 'type', 'number' )
					->set_attribute( 'min', 0 )
					->set_attribute( 'max', 360 ),
		    ));

			Container::make( 'post_meta', __( 'Pergunta e "Respostas Padrão"', 'crb' ) )
			->where( 'post_type', '=', 'perguntas' )
		    ->add_fields( array(
				Field::make( 'association', 'pergunta_assessment', 'Assessment' )
					->set_visible_in_rest_api( true )
					->set_max( 1 )
					->set_types( array(
						array(
							'type'      => 'post',
							'post_type' => 'assessment',
						)
				)),
				Field::make( 'association', 'pergunta_modulo', 'Módulo' )
					->set_visible_in_rest_api( true )
					->set_max( 1 )
					->set_types( array(
						array(
							'type'      => 'term',
							'taxonomy' 	=> 'modulo_pergunta',
						)
					) ),
				Field::make('text' , 'pergunta_ordem' , 'Ordem')
					->set_visible_in_rest_api( true )
					->set_attribute( 'type', 'number' )
					->set_attribute( 'min', 1 ),
				Field::make( 'image', 'pergunta_img', 'Imagem' )
					->set_value_type( 'url' )
					->set_visible_in_rest_api( true ),
				Field::make('text' , 'pergunta_pontos_total' , 'Total de Pontos nessa Pergunta')
					->set_visible_in_rest_api( true )
					->set_attribute( 'type', 'number' )
					->set_attribute( 'min', 0 ),
				Field::make( 'separator', 'respostas_grad_separator', 'Respostas para esta Pergunta' )
					->set_visible_in_rest_api( false ),
				Field::make( 'select', 'tipo_de_resposta', 'Tipo de resposta' )
					->set_visible_in_rest_api( true )
					->add_options( array(
						'radio_resposta' => 'Única resposta',
						'checkbox_resposta' => 'Várias respostass',
						'texto_resposta' => 'Resposta de texto',
						'imagem_resposta' => 'Imagem'
					) ),
				Field::make( 'complex', 'pergunta_resposta', 'Respostas' )
					->set_visible_in_rest_api( true )
					->set_layout( 'tabbed-horizontal' )
					->add_fields( array(
						Field::make('text' , 'titulo_radio' , 'Titulo')
							-> set_conditional_logic(array(
									array(
										'field' => 'parent.tipo_de_resposta',
										'value' => 'radio_resposta',
										'compare' => '=',
									)
								)),
						Field::make('text' , 'titulo_checkbox' , 'Titulo')
							-> set_conditional_logic(array(
									array(
										'field' => 'parent.tipo_de_resposta',
										'value' => 'checkbox_resposta',
										'compare' => '=',
									)
								)),
						Field::make('text' , 'resposta_pontos' , 'Pontos por esta Resposta')
							->set_attribute( 'type', 'number' )
							->set_attribute( 'min', 0 )
							->set_attribute( 'max', 100 ),
						Field::make('textarea' , 'resposta_description' , 'Descrição')
							->set_rows( 4 )
							->set_visible_in_rest_api( true ),
				) )
		    ));

			

			Container::make( 'post_meta', __( 'Respostas', 'crb' ) )
				->where( 'post_type', '=', 'respostas' )
				->add_fields( array(
					Field::make( 'complex', 'respostas', 'Respostas' )
						->set_visible_in_rest_api( true )
						->add_fields( array(
							Field::make( 'association', 'resposta_pergunta', 'Pergunta' )
								->set_max( 1 )
								->set_types( array(
									array(
										'type'      => 'post',
										'post_type' => 'perguntas',
									)
							)),
							Field::make( 'text', 'resposta', 'Resposta' ),
						) ),
				));

			Container::make( 'post_meta', __( 'Participante', 'crb' ) )
				->where( 'post_type', '=', 'respostas' )
				->add_fields( array(
					Field::make( 'text', 'user_title', 'Nome do participante' )
						->set_visible_in_rest_api( true ),
					Field::make( 'text', 'user_email', 'Email do participante' )
						->set_visible_in_rest_api( true ),
					Field::make( 'text', 'user_manufacturer', 'Fabricante' )
						->set_visible_in_rest_api( true ),
				));
	}



	///////////////////////////////////////////////
	// Lida com imagem anexada no formulário
	///////////////////////////////////////////////

	function image_wp_handle_upload($file_handler) {

		require_once( ABSPATH . 'wp-admin/includes/admin.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/image.php');
		require_once( ABSPATH . "wp-admin" . '/includes/file.php');
		require_once( ABSPATH . "wp-admin" . '/includes/media.php');
	
		$upload = wp_handle_upload( $file_handler, array('test_form' => false ) );
	
		$attachment = array(
		'post_mime_type' => $upload['type'],
		'post_title' => sanitize_file_name(basename($upload['url'])),
		'post_content' => '',
		'post_status' => 'inherit'
		);
	
		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
	
		return $attach_id;
	}

	// Salvando respostas assessmentz

	    function addMetaRespostas() {
	    		$assessmentID = $_POST['assessmentid'];
	    		$formFields = [
	    			$fieldText[] = '',
	    			$fieldCheckbox[] = '',
	    			$fieldImg[] = '',
	    			$fieldRadio[] = '',
	    			$tituloPerguntas[] = '',
	    		];

				/// ATENÇÂO!
				// Precisa pegar o $_POST['answers'] e salvar dentro do post meta
				// Não precisa tratar CADA uma das perguntas, isso a gente deve fazer ( já ta feito ), lá no frontend
				// Assim o Back recebe um array prontinho com titulo e respostas, e só salva aonde for necessário

	    		$meta_values = $_POST['answers'];
	    		$response = array();
	    		foreach ($meta_values as $value) {
	    			$response[] = array(
					    array(
					        'title' => $value['title'],
					        'answers' => $value['answers'],
					    )
					);
	    		}
	    		for ($i=0; $i <= count($response) -1 ; $i++) { 
	    			$response_string = implode(' : ' , $response[$i]);
	    			echo json_encode($response_string);
	    		}
			    $new_post = array(
			    	'post_type' => 'respostas',
			        'post_title'    => 'Teste',
			        'post_content'  => $response_string,
			        'post_status'   => 'publish',
			    );
			    //save the new post
			    $pid = wp_insert_post($new_post);

				// Faz upload da imagem

				// foreach($_POST['file'] as $image){

				// 	$imageUploaded = image_wp_handle_upload( $image );
		  
				// 	// $imageUploaded retorna o ID da imagem que foi anexada a biblioteca
				// 	// para buscar a URL desse anexo, usar:
				// 	// $imageUrl = wp_get_attachment_url($imageUploaded
		  
		  		// }

				//echo json_encode($_POST['answers']);

				wp_die();
	}
	add_action('wp_ajax_nopriv_addMetaRespostas', 'addMetaRespostas');
	add_action('wp_ajax_addMetaRespostas', 'addMetaRespostas');