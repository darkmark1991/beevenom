<!DOCTYPE html>
<html lang="en">
<head>
    <link href="<?php bloginfo('template_directory');?>/style.css" rel="stylesheet">
    <script src="<?php bloginfo('template_directory');?>/js/jquery-3.1.1.min.js" type="text/javascript"></script>
    <script src="<?php bloginfo('template_directory');?>/js/script.js" type="text/javascript"></script>
    <!-- favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo('template_directory');?>/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?php bloginfo('template_directory');?>/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php bloginfo('template_directory');?>/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?php bloginfo('template_directory');?>/favicon/manifest.json">
    <link rel="mask-icon" href="<?php bloginfo('template_directory');?>/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">
    <!--[if (gte IE 6)&(lte IE 8)]>
    <script src="<?php bloginfo('template_directory');?>/selectivizr.min.js" type="text/javascript"></script>
    <![endif]-->
    <?php wp_head(); ?>
</head>

<body>
<div class="site">
    <div class="header">
        <?php if ( is_active_sidebar( 'header-widgets' ) ) : ?>
            <div id="header-widgets" class="header-widget-area" role="complementary">
                <?php dynamic_sidebar( 'header-widgets' ); ?>
            </div>
        <?php endif; ?>
        <a href="<?php echo get_site_url(); ?>" id="logo-home-url" class="logo-home-url">
            <div id="logo-slider">
                <div>
                    <img src="<?php bloginfo('template_directory');?>/images/logo_new.png">
                </div>
                <div>
                    <img src="<?php bloginfo('template_directory');?>/images/logo_old.png">
                </div>
            </div>
        </a>

    </div>
    <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'beevenom' ); ?>">
        <?php
        if(has_nav_menu('primary'))
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_class'     => 'primary-menu',
            ) );
        ?>
    </nav>
    <div class="header-image">
        <?php
        if(has_post_thumbnail()){
            the_post_thumbnail();
        } else if(has_header_image()){
            ?>
            <img src = "<?php header_image(); ?>" height = "<?php echo get_custom_header()->height; ?>" width = "<?php echo get_custom_header()->width; ?>" alt = "" />
            <?php
        }
        ?>
        <div class="header-tagline">
            <p class="page-title">
                <?php
                if(get_the_title() != '')
                    echo get_the_title();
                else
                    echo get_bloginfo( 'description', 'display' );
                ?>
            </p>
        </div>
    </div>
    <div class="site-inner">
        <div class="site-content">
            <?php
            while ( have_posts() ) : the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>
</div>

<footer class="footer">
    <p>Copyright &copy; <?php echo (date("Y") === "2016")?"2016":"2016 - " . date("Y"); ?> <?php echo bloginfo('name');?> LTD</p>
</footer>
<!--        --><?php //wp_footer(); ?>
</body>
</html>