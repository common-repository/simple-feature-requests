<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
/**
 * Template Hooks.
 */
class SFR_Template_Methods {
    /**
     * Get filters.
     *
     * @return array
     */
    public static function get_filters() {
        $archive_url = sfr_get_archive_url_with_filters( array('filter', 'search') );
        $filters = array(
            'latest' => array(
                'type'  => 'text',
                'url'   => $archive_url,
                'class' => array('active'),
                'label' => __( 'Latest', 'simple-feature-requests' ),
            ),
            'top'    => array(
                'type'  => 'text',
                'url'   => add_query_arg( array(
                    'filter' => 'top',
                ), $archive_url ),
                'class' => array(),
                'label' => __( 'Top', 'simple-feature-requests' ),
            ),
        );
        if ( is_user_logged_in() ) {
            $default_plural_name = sfr_apply_filters( 'sfr_plural_request_name', 'Requests', true );
            $filters['my-requests'] = array(
                'type'  => 'text',
                'url'   => add_query_arg( array(
                    'filter' => 'my-requests',
                ), $archive_url ),
                'class' => array(),
                'label' => sprintf( __( 'My %s', 'simple-feature-requests' ), $default_plural_name ),
            );
        }
        $status_excludes = ( is_user_logged_in() ? array() : array('pending') );
        $filters['status'] = array(
            'type'    => 'select',
            'class'   => array(),
            'label'   => __( 'Status', 'simple-feature-requests' ),
            'options' => sfr_get_statuses( $status_excludes ),
        );
        if ( isset( $_GET['filter'] ) && isset( $filters[$_GET['filter']] ) ) {
            $filters['latest']['class'] = array();
            $filters[$_GET['filter']]['class'][] = 'active';
        }
        return sfr_apply_filters( 'sfr_filters', $filters );
    }

    /**
     * Get filter HTML.
     *
     * @param array $filter
     *
     * @return string
     */
    public static function get_filter_html( $filter ) {
        $method_name = sprintf( 'filter_html_%s', $filter['type'] );
        if ( !method_exists( __CLASS__, $method_name ) ) {
            return '';
        }
        ob_start();
        call_user_func_array( array(__CLASS__, $method_name), array($filter) );
        return ob_get_clean();
    }

    /**
     * Get text filter HTML.
     *
     * @param $filter
     */
    public static function filter_html_text( $filter ) {
        ?>
		<a href="<?php 
        esc_attr_e( $filter['url'] );
        ?>" class="sfr-filters__filter-item-button <?php 
        echo implode( ' ', $filter['class'] );
        ?>">
			<?php 
        echo $filter['label'];
        ?>
		</a>
		<?php 
    }

    /**
     * Get select filter HTML.
     *
     * @param $filter
     */
    public static function filter_html_select( $filter ) {
        $base_url = sfr_get_archive_url_with_filters( array($filter['key'], 'search') );
        $selected = filter_input( INPUT_GET, $filter['key'] );
        $option_keys = array_keys( $filter['options'] );
        $selected = ( in_array( $selected, $option_keys, true ) ? $selected : false );
        ?>
		<span class="sfr-filters__filter-item-button <?php 
        if ( $selected ) {
            echo 'active';
        }
        ?>">
			<select class="<?php 
        echo implode( ' ', $filter['class'] );
        ?>" onchange="location.href = this.value;">
				<option value="<?php 
        echo esc_attr( $base_url );
        ?>"><?php 
        printf( '%s %s', __( 'Any', 'simple-feature-requests' ), $filter['label'] );
        ?></option>
				<?php 
        foreach ( $filter['options'] as $value => $label ) {
            ?>
					<?php 
            $url = add_query_arg( $filter['key'], $value, $base_url );
            ?>
					<option value="<?php 
            echo esc_attr( $url );
            ?>" <?php 
            selected( $value, $selected );
            ?>><?php 
            echo $label;
            ?></option>
				<?php 
        }
        ?>
			</select>
		</span>
		<?php 
    }

