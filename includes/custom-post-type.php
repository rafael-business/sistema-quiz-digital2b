<?php

/* CPT */

function cpt_assessment() {

    $labels = array(
        'name'                  => _x( 'Assessments', 'taxonomy general name', 'sistema-assessment-digital2b' ),
        'singular_name'         => _x( 'Assessment', 'taxonomy singular name', 'sistema-assessment-digital2b' ),
        'add_new'               => __( 'Novo Assessment', 'sistema-assessment-digital2b' ),
        'add_new_item'          => __( 'Adicionar novo Assessment', 'sistema-assessment-digital2b' ),
        'edit_item'             => __( 'Editar assessment', 'sistema-assessment-digital2b' ),
        'new_item'              => __( 'Novo assessment', 'sistema-assessment-digital2b' ),
        'all_items'             => __( 'Assessments', 'sistema-assessment-digital2b' ),
        'view_item'             => __( 'Ver assessment', 'sistema-assessment-digital2b' ),
        'search_items'          => __( 'Buscar Assessments', 'sistema-assessment-digital2b' ),
        'not_found'             => __( 'Nenhum assessment encontrado', 'sistema-assessment-digital2b' ),
        'not_found_in_trash'    => __( 'Nenhum assessment encontrado', 'sistema-assessment-digital2b' ),
        'menu_name'             => __( 'Assessments', 'sistema-assessment-digital2b' )
    );
    
    // register post type
    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'has_archive'       => true,
        'show_ui'           => true,
        'capability_type'   => 'post',
        'hierarchical'      => false,
        'rewrite'           => array('slug' => 'assessment'),
        'query_var'         => true,
        'menu_icon'         => 'dashicons-clipboard',
        'supports'          => array( 'title', 'author', 'editor' ),
        'show_in_rest'      => true
    );

    register_post_type( 'assessment', $args );

}

add_action( 'init', 'cpt_assessment' );

function cpt_perguntas() {

    $labels = array(
        'name'                  => _x( 'Perguntas', 'taxonomy general name', 'sistema-assessment-digital2b' ),
        'singular_name'         => _x( 'Pergunta', 'taxonomy singular name', 'sistema-assessment-digital2b' ),
        'add_new'               => __( 'Nova Pergunta', 'sistema-assessment-digital2b' ),
        'add_new_item'          => __( 'Adicionar nova Pergunta', 'sistema-assessment-digital2b' ),
        'edit_item'             => __( 'Editar Pergunta', 'sistema-assessment-digital2b' ),
        'new_item'              => __( 'Nova Pergunta', 'sistema-assessment-digital2b' ),
        'all_items'             => __( 'Perguntas', 'sistema-assessment-digital2b' ),
        'view_item'             => __( 'Ver Pergunta', 'sistema-assessment-digital2b' ),
        'search_items'          => __( 'Buscar Perguntas', 'sistema-assessment-digital2b' ),
        'not_found'             => __( 'Nenhuma Pergunta encontrada', 'sistema-assessment-digital2b' ),
        'not_found_in_trash'    => __( 'Nenhuma Pergunta encontrada na lixeira', 'sistema-assessment-digital2b' ),
        'menu_name'             => __( 'Perguntas', 'sistema-assessment-digital2b' )
    );
    
    // register post type
    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'has_archive'       => true,
        'show_ui'           => true,
        'capability_type'   => 'post',
        'hierarchical'      => false,
        'rewrite'           => array('slug' => 'perguntas'),
        'query_var'         => true,
        'menu_icon'         => 'dashicons-editor-ol',
        'supports'          => array( 'title', 'author', 'editor' ),
        'show_in_rest'      => true
    );

    register_post_type( 'perguntas', $args );
    register_modulo_pergunta_taxonomy();

}

add_action( 'init', 'cpt_perguntas' );

function register_modulo_pergunta_taxonomy() {
 
    $labels = array(
        'name'              => _x( 'Módulos', 'taxonomy general name', 'sistema-assessment-digital2b' ),
        'singular_name'     => _x( 'Módulo', 'taxonomy singular name', 'sistema-assessment-digital2b' ),
        'search_items'      => __( 'Procurar Módulos', 'sistema-assessment-digital2b' ),
        'all_items'         => __( 'Todas as Módulos', 'sistema-assessment-digital2b' ),
        'view_item'         => __( 'Ver Módulo', 'sistema-assessment-digital2b' ),
        'parent_item'       => __( 'Módulo Pai', 'sistema-assessment-digital2b' ),
        'parent_item_colon' => __( 'Módulo Pai:', 'sistema-assessment-digital2b' ),
        'edit_item'         => __( 'Editar Módulo', 'sistema-assessment-digital2b' ),
        'update_item'       => __( 'Atualizar Módulo', 'sistema-assessment-digital2b' ),
        'add_new_item'      => __( 'Adicionar Módulo', 'sistema-assessment-digital2b' ),
        'new_item_name'     => __( 'Nome da nova Módulo', 'sistema-assessment-digital2b' ),
        'not_found'         => __( 'Nenhuma Módulo encontrada', 'sistema-assessment-digital2b' ),
        'back_to_items'     => __( 'Voltar à Módulos', 'sistema-assessment-digital2b' ),
        'menu_name'         => __( 'Módulos', 'sistema-assessment-digital2b' ),
    );
 
    $args = array(
        'labels'            => $labels,
        'public'            => false,
        'show_ui'           => true
    );
 
 
    register_taxonomy( 'modulo_pergunta', 'perguntas', $args );
 
}

/*****************************************************/


function cpt_answers_assessment() {

    $labels = array(
        'name'                  => 'Respostas',
        'singular_name'         => 'Resposta',
        'add_new'               => 'Novo Grupo',
        'add_new_item'          => 'Adicionar novo grupo de Respostas',
        'edit_item'             => 'Editar Resposta',
        'new_item'              => 'Nova Resposta',
        'all_items'             => 'Grupos de Respostas',
        'view_item'             => 'Ver Resposta',
        'search_items'          => 'Buscar Respostas',
        'not_found'             => 'Nenhuma resposta encontrada',
        'not_found_in_trash'    => 'Nenhuma resposta encontrada na lixeira', 
        'parent_item_colon'     => '',
        'menu_name'             => 'Respostas',
    );
    
    $args = array(
        'labels'            => $labels,
        'public'            => true,
        'has_archive'       => true,
        'show_ui'           => true,
        'capability_type'   => 'post',
        'hierarchical'      => false,
        'rewrite'           => array('slug' => 'respostas'),
        'query_var'         => true,
        'menu_icon'         => 'dashicons-editor-ol-rtl',
        'supports'          => array( 'title', 'author' ),
        'show_in_rest'      => true
    );

    register_post_type( 'respostas', $args );

}

add_action( 'init', 'cpt_answers_assessment' );
