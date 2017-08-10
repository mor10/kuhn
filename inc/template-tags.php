<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Kuhn
 */

 if ( ! function_exists( 'kuhn_posted_on' ) ) :
 /**
  * Prints HTML with meta information for the current post-date/time and author.
  */
 function kuhn_posted_on() {
 	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
 	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
 		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
 	}

 	$time_string = sprintf( $time_string,
 		esc_attr( get_the_date( 'c' ) ),
 		esc_html( get_the_date() ),
 		esc_attr( get_the_modified_date( 'c' ) ),
 		esc_html( get_the_modified_date() )
 	);

 	$posted_on = sprintf(
 		esc_html_x( 'Published %s', 'post date', 'kuhn' ),
 		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
 	);

 	$byline = sprintf(
 		esc_html_x( 'Written by %s', 'post author', 'kuhn' ),
 		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
 	);

 	echo '<span class="byline"> ' . $byline . '</span> <span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

 	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
 		echo ' <span class="comments-link"><span class="extra">Discussion </span>';
 		/* translators: %s: post title */
 		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'kuhn' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
 		echo '</span>';
 	}

 	edit_post_link(
 		sprintf(
 			/* translators: %s: Name of current post */
 			esc_html__( 'Edit %s', 'kuhn' ),
 			the_title( '<span class="screen-reader-text">"', '"</span>', false )
 		),
 		' <span class="edit-link"><span class="extra">Admin </span>',
 		'</span>'
 	);

 }
 endif;

 if ( ! function_exists( 'kuhn_entry_footer' ) ) :
 /**
  * Prints HTML with meta information for the categories, tags and comments.
  */
 function kuhn_entry_footer() {
 	// Hide tag text for pages.
 	if ( 'post' === get_post_type() ) {

 		/* translators: used between list items, there is a space after the comma */
 		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'kuhn' ) );
 		if ( $tags_list ) {
 			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'kuhn' ) . '</span>', $tags_list ); // WPCS: XSS OK.
 		}
 	}

 }
 endif;

 /**
  * Display category list
  */

 function kuhn_the_category_list() {
 	/* translators: used between list items, there is a space after the comma */
 	$categories_list = get_the_category_list( esc_html__( ', ', 'kuhn' ) );
 	if ( $categories_list && kuhn_categorized_blog() ) {
 		printf( '<div class="cat-links">' . esc_html__( '%1$s', 'kuhn' ) . '</div>', $categories_list ); // WPCS: XSS OK.
 	}
 }

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function kuhn_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'kuhn_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'kuhn_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so kuhn_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so kuhn_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in kuhn_categorized_blog.
 */
function kuhn_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'kuhn_categories' );
}
add_action( 'edit_category', 'kuhn_category_transient_flusher' );
add_action( 'save_post',     'kuhn_category_transient_flusher' );


/**
 * Post navigation (previous / next post) for single posts.
 */
function kuhn_post_navigation() {
	the_post_navigation( array(
		'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'kuhn' ) . '</span> ' .
			'<span class="screen-reader-text">' . __( 'Next post:', 'kuhn' ) . '</span> ' .
			'<span class="post-title">%title</span>',
		'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'kuhn' ) . '</span> ' .
			'<span class="screen-reader-text">' . __( 'Previous post:', 'kuhn' ) . '</span> ' .
			'<span class="post-title">%title</span>',
	) );
}

/**
 * Customize ellipsis at end of excerpts.
 */
function kuhn_excerpt_more( $more ) {
	return "…";
}
add_filter( 'excerpt_more', 'kuhn_excerpt_more' );

$prev_link = get_next_posts_link( __( 'Older posts' ) );
$navigation = '<div class="nav-next">' . $prev_link . '</div>';

/**
 * Custom pagination navigation  for archive pages.
 * Deconstructed version of get_the_posts_navigation()
 * @link https://developer.wordpress.org/reference/functions/get_the_posts_navigation/
 *
 * @param string $end Defines the position of the nav button: 'next' or 'previous'.

 */
function kuhn_get_the_archive_navigation( $end ) {
    $navigation = '';

    // Don't print empty markup if there's only one page.
    if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {

        if ( $end == 'next' ) {
            $nav_link = get_previous_posts_link( _x( 'Newer posts', 'Newer posts button on archive pages.', 'kuhn' ) );
        } elseif ( $end == 'previous' ) {
            $nav_link = get_next_posts_link( _x( 'Older posts', 'Older posts button on archive pages.', 'kuhn' ) );
        }

        if ( $nav_link ) {
            $navigation .= '<div class="nav-' . $end . '">' . $nav_link . '</div>';
            $navigation = _navigation_markup( $navigation, 'posts-navigation', __( 'Posts navigation', 'Screen reader text for posts pagination areas.', 'kuhn' ) );
        }

    }

    return $navigation;

}
