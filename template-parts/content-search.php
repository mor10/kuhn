<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Kuhn
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	if ( has_post_thumbnail() ) { ?>
	<figure class="featured-image">
		<a href="<?php echo esc_url( get_permalink() ) ?>" rel="bookmark">
			<?php
			$thumbnail_alt = 'Go to ' . get_the_title();
			the_post_thumbnail('kuhn-index', 'alt=' . $thumbnail_alt);
			?>
		</a>
	</figure><!-- .featured-image full-bleed -->
	<?php } ?>

	<header class="entry-header">
		<?php kuhn_the_category_list(); ?>
		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );	?>
	</header><!-- .entry-header -->

	<?php
	if ( 'post' === get_post_type() ) : ?>
	<div class="entry-meta">
		<?php kuhn_posted_on(); ?>
	</div><!-- .entry-meta -->
	<?php
	endif; ?>

	<div class="entry-content">
		<?php the_excerpt(); ?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->

