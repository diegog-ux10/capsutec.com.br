<?php get_header(); ?>
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
            <div class="mision">

            </div>
        </div>
    </div>
<?php get_footer(); ?>