<?php 

// Template do assessment

if ( isset( $_POST ) && !empty( $_POST ) ) : 

    $id = isset( $_POST['id_resposta'] ) && !empty( $_POST['id_resposta'] ) ? $_POST['id_resposta'] : null;

    $args = array(
        'post_type'    => 'respostas',
        'post_status'  => 'publish',
        'post_title'   => 'Resposta à pergunta #'. $_POST['resposta_pergunta']
    );

    $action = 'insert';
    if ( $id ) : 

        $args['ID'] = $id;
        $action = 'update';
    endif;
     
    $resposta_id = call_user_func( "wp_{$action}_post", $args );
    carbon_set_post_meta( $resposta_id, 'respostas',
    array(
        array(
            'resposta_pergunta' => array( $_POST['resposta_pergunta'] ),
            'resposta' => $_POST['resposta']
        )
    ));

    carbon_set_post_meta( $resposta_id, 'user_title', $_SESSION["auth-user"]["name"] );
    carbon_set_post_meta( $resposta_id, 'user_email', $_SESSION["auth-user"]["email"] );
    carbon_set_post_meta( $resposta_id, 'user_manufacturer', $_SESSION["auth-user"]["fabricanteName"] );
endif;

$assets = plugin_dir_url( __FILE__ ) . '../../public/frontend/VTM-Wizard/assets/';
$manufacturer = $_SESSION["auth-user"]["fabricanteName"] ?? null;
$assessment_id = get_the_ID();
$url = get_rest_url( null, 'mount-frontend/'. $assessment_id );
$request = new WP_REST_Request( 'GET', '/mount-frontend/'. $assessment_id );
$assessment = rest_do_request( $request );

if ( $assessment ) {

    $json = rest_get_server()->response_to_data( $assessment, true );

    $title = $json['assessment_title'];
    $content = wpautop( $json['assessment_description'] );

    $style = $json['assessment_style'];
    $estilos = '';
    foreach ( $style as $key => $value ) : 
        
        switch ($key) {
            case 'background':
                $estilos .= "{$key}: {$value['type']}({$value['angle']}deg, {$value['color_start']} 0%, {$value['color_finish']} 100%);";
                break;
            
            default:
                $estilos .= "";
                break;
        }
    endforeach;

    $perguntas = $json['assessment_perguntas'];
    $qtd_perguntas = count( $perguntas );
    $respondidas = 0;
    $modulos = array();

    $all_pontos = array();
    $total_pontos = array();
    $nao_respondida = array();
    foreach ( $perguntas as $pergunta_id => $pergunta ) : 

        $mid = clean($pergunta['pergunta_modulo']['id']);
        $modulos[$mid][$pergunta['pergunta_ordem']] = $pergunta_id;
        $_pts[$pergunta_id] = pontos( $pergunta_id );
        $all_pontos[$mid] += intval( $pergunta['pergunta_pontos_total'] );
        $total_pontos[$mid] += $_pts[$pergunta_id];
        ksort( $modulos[$mid] );

        $pergunta_img[$pergunta_id] = isset( $pergunta['pergunta_img'] ) && $pergunta['pergunta_img'] ? $pergunta['pergunta_img'] : $assets .'img/coca-cola.png';
        $material_exemplo[$pergunta_id] = wp_get_attachment_url( $pergunta['pergunta_material_exemplo'] );

        $respondidas += active( $pergunta_id ) ? 1 : 0;
        if ( 2 === count( $nao_respondida ) ) continue;
        if ( null === active( $pergunta_id ) ) : 
            
            $nao_respondida[$pergunta['pergunta_ordem']] = $pergunta_id;
        endif;
    endforeach;
    ksort( $nao_respondida, SORT_NUMERIC );

    $pid = isset( $_GET['pergunta'] ) ? $_GET['pergunta'] : null;
    $pergunta_all = new WP_REST_Request( 'GET', '/wp/v2/perguntas/'. $pid );
    $pergunta_all->set_query_params(
        array(
            'per_page'  => 100,
            'status'    => 'publish',
            'order'     => 'DESC',
            'orderby'   => 'ID',
        )
    );
    $pergunta_all = rest_do_request( $pergunta_all );
    $pergunta_all = rest_get_server()->response_to_data( $pergunta_all, true );
    $proxima = $pid ? 1 : 0;
    $p = isset( $nao_respondida ) && 0 < count( $nao_respondida ) ? '?pergunta='. array_values( $nao_respondida )[$proxima] : '?fim';
}

