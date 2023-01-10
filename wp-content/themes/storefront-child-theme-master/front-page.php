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
                            </div>
                            <div class="card-content flex-columns">
                                <a href="#"><?php echo _('Adicionar à cotação')?></a>
                                <h2 class="title-m"><?php the_title() ?></h2>
                            </div>
                            <a href="<?php echo get_the_permalink(); ?>" class="btn-blue card-product-link"><?php echo _('Ver Produto') ?></a>
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

        <section id="time" class="full-width v-m-padding ">
            <div class="max-width flex center">
                <div class="title flex">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-clock-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                    </svg>
                    <h2 class="title-m white"><?php echo _('Horário de funcionamento') ?></h2>
                </div>
                <div class="flex">
                    <ul class="flex-columns">
                        <li class="title-m white"><?php echo _('Segunda à Quinta 7h45 às 12h00') ?></li>
                        <li class="title-m white"><?php echo _('13h15 às 18h00 Sexta 7h45 às 17h00') ?></li>
                    </ul>
                </div>
            </div>
        </section>
    </div><!--id= post name-->

    <div id="google-maps" class="full-width">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3660.919330681457!2d-51.97549512530118!3d-23.427281056678762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ecd7021243d65d%3A0x41f4abfaff04afa2!2sCapsutec%20Encapsuladoras%20e%20Equipamentos%20Farmac%C3%AAuticos!5e0!3m2!1ses!2sve!4v1673360689217!5m2!1ses!2sve" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
<?php get_footer(); ?>