var FLBuilderNumber;

(function($) {
	
	/**
	 * Class for Post Carousel Module
	 *
	 * @since 1.6.1
	 */
	FLBuilderNumber = function( settings ){

		// set params
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-number';
		this.layout				 = settings.layout;
		this.type				 = settings.type;
		this.number				 = settings.number;
		this.max				 = settings.max;
		this.speed 				 = settings.speed;
		this.delay 				 = settings.delay;
		this.breakPoints         = settings.breakPoints;
		this.currentBrowserWidth = $( window ).width();

		// initialize the menu 
		this._initNumber();
		
	};

	FLBuilderNumber.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		layout 	                : '',
		type 	                : '',
		number 	                : 0,
		max 	                : 0,
		speed 					: 0,
		delay 					: 0,

		_initNumber: function(){

			var self = this;

			if( typeof jQuery.fn.waypoint !== 'undefined' ) {
				$( this.wrapperClass ).waypoint({
					offset: '80%',
					triggerOnce: true,
					handler: function( direction ){
						self._initCount();
					}
				});
			} else {
				self._initCount();
			}

		},

		_initCount: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' );

			if( !isNaN( this.delay ) && this.delay > 0 ) {
				setTimeout( function(){
					if( this.layout == 'circle' ){
						this._triggerCircle();
					} else if( this.layout == 'bars' ){
						this._triggerBar();
					}
					this._countNumber();
				}.bind( this ), this.delay * 1000 );
			}
			else {
				if( this.layout == 'circle' ){
					this._triggerCircle();
				} else if( this.layout == 'bars' ){
					this._triggerBar();
				}
				this._countNumber();
			}
		},

		_countNumber: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' ),
				$string = $number.find( '.fl-number-int' ),
				current = 0;


		    $string.prop( 'Counter',0 ).animate({
		        Counter: this.number
		    }, {
		        duration: this.speed,
		        easing: 'swing',
		        step: function ( now ) {
		            $string.text( Math.ceil( now ) );
		        }
		    });

		},

		_triggerCircle: function(){

			var $bar   = $( this.wrapperClass ).find( '.fl-bar' ),
				r      = $bar.attr('r'),
				circle = Math.PI*(r*2),
				val    = this.number,
				max    = this.type == 'percent' ? 100 : this.max;
   
			if (val < 0) { val = 0;}
			if (val > max) { val = max;}
			
			if( this.type == 'percent' ){
				var pct = ( ( 100 - val ) /100) * circle;			
			} else {
				var pct = ( 1 - ( val / max ) ) * circle;
			}

		    $bar.animate({
		        strokeDashoffset: pct
		    }, {
		        duration: this.speed,
		        easing: 'swing'
		    });
			
		},

		_triggerBar: function(){

			var $bar = $( this.wrapperClass ).find( '.fl-number-bar' );

			if( this.type == 'percent' ){
				var number = this.number > 100 ? 100 : this.number;
			} else {
				var number = Math.ceil( ( this.number / this.max ) * 100 );
			}

		    $bar.animate({
		        width: number + '%'
		    }, {
		        duration: this.speed,
		        easing: 'swing'
		    });

		}
	
	};
		
})(jQuery);