    /**
     * Loop item title.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_title( $feature_request ) {
        if ( $feature_request->is_single() ) {
            ?>
			<<?php 
            echo SFR_Post_Types::get_single_title_tag();
            ?> class="sfr-loop-item__title"><?php 
            echo $feature_request->post->post_title;
            ?></<?php 
            echo SFR_Post_Types::get_single_title_tag();
            ?>>
			<?php 
            if ( SFR_Post_types::hide_entry_title() ) {
                ?>
				<style>
				.entry-header, .wp-block-post-title {
					display: none;
				}
				</style>
				<?php 
            }
            return;
        }
        ?>
		<<?php 
        echo SFR_Post_Types::get_archive_title_tag();
        ?> class="sfr-loop-item__title">
			<a href="<?php 
        echo get_the_permalink( $feature_request->post->ID );
        ?>"><?php 
        echo $feature_request->post->post_title;
        ?></a>
		</<?php 
        echo SFR_Post_Types::get_archive_title_tag();
        ?>>
		<?php 
    }

    /**
     * Loop item vote badge.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_vote_badge( $feature_request ) {
        $votes_count = $feature_request->get_votes_count();
        $page_type = SFR_Post_Types::get_page_type();
        ?>
		<div class="sfr-vote-badge sfr-vote-badge--<?php 
        echo esc_attr( $feature_request->post->ID );
        ?> sfr-vote-badge--<?php 
        echo esc_attr( $page_type );
        ?>">
			<div class="sfr-vote-badge__count">
				<strong><?php 
        echo $votes_count;
        ?></strong>
				<span><?php 
        echo _n(
            'vote',
            'votes',
            $votes_count,
            'simple-feature-requests'
        );
        ?></span>
			</div>
			<button class="sfr-vote-badge__increment sfr-vote-button <?php 
        if ( $feature_request->has_user_voted() ) {
            echo 'sfr-vote-button--voted';
        }
        ?>" data-sfr-vote="<?php 
        echo esc_attr( $feature_request->post->ID );
        ?>"><?php 
        echo $feature_request->get_vote_button_text();
        ?></button>
		</div>
		<?php 
    }

    /**
     * Loop item vote badge (mini).
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_vote_badge_mini( $feature_request ) {
        $votes_count = $feature_request->get_votes_count();
        ?>
		<div class="sfr-vote-badge sfr-vote-badge--mini sfr-vote-badge--<?php 
        echo esc_attr( $feature_request->post->ID );
        ?>">
			<button class="sfr-vote-badge__increment sfr-vote-button <?php 
        if ( $feature_request->has_user_voted() ) {
            echo 'sfr-vote-button--voted';
        }
        ?>" data-sfr-vote="<?php 
        echo esc_attr( $feature_request->post->ID );
        ?>"><?php 
        echo $feature_request->get_vote_button_text();
        ?></button>
			<div class="sfr-vote-badge__count">
				<strong><?php 
        echo $votes_count;
        ?></strong>
			</div>
		</div>
		<?php 
    }

    /**
     * Loop item status badge.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_status_badge( $feature_request ) {
        $status = $feature_request->get_status();
        if ( $status === 'publish' ) {
            return;
        }
        $label = sfr_get_status_label( $status );
        if ( empty( $label ) ) {
            return;
        }
        $status_colors = sfr_get_status_colors( $status );
        ?>
		<span class="sfr-status-badges">
			<span class="sfr-status-badge sfr-status-badge--<?php 
        echo esc_attr( $status );
        ?>" style="background: <?php 
        echo esc_attr( $status_colors['background'] );
        ?>; color: <?php 
        echo esc_attr( $status_colors['color'] );
        ?>;">
				<?php 
        echo $label;
        ?>
			</span>
		</span>
		<?php 
    }

    /**
     * Loop item author.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_author( $feature_request ) {
        if ( !SFR_Post_Types::is_type( 'single' ) ) {
            return;
        }
        ?>
		<span class="sfr-author">
			<?php 
        echo get_avatar(
            $feature_request->post->post_author,
            40,
            '',
            false,
            array(
                'width'  => 20,
                'height' => 20,
            )
        );
        ?>
			<?php 
        printf( '%s %s', $feature_request->get_author_display_name(), __( 'shared this idea', 'simple-feature-requests' ) );
        ?>
		</span>
		<?php 
    }

    /**
     * Loop item posted date.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_posted_date( $feature_request ) {
        ?>
		<span class="sfr-date">
			<?php 
        echo get_the_date( '', $feature_request->post->id );
        ?>
		</span>
		<?php 
    }

    /**
     * Loop item comment count.
     *
     * @param SFR_Feature_Request $feature_request
     */
    public static function loop_item_comment_count( $feature_request ) {
        if ( SFR_Post_Types::is_type( 'single' ) || !sfr_comments_enabled() ) {
            return;
        }
        $comment_count = get_comments_number( $feature_request->post->ID );
        ?>
		<span class="sfr-comment-count sfr-u-nowrap">
			<?php 
        printf( _n(
            '%d comment',
            '%d comments',
            $comment_count,
            'simple-feature-requests'
        ), $comment_count );
        ?>
		</span>
		<?php 
    }

    /**
     * Display comments.
     */
    public static function comments() {
        if ( !SFR_Post_Types::is_type( 'single' ) || !sfr_comments_enabled() ) {
            return;
        }
        if ( comments_open() ) {
            ?>
			<div class="sfr-comments">
				<?php 
            comments_template();
            ?>
			</div>
		<?php 
        }
    }

    /**
     * Back to archive link.
     */
    public static function back_to_archive_link() {
        $href = sfr_apply_filters( 'sfr_archive_link', SFR_Feature_Request::board_link() );
        ?>
		<a href="<?php 
        echo esc_attr( $href );
        ?>" class="sfr-back-to-archive-link">
			<?php 
        $default_plural_name = sfr_apply_filters( 'sfr_plural_request_name', 'Feature Requests', true );
        ?>
			<?php 
        echo sprintf( __( '&larr; All %s', 'simple-feature-requests' ), $default_plural_name );
        ?>
		</a>
		<?php 
    }

}
