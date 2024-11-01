(function( $, document ) {
	var sfr = {

		/**
		 * Set up cache with common elements and vars.
		 */
		cache: function() {
			sfr.vars = {};

			sfr.vars.vote_button_selector = '[data-sfr-vote]';
			sfr.vars.button_status_classes = {
				'voting': 'sfr-vote-button--voting',
				'voted': 'sfr-vote-button--voted'
			};
			sfr.vars.toggle_button_selector = '[data-sfr-toggle]';
			sfr.vars.toggle_user_type_selector = '[data-sfr-toggle-submission-user-type]';

			sfr.els = {};

			sfr.els.container = $( '.sfr-container' );
			sfr.els.filters = $( '.sfr-filters' );
			sfr.els.submission_form = {
				'form': $( '.sfr-form--submission' ),
				'title': $( '.sfr-form--submission .sfr-form__title' ),
				'reveal': $( '.sfr-form--submission .sfr-form__reveal' ),
				'loader': $( '.sfr-search-field__icon--loader' ),
				'clear': $( '.sfr-search-field__icon--clear' ),
				'choices': {
					'container': $( '.sfr-form--submission .sfr-form__choices' ),
					'count': $( '.sfr-form--submission .sfr-form__choices-count' ),
					'vote': $( '.sfr-form--submission .sfr-form__choices-vote' ),
					'or': $( '.sfr-form--submission .sfr-form__choices-or' ),
					'post': $( '.sfr-form--submission .sfr-form__choices-post' )
				}
			};
			sfr.els.loop = {
				'container': $( '.sfr-content' ),
			};
		},

		/**
		 * Run on doc ready.
		 */
		on_ready: function() {
			sfr.cache();
			sfr.setup_vote_buttons();
			sfr.setup_toggle_buttons();
			sfr.setup_toggle_user_type();
			sfr.setup_submission_form();
			sfr.setup_image_uploader();
			sfr.setup_image_gallery();
		},

		/**
		 * Setup vote buttons.
		 */
		setup_vote_buttons: function() {
			$( document ).on( 'click', sfr.vars.vote_button_selector, function() {
				var $button = $( this );

				if ( sfr.is_voting( $button ) ) {
					return;
				}

				if ( sfr.has_user_voted( $button ) ) {
					sfr.update_vote_count( $button, 'remove' );
					return;
				}

				sfr.update_vote_count( $button, 'add' );
			} );
		},

		/**
		 * Update vote count.
		 *
		 * @key $button
		 * @key type add|remove
		 */
		update_vote_count: function( $button, type ) {
			var $button_text = $button.text(),
				$button_class = $button.attr( 'class' );

			sfr.add_button_status_class( $button, 'voting' );
			$button.text( sfr_vars.il8n.voting + '...' );

			var post_id = sfr.get_post_id( $button ),
				$badge = $button.closest( '.sfr-vote-badge' ),
				$votes_counter = $badge.find( '.sfr-vote-badge__count strong' ),
				$votes_text = $badge.find( '.sfr-vote-badge__count span' ),
				data = {
					'action': 'sfr_update_vote_count',
					'post_id': post_id,
					'type': type,
					'nonce': sfr_vars.nonce
				};

			$.post( sfr_vars.ajax_url, data, function( response ) {
				if ( ! response.success ) {
					sfr.display_notice( response.message, 'error' );
					$button.attr( 'class', $button_class ).text( $button_text );
					return;
				}

				if ( type === 'add' ) {
					$button.text( sfr_vars.il8n.voted );
					sfr.add_button_status_class( $button, 'voted' );
					$votes_counter.text( response.votes );
					$votes_text.text( response.votes_wording );
					return;
				}

				sfr.remove_button_status_classes( $button );
				$button.text( sfr_vars.il8n.vote );
				$votes_counter.text( response.votes );
				$votes_text.text( response.votes_wording );
			} );
		},

		/**
		 * Display notice.
		 *
		 * @key message
		 * @key type
		 */
		display_notice: function( message, type ) {
			alert( message );
		},

		/**
		 * Get post ID from button.
		 *
		 * @key $button
		 * @return {Number}
		 */
		get_post_id: function( $button ) {
			return parseInt( $button.data( 'sfr-vote' ) );
		},

		/**
		 * Has user voted?
		 *
		 * @key $button
		 */
		has_user_voted: function( $button ) {
			return $button.hasClass( sfr.vars.button_status_classes.voted );
		},

		/**
		 * Is voting in progress?
		 *
		 * @key $button
		 */
		is_voting: function( $button ) {
			return $button.hasClass( sfr.vars.button_status_classes.voting );
		},

		/**
		 * Add button status classes.
		 *
		 * @key $button
		 * @key type
		 */
		add_button_status_class: function( $button, type ) {
			sfr.remove_button_status_classes( $button );

			$button.addClass( sfr.vars.button_status_classes[ type ] );
		},

		/**
		 * Remove button status classes.
		 *
		 * @key $button
		 */
		remove_button_status_classes: function( $button ) {
			$.each( sfr.vars.button_status_classes, function( index, status_class ) {
				$button.removeClass( status_class );
			} );
		},

		/**
		 * Setup toggle buttons.
		 */
		setup_toggle_buttons: function() {
			$( document ).on( 'click', sfr.vars.toggle_button_selector, function() {
				var $button = $( this ),
					$toggle = $( '.sfr-js-toggle-' + $button.data( 'sfr-toggle' ) );

				$toggle.toggle();
			} );
		},

		/**
		 * Setup toggle user type.
		 */
		setup_toggle_user_type: function() {
			$( document ).on( 'click', sfr.vars.toggle_user_type_selector, function() {
				var $button = $( this ),
					type = $button.data( 'sfr-toggle-submission-user-type' );

				$( '[name="sfr-login-user-type"]' ).val( type );
			} );
		},

		/**
		 * Setup submission form.
		 */
		setup_submission_form: function() {
			if ( sfr.els.submission_form.form.length <= 0 ) {
				return;
			}

			sfr.els.submission_form.title.keypress( function( e ) {
				if ( e.which === 13 ) {
					e.preventDefault();
				}
			} );

			var timeout_id = null;

			sfr.els.submission_form.title.keyup( function( e ) {
				clearTimeout( timeout_id );

				timeout_id = setTimeout( function() {
					sfr.search_feature_requests( e.target.value );
				}, 500 );
			} );

			sfr.els.submission_form.choices.post.on( 'click', function() {
				sfr.reveal_submission_form();
				return false;
			} );

			$( '.sfr-js-clear-search-field' ).on( 'click', function() {
				sfr.els.submission_form.title.val('').keyup();
				$( this ).hide();
			} );
		},

		/**
		 * Search feature requests based on string.
		 *
		 * @key search
		 */
		search_feature_requests: function( search ) {
			sfr.update_query_args( 'search', search );
			sfr.toggle_loader( 'show' );

			var data = {
				'action': 'sfr_search_feature_requests',
				'nonce': sfr_vars.nonce,
				'paged': sfr_vars.paged,
			};

			$.extend( data, sfr.get_url_parameters( window.location.href ) );

			// Add here so the characters aren't encoded when using $.extend()
			data.search = search;

			$.post( sfr_vars.ajax_url, data, function( response ) {
				if ( ! response.success && ! response.html ) {
					sfr.display_notice( response.message, 'error' );
					sfr.toggle_loader( 'hide' );
					return;
				}

				if ( ! response.success && search.length > 0 ) {
					sfr.reveal_submission_form();
				} else {
					sfr.hide_submission_form();
				}

				sfr.els.loop.container.html( response.html );
				sfr.replace_pagination( response.pagination );
				sfr.toggle_filters( response );
				sfr.toggle_choices( response );
				sfr.toggle_loader( 'hide' );
			} );
		},

		/**
		 * Update a URL query arg.
		 *
		 * @param key
		 * @param value
		 */
		update_query_args: function( key, value ) {
			key = encodeURIComponent( key );

			var url = window.location.href,
				params = sfr.get_url_parameters( url );

			if ( value.length > 0 ) {
				params[ key ] = value;
			} else {
				delete params[ key ];
			}

			url = url.split( '?' )[ 0 ];

			if ( $.param( params ).length > 0 ) {
				url += "?" + $.param( params );
			}

			window.history.pushState( { key: key, value: value }, document.title, url );
		},

		/**
		 * Get URL parameters.
		 *
		 * @param url
		 *
		 * @return object
		 */
		get_url_parameters: function( url ) {
			var result = {},
				searchIndex = url.indexOf( "?" );

			if ( searchIndex === - 1 ) {
				return result;
			}

			var sPageURL = url.substring( searchIndex + 1 ),
				sURLVariables = sPageURL.split( '&' );

			for ( var i = 0; i < sURLVariables.length; i ++ ) {
				var sParameterName = sURLVariables[ i ].split( '=' );
				result[ sParameterName[ 0 ] ] = sParameterName[ 1 ];
			}

			return result;
		},

		/**
		 * Replace pagination.
		 *
		 * @param pagination
		 */
		replace_pagination: function( pagination ) {
			if ( $( '.sfr-pagination' ).length <= 0 ) {
				return;
			}

			$( '.sfr-pagination' ).replaceWith( pagination );
		},

		/**
		 * Toggle filters.
		 *
		 * @param search
		 */
		toggle_filters: function( response ) {
			sfr.els.filters.hide();

			if ( response.search.length <= 0 ) {
				sfr.els.filters.show();
			}
		},

		/**
		 * Toggle choices.
		 *
		 * @param response
		 */
		toggle_choices: function( response ) {
			sfr.els.submission_form.choices.vote.show();
			sfr.els.submission_form.choices.or.show();
			sfr.els.submission_form.choices.post.show();
			sfr.els.submission_form.choices.count.text( response.count );
			sfr.els.submission_form.choices.container.show();

			if ( response.count <= 0 || response.search.length <= 0 ) {
				sfr.els.submission_form.choices.container.hide();
			}
		},

		/**
		 * Reveal submission form.
		 */
		reveal_submission_form: function() {
			sfr.els.submission_form.choices.or.hide();
			sfr.els.submission_form.choices.post.hide();
			sfr.els.submission_form.reveal.show();
			sfr.focus_submission_title();
		},

		/**
		 * Hide submission form.
		 */
		hide_submission_form: function() {
			sfr.els.submission_form.reveal.hide();
		},

		/**
		 * Focus in submission title field.
		 */
		focus_submission_title: function() {
			var value = sfr.els.submission_form.title.val();
			sfr.els.submission_form.title.focus();
			sfr.els.submission_form.title.val( '' ).val( value );
		},

		/**
		 * Toggle loader.
		 *
		 * @param visiblity
		 */
		toggle_loader: function( visiblity ) {
			if ( typeof visiblity === 'undefined' ) {
				return;
			}

			if ( visiblity === 'show' ) {
				sfr.els.submission_form.clear.hide();
				sfr.els.submission_form.loader.show();
			} else {
				sfr.els.submission_form.loader.hide();

				if ( sfr.els.submission_form.title.val().length > 0 ) {
					sfr.els.submission_form.clear.show();
				}
			}
		},

		/**
		 * Image upload handler
		 */
		setup_image_uploader: function(){
			if ( typeof Dropzone !== 'undefined' ) {
				var uploader = $('#sfr-image-uploader');

				if( !uploader.length ){
					return;
				}

				var submit_btn = sfr.els.submission_form.form.find('.sfr-form__button');

				uploader.dropzone({
					url: sfr_vars.ajax_url,
					params: {
						action: 'sfr_set_feature_request_attachments',
						nonce: sfr_vars.attachment_nonce
					},
					init: function(){

						sfr.check_for_already_submitted_attachments( uploader, this );

						this.on('successmultiple', function(data, response){
							if( response.success && response.data.attachment_ids ){

								uploader.find('[name="attachment_ids[]"]').remove();

								$.each( response.data.attachment_ids, function( i, id ){
									uploader.append( '<input type="hidden" name="attachment_ids[]" value="'+id+'" />' );
								});
							}
						});

						this.on('processingmultiple', function(){
							submit_btn.prop('disabled', true);
						});

						this.on('successmultiple', function(){
							submit_btn.removeAttr('disabled');
							submit_btn.trigger('click');
						});

						this.on('removedfile', function(file){
							if( ! file.attachment_id ){
								return;
							}

							$('[name="attachment_ids[]"][value="'+file.attachment_id+'"]').remove();
						});
					},
					acceptedFiles: 'image/*',
					uploadMultiple: true,
					paramName: 'attachments',
					autoProcessQueue: false,
					addRemoveLinks: true,
					hiddenInputContainer: uploader[0],
					maxFiles: 5,
					maxFilesize: sfr_vars['attachments_max_filesize'],
					parallelUploads: 5
				});

				var dropzone = Dropzone.forElement( uploader.get(0) );

				sfr.els.submission_form.form.on('submit', function(e){

					if( !dropzone.getQueuedFiles().length ){
						return;
					}

					e.preventDefault();
					dropzone.processQueue();
				});
			}
		},

		/**
		 * Initiate already added attachments
		 *
		 * @param {jQuery} el jquery element of attachments container
		 * @param {Dropzone} dropzone Dropzone instance
		 */
		check_for_already_submitted_attachments: function( el, dropzone ){
			if ( typeof Dropzone !== 'undefined' ) {
				var attachment_ids = el.find( '[name="attachment_ids[]"]' );

				if( ! attachment_ids.length ){
					return;
				}

				var values = attachment_ids.toArray().map( function( input ){ return $( input ).val(); });

				$.get({
					url: sfr_vars.ajax_url,
					data: {
						action: 'sfr_get_feature_request_attachments',
						nonce: sfr_vars.attachment_nonce,
						attachment_ids: values
					},
					success: function(response){
						if( response.success && response.data.attachments ){
							$.each( response.data.attachments, function( i, attachment ){

								var mockFile = { name: attachment.name, size: attachment.size, attachment_id: i };

								dropzone.emit("addedfile", mockFile);
								dropzone.options.thumbnail.call(dropzone, mockFile, attachment.url);
								dropzone.emit("complete", mockFile);
							});
						}
					}
				});
			}
		},

		/**
		 * Image gallery on frontend
		 */
		setup_image_gallery: function(){

			var wrapper 	= $('.sfr-attachments');

			if( ! wrapper.length ){
				return;
			}

			wrapper.on('click', '.sfr-attachment', function(){

				var attachments = $(this).parent().find('.sfr-attachment');
				var images 		= [];

				attachments.each(function(){

					var img = $(this).find('img');
					if( img ){
						images.push({
							src: img.data('src'),
							w: img.data('width'),
							h: img.data('height'),
						});
					}
				});

				var gallery = new PhotoSwipe( $('.pswp').get(0), PhotoSwipeUI_Default, images, {index: 0});
				gallery.init();
			});
		}
	};

	if( ( typeof Dropzone !== 'undefined' && Dropzone !== null ) && sfr_vars.allow_attachments ) {
    	Dropzone.autoDiscover = false;
	}
	$( document ).ready( sfr.on_ready );
}( jQuery, document ));