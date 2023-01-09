<?php get_header(); ?>

    <!-- Slider do home -->
    <div id="<?php echo get_post()->post_name  ?>">
        <div class="main-container-prueba">
            <div class="slider-container">
                <!-- Swiper -->
                <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php // Check rows existexists.
                    if( have_rows('slider') ):

                        // Loop through rows.
                        while( have_rows('slider') ) : the_row(); 
                        $imagen = get_sub_field('imagem_de_fundo');
                        $link = get_sub_field('link');
                        $button_text = get_sub_field('texto_no_botao');
                        $text = get_sub_field('paragrafo_do_slide');
                        $title = get_sub_field('mensagem_principal');
                        ?>
                        <div class="swiper-slide" style='background-image: url("<?= $imagen ?>");'>
                            <div class="flex-columns swiper-slide_info-container">
                                <h2 class="title-l dark-blue"><?= _($title) ?></h2>
                                <p class="text blue"><?= _($text) ?></p>
                                <a href="<?= $link ?>" class="btn-blue"><?= _($button_text) ?></a>
                            </div>
                        </div>

                        <!-- End loop -->
                       <?php endwhile;

                    // No value.
                    else :
                        // Do something...
                    endif; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                </div>
            </div>

            <!-- seccion valores -->
            <div class="max-width center flex" id="value-container">
                <div class="column_value flex-columns">
                    <h4 class="title-s white"><?= _('MISSÃO') ?></h4>
                    <p class="text-xsm white"><?= _('Ser uma empresa dinâmica, com uma equipe permanentemente comprometida com a busca da excelência, objetivando a satisfação do cliente com rentabilidade e competitividade, contribuindo para o bem-estar e o desenvolvimento profissional de seus colaboradores e a preservação do meio ambiente') ?></p>
                </div>
                <div class="column_value flex-columns">
                    <h4 class="title-s white"><?= _('VISÃO') ?></h4>
                    <p class="text-xsm white"><?= _('Ser reconhecida como uma empresa dinâmica e ética, comprometida com a busca permanente da excelência, com crescimento sustentável no mercado nacional e internacional.') ?></p>
                </div>
                <div class="column_value flex-columns">
                    <h4 class="title-s white"><?= _('VALORES') ?></h4>
                    <p class="text-xsm white"><?= _('Inovação, comprometimento, honestidade e respeito (a natureza e aos animais e ao próximo).') ?></p>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>