function clean( $string ){

   $string = str_replace( ' ', '_', $string );
   return preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
}

function monta_query( $pid ){

    $the_query = new WP_Query( array(
        'post_type'     => 'respostas',
        'nopaging'      => TRUE,
        'posts_per_page'=> -1,
        'order'         => 'DESC',
        'orderby'       => 'ID',
        'meta_query'    => array(
            array(
                'key'   => 'respostas/resposta_pergunta',
                'value' => $pid,
                'carbon_field_property' => 'id'
            ),
            array(
                'key'   => 'user_manufacturer',
                'value' => $_SESSION["auth-user"]["fabricanteName"]
            )
        ),
    ));

    return $the_query;
}

function active( $rid ){

    $the_query = monta_query( $rid );

    if ( $the_query->have_posts() ) : 

        $data = array();
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $data['id'] = get_the_ID(); 
            $data['resposta'] = carbon_get_the_post_meta( 'respostas/resposta' );
        endwhile;
        wp_reset_postdata();
    endif;

    return isset( $data ) ? $data : null;
}

function pontos( $pid ){

    $the_query = new WP_Query( array(
        'post_type'     => 'perguntas',
        'post_status'   => array( 'publish' ),
        'perm'          => 'readable',
        'p'             => $pid
    ));

    $pts = array();
    if ( $the_query->have_posts() && active( $pid ) ) : 

        while ( $the_query->have_posts() ) : $the_query->the_post(); 
            $id = get_the_ID();
            $respostas = carbon_get_the_post_meta( 'pergunta_resposta' );
            $_respostas = array();
            foreach ( $respostas as $index => $resposta ) : 
                
                if ( $resposta['titulo_radio'] == active( $pid )['resposta'] ) : 
                    
                    $_respostas[$id] = $index;
                endif;
            endforeach;
            $pts[$id] = $respostas[$_respostas[$id]]['resposta_pontos'];
        endwhile;
        wp_reset_postdata();
    endif;

    return isset( $pts[$pid] ) ? $pts[$pid] : 0;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- swiper -->
    <!-- <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" /> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- style -->
    <link rel="stylesheet" href="<?= $assets ?>css/style.compilado.css">

    <!-- gsap deixar no head -->
    <!--     <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.6.1/gsap.min.js"
        integrity="sha512-cdV6j5t5o24hkSciVrb8Ki6FveC2SgwGfLE31+ZQRHAeSRxYhAQskLkq3dLm8ZcWe1N3vBOEYmmbhzf7NTtFFQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

    <title><?= $title ?></title>

</head>

<body>

    <main id="page__modelo1">

        <div class="grid__app">
            <div class="sidebar">
                <header class="sidebar-header">
                    <h3><?= $title ?></h3>
                </header>
                <div class="sidebar-accordion accordion" id="accordionSidebar">
                    <ul class="accordion__lista">
                        <li class="accordion-item ">
                            <div class="accordion__lista-header accordion-header" id="headingOne">
                                <?php
                                $obj_id = get_queried_object_id();
                                $current_url = get_permalink( $obj_id );
                                ?>
                                <a class="accordion-button d-flex justify-content-between" href="<?= $current_url ?>">
                                    <div class="accordion-title">
                                        <span class="name-category">INSTRUÇÕES</span>
                                    </div>
                                </a>

                            </div>
                        </li>
                        <?php
                        $pontos_feitos = 0;
                        $pontos_geral = 0;
                        foreach ( $modulos as $mid => $perguntas_ordenadas ) : 

                            $term = get_term( $mid );
                            $modulo_name = $term->name;
                        ?>
                        <li class="accordion-item">
                            <div class="accordion__lista-header accordion-header">
                                <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= $mid ?>"
                                    aria-expanded="false" aria-controls="collapse3" data-mid="<?= $mid ?>">
                                    <div class="accordion-title">
                                        <span class="name-category"><?= $modulo_name ?></span>
                                        <span class="points">
                                            <?= $total_pontos[$mid] ?>/
                                            <?= $all_pontos[$mid] ?>pts
                                        </span>
                                    </div>

                                </button>
                            </div>
                            <div id="collapse_<?= $mid ?>" class="accordion-collapse collapse" data-bs-parent="#accordionSidebar">
                                <div class="accordion-body d-flex flex-column">
                                    <div class="position-relative">
                                        <div class="barra">
                                        </div>
                                        <ul class="nav flex-column sub-category">
                                            <!--ADICIONAR CLASSE ACTIVER BAR AO NAV-ITEM QUANDO FOR CONCLUÍDO PARA QUE FIQUE VERDE-->
                                            <?php
                                            foreach ( $perguntas_ordenadas as $ordem => $pergunta_id ) : 

                                                $pergunta = $perguntas["{$pergunta_id}"];
                                                $pts = $pergunta['pergunta_pontos_total'];
                                                $active = null !== active( $pergunta_id ) ? active( $pergunta_id )['id'] : null;
                                            ?>
                                            <li class="nav-item <?= $active ? 'active-bar' : '' ?>" data-pid="<?= $pergunta_id ?>">
                                                <a class="nav-link active " aria-current="page" href="?pergunta=<?= $pergunta_id ?>">
                                                    <div class="nav-link-flex">
                                                        <span class="name-sub-category"><?= $pergunta['pergunta_title']; ?></span>
                                                        <span class="points-sub-category">
                                                            <?= $_pts[$pergunta_id] ?>/
                                                            <?= $pts ?>pts
                                                        </span>
                                                    </div>

                                                </a>
                                            </li>
                                            <?php
                                            endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php 
                        endforeach; 
                        $qtd_100 = ceil(( $respondidas * 100 ) / $qtd_perguntas);
                        ?>
                    </ul>
                </div>
                <footer>
                    <div class="progress__bar">
                        <span>
                            <span id="progress-text">(<?= $respondidas ?>/<?= $qtd_perguntas ?>)</span>
                            Completo
                        </span>
                        <div class="bar">
                            <div class="bar__fill" style="width: <?= $qtd_100 ?>%;"></div>
                        </div>
                    </div>
                    <div class=" btn_container">
                        <div class="result__step">
                            <img src="<?= $assets ?>img/star.svg">
                            <span id="result__step-text"><?= $qtd_100 ?>%</span>
                        </div>
                        <button type="button" class="btn-primario">Salvar e sair</button>
                    </div>
                </footer>
            </div>



            <div class="content" style="<?= $estilos ?>">
                <div class="bg">
                    <?php
                    $obj_id = get_queried_object_id();
                    $current_url = get_permalink( $obj_id ) .$p;
                    if ( isset( $_GET['pergunta'] ) && isset( $pergunta_all ) ) : 

                    $term = get_term( $pergunta_all['pergunta_modulo'][0]['id'] );
                    $modulo_name = clean( $term->name );
                    ?>
                    <div class="content-wrapper grid">
                        <div class="img__box">
                          <img src="<?= $pergunta_img[$pergunta_all['id']] ?>" alt="">
                        </div>
                        <form action="<?= $current_url ?>" method="POST" class="question__content">
                          <div class="title">
                            <h2><?= $pergunta_all['title']['rendered'] ?></h2>
                            <p><?= $pergunta_all['content']['rendered'] ?></p>
                            <?php
                            if ( $material_exemplo[$pergunta_all['id']] ) : ?>
                            <p><a href="<?= $material_exemplo[$pergunta_all['id']] ?>" target="_blank">Material de Exemplo</a></p>
                            <?php
                            endif; ?>
                          </div>
                          <div class="questions__wrapper">
                            <?php
                            $tipo = $pergunta_all['tipo_de_resposta'];
                            $tipo = str_replace( '_resposta', '', $tipo );
                            $titulo = 'titulo_'. $tipo;
                            $pontos_total = $pergunta_all['pergunta_pontos_total'];
                            foreach ( $pergunta_all['pergunta_resposta'] as $resposta ) : 

                                $description = $resposta['resposta_description'];

                                $the_query = monta_query( $pid );
                                
                                $_resposta = null;
                                if ( $the_query->have_posts() ) : 

                                    while ( $the_query->have_posts() ) : $the_query->the_post(); 
                                        $id_resposta = get_the_ID();
                                        $_resposta = carbon_get_the_post_meta( 'respostas/resposta' );
                                    endwhile;
                                    wp_reset_postdata();
                                endif; ?>
                                <label class="questions__item">
                                  <div class="<?= $tipo ?>">
                                    <input type="hidden" name="id_resposta" value="<?= isset( $id_resposta ) ? $id_resposta : '' ?>">
                                    <input type="hidden" name="resposta_pergunta" value="<?= $pid ?>">
                                    <input 
                                        type="<?= $tipo ?>" 
                                        id="question-<?= $tipo ?>" 
                                        name="resposta" 
                                        value="<?= $resposta["{$titulo}"] ?>" 
                                        <?= $_resposta == $resposta["{$titulo}"] ? 'checked' : '' ?>
                                    >
                                  </div>
                                  <div class="question__text">
                                    <h3><?= $resposta["{$titulo}"] ?></h3>
                                    <p><?= $description ?></p>
                                  </div>
                                  <div class="question__points">
                                    <span><?= $resposta['resposta_pontos'] ? $resposta['resposta_pontos'] : 0 ?>pts</span>
                                  </div>
                                </label>
                            <?php
                            endforeach; ?>
                          </div>
                          <div class="btn__container">
                            <div class="score">
                              <span id="score__text">
                                <?= $pontos_total ? $pontos_total : 0  ?>/
                                <?= $all_pontos[$modulo_name] ?>pts
                              </span>
                            </div>
                            <button class="btn-primario" id="btnNext" type="submit">PROSSEGUIR</button>
                          </div>
                        </form>
                    </div>
                    <?php
                    elseif ( isset( $_GET['fim'] ) ) : ?>
                    <div class="content-wrapper">
                        <div class="text-content">
                            <div class="text-content-item" id="fim">
                                <h2 style="color: #4a0000ff; margin-bottom: 20px;">Parabéns!</h2>
                                <div>
                                    Você concluiu seu assessment.<br />
                                    Em seu resultado, conseguimos identificar algumas oportunidades:
                                </div>
                                <ol style="padding: 20px;">
                                <?php
                                foreach ( $modulos as $mid => $perguntas_ordenadas ) : 

                                    $term = get_term( $mid );
                                    $modulo_name = $term->name;

                                    echo '<li style="list-style: inherit; padding-left: 10px;">'. $modulo_name .' - '. $total_pontos[$mid] .' pontos, dentre '. $all_pontos[$mid] .' possíveis.'.'</li>';
                                endforeach;
                                ?>
                                </ol>
                            </div>
                            <div class="btn__container">
                                <a href="<?= $current_url ?>" class="btn-primario">Sair</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    else : ?>
                    <div class="content-wrapper">
                        <div class="text-content">
                            <div class="text-content-item" id="instrucoes">
                                <h3>Instruções</h3>
                                <?= $content ?>
                            </div>
                             <div class="btn__container">
                                <a href="<?= $current_url ?>" class="btn-primario">PROSSEGUIR</a>
                            </div>
                        </div>
                    </div>
                    <?php
                    endif; ?>
                </div>

                <div class="logo__content">
                    <img src="<?= $assets ?>img/vtm.png" alt="logo VTM">
                    <img src="<?= $assets ?>img/logbit.png" alt="Logo Logbit">
                </div>
            </div>
        </div>
    </main>

    <!-- jquery -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <!-- icones -->
    <!-- <script type="module" src="https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js"></script> -->
    <!-- script main -->
    <script type="module" src="<?= $assets ?>js/main.js"></script>

    <!--bootstrap js-->
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>

</body>
</html>