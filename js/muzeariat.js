function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
};

jQuery(function($){
    // now you can use jQuery code here with $ shortcut formatting
    // this executes immediately - before the page is finished loading

	// anti spam
	var r='orangutan';
    
    $('span.'+r).each(function() {
       var $this = $(this),
           value = new String($this.text());
    
           value = value.replace('['+r+']', '@');
    
           $this.replaceWith( $('<a></a>').text(value).addClass( $this.attr( 'class' ) ).attr( 'href', 'mailto:'+value ) );
    });

    /**
     * Newsletter support
     */
    
    // preload nedded components
    var img = new Image(),
        smiley = ajax.assets_url + '/img/happiness.svg';
    img.src = smiley;


    $('#newsletter')
        .attr('novalidate', true)
        .each( function() {
            var $this = $(this),
                $input = $this.find( 'input[name="ne"]'),
                $noti = $input.prev(),
                $submit = $this.find( 'input[type="submit"]'),
                showNoti = function(txt) {
                    var $msg = $noti.clone();

                    $noti.before($msg);
                    $noti.remove();

                    $msg.text( txt ).addClass('vaporize').attr('aria-hidden', 'false');                  
                },
                success = function() {
                    $this
                        .fadeOut('slow', function() {
                            $this.replaceWith( '<p class="appear-nicely dynamic-msg">Dziękujemy za rejestrację! <img width="16" alt=":)" src="' + smiley + '"></p>' );
                        });      
                };
                
            // Submit handler
            $this.submit( function(e) {
                var serializedData = $this.serialize();

                $noti = $input.prev();
                
                console.log( 'INFO: Form submit.' );

                e.preventDefault();
    
                // validate
                if( validateEmail( $input.val() ) ) { 
                    var data = {};
    
                    // Prepare ajax data
                    data = {
                        action: 'realhero_subscribe',
                        nonce: ajax.ajax_nonce,
                        data: serializedData
                    }

                    // send ajax request
                    $.ajax({
                        method: "POST",
                        url: ajax.url,
                        data: data,
                        beforeSend: function() {
                            $input.prop( 'disabled', true );
                            $submit.val('Czekaj').prop( 'disabled', true );
                        },
                        success: function( data ) {
                        
                            if( data.status == 'success' ) {
                                // showNoti( data.msg );
                                success();
                                console.log( 'INFO: Wszystko OK!' );
                            } else {
                                $input.prop( 'disabled', false );
                                $submit.val('Wyślij').prop( 'disabled', false );

                                showNoti( data.msg );
                                console.log( 'INFO: Bad response.' );
                            }
                        }
                    });
    
                    console.log( 'INFO: Email ok.' );
                } else {    
                    showNoti( 'Wpisz poprawny adres e-mail!' );
                };
            });
        });

});
