/**
 * @preserve Galleria Classic Theme 2011-08-01
 * http://galleria.aino.se
 *
 * Copyright (c) 2011, Aino
 * Licensed under the MIT license.
 */

/*global jQuery, Galleria */

Galleria.requires(1.25, 'This version of Classic theme requires Galleria 1.2.5 or later');
( function($) {

	Galleria.addTheme({
		name : 'classic',
		author : 'Galleria',
		defaults : {
			transition : 'slide',
			thumbCrop : 'height',
			_locale : {
				enter_fullscreen : "Enter fullscreen",

				exit_fullscreen : "Exit fullscreen",

				popout_image : "Popout image",
			},
			// set this to false if you want to show the caption all the time:
			_toggleInfo : true,
			_showFullscreen : true,
			_showPopout : true,
			_showProgress : true,
			_showTooltip : true,
			_showDetailInfo : true
		},
		init : function(options) {

			// add some elements
			// this.addElement('info-link','info-close','balls');
			// this.append({
			// 'info' : ['info-link','info-close','balls']
			// });
			this.addElement("bar", "fullscreen", "popout", "progress", "info-link", "detail", "info-close","detail-text");

			this.append({

				stage : "progress",

				container : ["bar", "tooltip", "detail"],

				bar : ["fullscreen", "popout", "info"],
				
				detail : ["info-link", "detail-text", "info-close"]

			});

			this.prependChild("info", "counter");

			// cache some stuff
			var info = this.$('info-link,info-close,detail-text'), touch = Galleria.TOUCH, click = touch ? 'touchstart' : 'click';

			// show loader & counter with opacity
			this.$('loader,counter').show().css('opacity', 0.4);

			// some stuff for non-touch browsers
			if(!touch) {
				this.addIdleState(this.get('image-nav-left'), {
					left : -50
				});
				this.addIdleState(this.get('image-nav-right'), {
					right : -50
				});
				// this.addIdleState( this.get('counter'), { opacity:0 });
			}
			var w = this;
			var da = this.$("popout");
			var N = this.$("fullscreen");
			var aa = this.$("bar");
			var ja = false;
			var X = options.transition;
			var P = options._locale; 
			if(options._showPopout)
				da.click(function(A) {

					w.openLightbox();

					A.preventDefault()

				});
			else {

				da.remove();

				if(options._showFullscreen) {

					this.$("s4").remove();

					this.$("info").css("right", 40);

					N.css("right", 0)

				}

			}

			if(options._showFullscreen)
				N.click(function() {
					ja ? w.exitFullscreen() : w.enterFullscreen()

				});
			else {

				N.remove();

				if(options._show_popout) {

					this.$("s4").remove();

					this.$("info").css("right", 40);

					da.css("right", 0)

				}

			}
			this.bind("fullscreen_enter", function() {
				ja = true;

				w.setOptions("transition", false);

				N.addClass("open");

				aa.css("bottom", 0);

				this.defineTooltip("fullscreen", P.exit_fullscreen);

				Galleria.TOUCH || this.addIdleState(aa, {

					bottom : -31

				})

			});

			this.bind("fullscreen_exit", function() {
				ja = false;

				Galleria.utils.clearTimer("bar");

				w.setOptions("transition", X);

				N.removeClass("open");

				aa.css("bottom", '110px');

				this.defineTooltip("fullscreen", P.enter_fullscreen);

				Galleria.TOUCH || this.removeIdleState(aa, {

					bottom : -31

				})

			});
			//toggle info
			if(options._toggleInfo === true) {
				info.bind(click, function() {
					info.toggle();
				});
			} else {
				info.show();
				this.$('info-link, info-close').hide();
			}
			
			if(options._showDetailInfo === false) {
				this.$('info-link, info-close').hide();
			}

			// bind some stuff
			this.bind('thumbnail', function(e) {

				if(!touch) {
					// fade thumbnails
					$(e.thumbTarget).css('opacity', 0.6).parent().hover(function() {
						$(this).not('.active').children().stop().fadeTo(100, 1);
					}, function() {
						$(this).not('.active').children().stop().fadeTo(400, 0.6);
					});
					if(e.index === this.getIndex()) {
						$(e.thumbTarget).css('opacity', 1);
					}
				} else {
					$(e.thumbTarget).css('opacity', this.getIndex() ? 1 : 0.6);
				}
			});

			this.bind('loadstart', function(e) {
				if(!e.cached) {
					this.$('loader').show().fadeTo(200, 0.4);
				}
				;
				this.$('detail-text').html(this.getData().detailDescription);
				this.$('info-text').toggle(this.hasInfo());

				$(e.thumbTarget).css('opacity', 1).parent().siblings().children().css('opacity', 0.6);
			});

			this.bind('loadfinish', function(e) {
				this.$('loader').fadeOut(200);
			});
		}
	});

}(jQuery));
