<?php
if($args['title']): 
    $title = $args['title'];
else:
    $title = get_the_title();
endif;
?>

<div class="full-width" style="background-image: url('<?php echo get_stylesheet_directory_uri() . '/assets/img/title-bg-cap.png' ?>'); " id="page-header-banner">
    <div class="max-width center">
        <h1 class="white title-xm"><?php echo _($title); ?></h1>
    </div>
</div>