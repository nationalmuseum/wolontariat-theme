<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Illustratr
 */
?>

	</div><!-- #content -->

	<?php get_template_part( 'newsletterForm' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="footer-area clear">
			<?php
				if ( has_nav_menu( 'social' ) ) {
					wp_nav_menu( array(
						'theme_location'  => 'social',
						'container_class' => 'menu-social',
						'menu_class'      => 'clear',
						'link_before'     => '<span class="screen-reader-text">',
						'link_after'      => '</span>',
						'depth'           => 1,
					) );
				}
			?>
			<div class="site-info logo-container">
				<div class="grid grid-md-1-2">
			 		<a class="logo" href="http://www.mnw.art.pl"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.png" alt="Muzeum Narodowe w Warszawie" /></a>
				</div><!-- @info: logo 
				--><div class="grid grid-md-1-2">
					<span class="orangutan mail">muzeariat[orangutan]mnw.art.pl</span>
				</div><!-- @info: contact -->
			</div><!--
		 --><div class="site-info text-container">
				<?php printf( 'Motyw: %1$s<br /> Od: %2$s', 'Illustratr | Wolontariat', '<a href="http://wordpress.com/themes/illustratr/" rel="designer">WordPress.com</a> | <a href="http://realhe.ro" rel="designer">Realhe.ro</a>' ); ?>
			</div><!-- .site-info -->
		</div><!-- .footer-area -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>