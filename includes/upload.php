<?php



///////////////////////////////////////////////
// Lida com imagem anexada no formulário
///////////////////////////////////////////////

function custom_image_wp_handle_upload($file_handler) {

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

    function saveassessmentContent() {

        session_start();

        $assessmentID = $_POST['assessmentid'];
        $answers = $_POST['answers'];
        $uploadedfile = $_FILES;

        // Faz upload da imagem
        foreach($uploadedfile as $key => $image){
            if($image['name'] !== ''){
                $imageUploaded = custom_image_wp_handle_upload( $image );
                $imageUrl = wp_get_attachment_url($imageUploaded);
                $answers[][$key] = $imageUrl;
            }
        }

        $post_id = wp_insert_post(array (
            'post_type' => 'respostas',
            'post_title' => date("d-m-Y-G:i:s"),
            'post_content' => '',
            'post_status' => 'publish'
        ));
        
        // Save assessment Title 
        //carbon_set_post_meta( $post_id, 'answered_assessment', get_the_title( $assessmentID ) );

        // Set user informations
        if($_SESSION["auth-user"]) {
            carbon_set_post_meta( $post_id, 'user_title', $_SESSION["auth-user"]["name"] );
            carbon_set_post_meta( $post_id, 'user_email', $_SESSION["auth-user"]["email"] );
            carbon_set_post_meta( $post_id, 'user_manufacturer', $_SESSION["auth-user"]["fabricanteName"] );

            $to = $_SESSION["auth-user"]["email"];
            $subject = 'Obrigado por preencher o assessment';
            $body = 'Você participou do assessment ' . get_the_title($assessmentID);
            $headers = array('Content-Type: text/html; charset=UTF-8');
     
            wp_mail( $to, $subject, $body, $headers );
        }
    
        // Set assessment relation
        carbon_set_post_meta(
            $post_id,
            'assessment_relationship',
            array(
                array(
                    'value' => 'post:assessment' . $assessmentID,
                    'id' => $assessmentID,
                    'type' => 'post',
                    'subtype' => 'assessment'
                )
            )
        );

        // Save assessment answers
        $complex_row = 0;
        foreach($answers as $answer) {
            carbon_set_post_meta( $post_id, 'respostas['.$complex_row.']/resposta_pergunta', key($answer) );
            carbon_set_post_meta( $post_id, 'respostas['.$complex_row.']/resposta', $answer[key($answer)] );
            $complex_row++;
        }

       

        echo $post_id;

        wp_die();
}
add_action('wp_ajax_nopriv_saveassessmentContent', 'saveassessmentContent');
add_action('wp_ajax_saveassessmentContent', 'saveassessmentContent');
