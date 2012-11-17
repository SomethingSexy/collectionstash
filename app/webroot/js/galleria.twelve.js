(function($) {

	Galleria.addTheme({
		name : 'test',
		author : 'test',

		defaults : {

			transition : "pulse",

			transitionSpeed : 500,

			imageCrop : true,

			thumbCrop : true,

			carousel : false,

			_locale : {

				show_thumbnails : "Show thumbnails",

				hide_thumbnails : "Hide thumbnails",

				play : "Play slideshow",

				pause : "Pause slideshow",

				enter_fullscreen : "Enter fullscreen",

				exit_fullscreen : "Exit fullscreen",

				popout_image : "Popout image",

				showing_image : "Showing image %s of %s"

			},

			_showFullscreen : true,

			_showPopout : true,

			_showProgress : true,

			_showTooltip : true

		},

		init : function(t) {

			this.addElement("bar", "fullscreen", "popout", "thumblink", "s1", "s2", "s3", "s4", "progress");

			this.append({

				stage : "progress",

				container : ["bar", "tooltip"],

				bar : ["fullscreen", "play", "popout", "thumblink", "info", "s1", "s2", "s3", "s4"]

			});

			this.prependChild("info", "counter");

			var w = this, 
			R = this.$("thumbnails-container"), 
			M = this.$("thumblink"),
			 N = this.$("fullscreen"), 
			 W = this.$("play"), 
			 da = this.$("popout"), 
			 aa = this.$("bar"), 
			 ca = this.$("progress"), 
			 X = t.transition, 
			 P = t._locale, 
			 ia = false, 
			 ja = false, 
			 la = !!t.autoplay, 
			 d = false, 
			 $ = function() {

				R.height(w.getStageHeight()).width(w.getStageWidth()).css("top", ia ? 0 : w.getStageHeight() + 30)

			}, ea = function() {

				if(ia && d)
					w.play();
				
				else {
					d = la;

					w.pause()

				}

				Galleria.utils.animate(R, {

					top : ia ? w.getStageHeight() + 30 : 0

				}, {

					easing : "galleria",

					duration : 400,

					complete : function() {

						w.defineTooltip("thumblink", ia ? P.show_thumbnails : P.hide_thumbnails);

						M[ia ? "removeClass" : "addClass"]("open");
						ia = !ia

					}
				})

			};
			$();

			t._showTooltip && w.bindTooltip({

				thumblink : P.show_thumbnails,

				fullscreen : P.enter_fullscreen,

				play : P.play,

				popout : P.popout_image,

				caption : function() {

					var A = w.getData(), fa = "";

					if(A) {

						if(A.title && A.title.length)
							fa += "<strong>" + A.title + "</strong>";

						if(A.description && A.description.length)
							fa += "<br>" + A.description
					}

					return fa

				},
				counter : function() {

					return P.showing_image.replace(/\%s/, w.getIndex() + 1).replace(/\%s/, w.getDataLength())

				}
			});

			t.showInfo || this.$("info").hide();

			this.bind("play", function() {
				la = true;

				W.addClass("playing")

			});

			this.bind("pause", function() {
				la = false;

				W.removeClass("playing");

				ca.width(0)

			});

			t._showProgress && this.bind("progress", function(A) {

				ca.width(A.percent / 100 * this.getStageWidth())

			});

			this.bind("loadstart", function(A) {

				A.cached || this.$("loader").show()

			});

			this.bind("loadfinish", function() {

				ca.width(0);

				this.$("loader").hide();

				this.refreshTooltip("counter", "caption")

			});

			this.bind("thumbnail", function(A) {

				this.$(A.thumbTarget).hover(function() {

					w.setInfo(A.thumbOrder);

					w.setCounter(A.thumbOrder);

				}, function() {

					w.setInfo();

					w.setCounter();

				}).click(function() {

					ea();

				})
			});

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

				aa.css("bottom", 0);

				this.defineTooltip("fullscreen", P.enter_fullscreen);

				Galleria.TOUCH || this.removeIdleState(aa, {

					bottom : -31

				})

			});

			this.bind("rescale", $);

			if(!Galleria.TOUCH) {

				this.addIdleState(this.get("image-nav-left"), {

					left : -36

				});

				this.addIdleState(this.get("image-nav-right"), {

					right : -36

				})

			}

			M.click(ea);

			if(t._showPopout)
				da.click(function(A) {

					w.openLightbox();

					A.preventDefault()

				});
			else {

				da.remove();

				if(t._showFullscreen) {

					this.$("s4").remove();

					this.$("info").css("right", 40);

					N.css("right", 0)

				}

			}

			W.click(function() {

				w.defineTooltip("play", la ? P.play : P.pause);

				if(la)
					w.pause();
				
else {
					ia && M.click();

					w.play()

				}

			});
			if(t._showFullscreen)
				N.click(function() {
					ja ? w.exitFullscreen() : w.enterFullscreen()

				});
			else {

				N.remove();

				if(t._show_popout) {

					this.$("s4").remove();

					this.$("info").css("right", 40);

					da.css("right", 0)

				}

			}

			if(!t._showFullscreen && !t._showPopout) {

				this.$("s3,s4").remove();

				this.$("info").css("right", 10)

			}

			t.autoplay && this.trigger("play")

		}
	});

}(jQuery));
