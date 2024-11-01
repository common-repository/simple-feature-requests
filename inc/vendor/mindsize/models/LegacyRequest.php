<?php

namespace SFR\Models;

/**
 * This class interacts with the legacy requests that are stored as WordPress Custom Post Types.
 * 
 * DO NOT USE THIS FOR ANY NEW DEVELOPMENT. It exists to sync the old data ONLY!
 */
class LegacyRequest {

    /**
     * Post meta flag to show a request has been sync'd to the new tables.
     *
     * @var string
     */
    private $syncd_flag = 'sfr_data_syncd';

    private $post_type = 'cpt_feature_requests';

    /**
     * Copies the data from WP tables into the new datastructure.
     * 
     * This code may be ugly. Sorry, not sorry.
     * 
     * @param int $legacy_id The id of the CPT from the wp_posts table.
     */
    public function sync( $legacy_post ) {
        error_log( "Syncing: " . $legacy_post->ID );

        // If the request has a board, lets create that first
        $legacy_boards = \wp_get_post_terms( $legacy_post->ID, 'request_board');
        $boards = [];
        if( !empty( $legacy_boards ) ) {
            foreach( $legacy_boards as $board ) {
                $new_board = \SFR\Models\Board::firstOrCreate([
                    'name'          => $board->name,
                    'description'   => $board->description
                ]);
                $boards[] = $new_board->id;
            }
        }

        // Build the request
        $request = \SFR\Models\Request::firstOrCreate([
            'name'           => $legacy_post->post_title,
            'description'    => $legacy_post->post_content,
            'status'         => $legacy_post->sfr_status,
            'user_id'        => $legacy_post->post_author,
        ]);

        // Link the request to its board
        foreach($boards as $board ) {
            \SFR\Models\RequestBoard::firstOrCreate([
                'request_id' => $request->id,
                'board_id'   => $board
            ]);
        }

        // Build the comments
        $legacy_comments = \get_comments( [ 'post_id' => $legacy_post->ID ]);
        foreach( $legacy_comments as $comment ) {
            \SFR\Models\RequestComment::firstOrCreate([
                'request_id' => $request->id,
                'user_id' => (\get_user_by( 'email', $comment->comment_author_email ))->ID,
                'comment' => $comment->comment_content,
                'created_at' => $comment->comment_date,
                'updated_at' => $comment->comment_date
            ]);
        }

        // Build the votes
        

        \update_post_meta( $legacy_post->ID, $this->syncd_flag, date('D, d M Y H:i:s') );
    }

    /**
     * Returns a list the post CPTs that have not yet been sync'd to the new table. 
     * CPTs are flagged after they have been sync'd
     *
     * @return void
     */
    public function not_syncd() {
        $query = new \WP_Query([
            'post_type' => $this->post_type,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => $this->syncd_flag,
                    'value' => false,
                    'type' => 'BOOLEAN'
                ],
                [
                    'key' => $this->syncd_flag,
                    'compare' => 'NOT EXISTS'
                ]
            ]
        ]);

        return $query->posts;
    }
}