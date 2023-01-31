<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header(); ?>
    
	<div id="primary" class="content-area <?php echo get_post()->post_name . "-" . get_the_ID() ?>">
		<main id="main" class="site-main" role="main">            
			<?php
			while ( have_posts() ) :
				the_post();

				do_action( 'storefront_page_before' );
                get_template_part('template-parts/content', 'pages-banner'); ?>
                <div class="max-width center l-m-padding">
					<?php if(is_page(83)): ?>
						<p class="text-sm dark-black center-text">Nesta página pode enviar um pedido de orçamento personalizado.</p>
						<p class="text-sm dark-black center-text">Envie sua solicitação e enviaremos uma cotação em 12-24 horas. Obrigado</p>	
					<?php endif; ?>
                    <?php get_template_part( 'content', 'page' ); ?>
                </div>
                <?php
				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );

			endwhile; // End of the loop.
			?>
            
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
