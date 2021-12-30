<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    sistema_assessment_digital2b
 * @subpackage sistema_assessment_digital2b/includes
 * @author     Your Name <email@example.com>
 */
class sistema_assessment_digital2b_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	protected $shortcodes;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();
		$this->shortcodes = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	public function add_shortcode( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->shortcodes = $this->add( $this->shortcodes, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->shortcodes as $hook ) {
			add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		add_filter( 'template_include', 'single_assessment_template' );

		function single_assessment_template( $template )
		{
			if ( 'assessment' === get_post_type() )
				return dirname( __FILE__ ) . '/templates/single-assessment.php';

			return $template;
		}


		function calculate_assessment_answers() {

				$points = 0;
				$assessmentID = $_POST['assessmentid'];
				$answers = $_POST['answers'];
	            $questionARR = array();
	            // RESPONSE
	            $response = array();
	            $responsDescription = '';
	            $responsImage = '';
	            $responsPoints = '';

	            if(
	                carbon_get_post_meta($assessmentID , 'crb_tipo_de_assessment') == 'pontuacao' || 
	                carbon_get_post_meta( $assessmentID , 'crb_tipo_de_assessment') == '' || 
	                !carbon_get_post_meta($assessmentID ,'crb_tipo_de_assessment')
	            ) {

	            	$perguntas = carbon_get_post_meta( $assessmentID ,'crb_perguntas' );
	            	foreach ($perguntas as $pergunta) {
	            		$row = 0;
			            foreach ($pergunta['crb_respostas'] as $resposta) {
			            	if($resposta['resposta_correta']){
			            		array_push($questionARR, $row);
			            	}
			            	$row++;
			        	}
			        }
			        //array(1 , 0 , 3);
			        //var_dump($questionARR);
	                for($i = 0; $i <= count($answers) - 1; $i++){

	                    if(intval($answers[$i]) == intval($questionARR[$i])){
	                        $points++;
	                    }
	                }
	                //var_dump($answers);
	                $pontuacoes = carbon_get_post_meta( $assessmentID ,'crb_pontuacoes' );
	                	foreach ($pontuacoes as $pontuacao){
	                		if(intval($points) >= intval($pontuacao['pontuacao_minima']) AND intval($points) <= intval($pontuacao['pontuacao_maxima'])){
	                			$response['type'] = 'pontuacoes';
	                            $response['responseDescription'] = $pontuacao['descricao'];
	                            $response['responseImage'] = $pontuacao['img_pontuacao'];
	                            $response['responsePoints'] = $points;

	                            echo json_encode($response);

	                            wp_die();
	                		}
	                	}
	            	}else{
		                // Get the most selected value
		                $result = array_count_values($answers);
		                $indexOf = array_search(max($result), $result);
		                $rowIndex = $indexOf;

		                $rows = carbon_get_post_meta( $assessmentID ,'crb_pontuacoes' );
		                $specific_row = $rows[$rowIndex];
		                $sub_description = $specific_row['descricao'];
		                $sub_image = $specific_row['img_pontuacao'];

		                $response['type'] = 'aderencia';
		                $response['responseDescription'] = $sub_description;
		                $response['responseImage'] = $sub_image;

		                echo json_encode($response);

		                wp_die();

		            }
		            

				}

			add_action('wp_ajax_nopriv_calculate_assessment_answers', 'calculate_assessment_answers');
			add_action('wp_ajax_calculate_assessment_answers', 'calculate_assessment_answers');

	}

}