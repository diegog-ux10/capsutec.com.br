<?php get_header(); ?>

    <div id="<?php echo get_post()->post_name  ?>">
        <!-- Slider do home -->
        <div class="slider-container">
            <!-- Swiper -->
            <div class="swiper mySwiper swiper1">
            <div class="swiper-wrapper swiper-wrapper1">
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
                    <div class="swiper-slide swiper-slide1" style='background-image: url("<?= $imagen ?>");'>
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
        </div><!--.slider-container-->

        <!-- valores seção -->
        <section class="max-width center flex" id="value-container">
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
        </section>
        
        <!-- quem somos seção -->
        <?php $about_group = get_field('quem_somos')?> <!-- Obtendo o campo acf -->
        <section id="about-home" class="max-width center flex l-m-padding">
            <figure>
                <img src="<?= esc_url($about_group['imagem']) ?>" alt="capsutec">
            </figure>
            <article class="flex-columns">
                <h3 class="title-xs blue"><?= _(esc_html($about_group['titulo'])) ?></h3>
                <h2 class="title-m dark-blue"><?= _(esc_html($about_group['grau_secundario'])) ?></h2>
                <?= _($about_group['texto']) ?>            
            </article>
        </section>

        <?php
        // The tax query
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN', // or 'NOT IN' to exclude feature products
        );

        $product_settings = array(
            'posts_per_page' => 4,
            'post_type' => 'product',    
            'orderby' => 'date',
            'order' => 'ASC',
            'tax_query' => $tax_query
        );

        $products = new WP_Query($product_settings);
    
        ?>
        <section id="carousel-home">
            <?php if(have_posts($products)): ?>        
            <div class="carousel-container swiper swiper2">
                <div class="slide-content">
                    <div class="card-wrapper swiper-wrapper swiper-wrapper2">
                    <?php while($products->have_posts()) : $products->the_post(); ?>
                    <?php global $product; 
                            $id = $product->get_id(); ?>
                        <div class="card swiper-slide swiper-slide2">
                            <div class="image-content flex-columns">
                                <div class="card-image">
                                    <img src="<?php echo get_the_post_thumbnail_url() ?>" alt="<?php echo esc_attr(get_the_title()) ?>" class="card-img">
                                </div>
                                <div class="card-content flex-columns">
                                <a rel="nofollow" href='<?php echo home_url() . "/cart/?add-to-cart=$id" ?>' data-quantity="1" data-product_id="<?php echo $id?>" data-product_sku="<?php $product->get_sku();?>" class="add_to_cart_button ajax_add_to_cart"><?php echo _('Adicionar à cotação')?></a>
                                    <h2 class="name"><?php the_title() ?></h2>
                                    <?php wsb_add_to_cart_button(); ?>
                                </div>
                                <p><?php echo $id ?></p>
                                <div class="button"><?php echo _('Ver Produto') ?></div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>            
                </div>
                <div class="swiper-button-next swiper-navBtn"></div>
                <div class="swiper-button-prev swiper-navBtn"></div>
                <div class="swiper-pagination"></div>
            </div>
            
            <?php endif; ?>
        </section>
        
        <?php echo do_shortcode( "[ajax_add_to_cart id='78' text='Buy']" ); ?>
    </div><!--id= post name-->
<?php get_footer(); ?>