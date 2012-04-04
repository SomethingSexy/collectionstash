( function($) {

	$.widget("cs.comments", {

		// These options will be used as defaults

		options : {

		},

		// Set up the widget
		_create : function() {
			//Do some initial setup
			var self = this;
			//This is the type of comment we are attached to
			this.commentType = this.element.attr('data-type');
			//This is the id of the type we are attached to
			this.commentTypeID = this.element.attr('data-typeID');
			//Add a class for themeing
			this.element.addClass('comments-container');
			this.element.prepend($('<div class="title"><h3>Discussion</h3></div>'));
			//This is the comment list which will hold all comments
			this.commentsList = $('<ol></ol>').addClass('comments');
			//Add our comment list to our main element
			this.element.append(this.commentsList);

			var commentPost = self._buildComnentPost();

			this.element.append(commentPost);
			//Post button, although not sure I want this
			this.changer = $("<button>", {
				text : "Post",
				"id" : "post-comment"
			}).appendTo(this.element).button().click(function(event) {
				//Using apply here because we want to pass in the widget context
				$.cs.comments.prototype._postComment.apply(self, event);
			});
			//bind events
			$(document).on('click', '#comments ol.comments li.comment div.actions span a' , function() {
				alert('action!');
			});
			//get the initial list of comments
			$.ajax({
				type : "GET",
				dataType : 'json',
				url : '/comments/view/' + this.commentType + '/' + this.commentTypeID + '.json',
				success : function(data, textStatus, XMLHttpRequest) {
					//Need to get top level permissions first
					if(data.commentsData.comments.length > 0) {
						var cont = [];
						//Initialize an array to build the content
						$.each(data.commentsData.comments, function(index, element) {
							var commentMarkup = $.cs.comments.prototype._buildComment(element);
							cont.push(commentMarkup)
						});
						self.commentsList.append(cont.join(''));
						//save the last comment to be used later when posting
						self.lastComment = data.commentsData.comments[data.commentsData.comments.length - 1];
					} else {
						//TODO: There are no comments
					}
				}
			});
		},
		_addError : function(errorMessage) {

			$('#CommentComment').after('<div class="error-message">' + errorMessage + '</div>');
		},
		_postComment : function(event) {
			var self = this;
			var comment = $('#CommentComment').val();
			$.ajax({
				type : "post",
				data : {
					'data[Comment][type]' : this.commentType,
					'data[Comment][type_id]' : this.commentTypeID,
					'data[Comment][comment]' : comment,
					'data[Comment][last_comment_created]' : self.lastComment.Comment.created
				},
				dataType : 'json',
				url : '/comments/add.json',
				success : function(data, textStatus, XMLHttpRequest) {
					if(data.success.isSuccess) {
						$('#CommentComment').val('');
						if(data.comments.length > 0) {
							var cont = [];
							//Initialize an array to build the content
							$.each(data.comments, function(index, element) {
								var commentMarkup = $.cs.comments.prototype._buildComment(element);
								cont.push(commentMarkup)
							});
							self.commentsList.append(cont.join(''));
							//save the last comment to be used later when posting
							self.lastComment = data.comments[data.comments.length - 1];
						} else {
							//TODO: There are no comments
						}

					} else {
						$.cs.comments.prototype._addError(data.error.message);
					}

				}
			});
		},
		_buildComnentPost : function() {
			// <div class="post-comment-container">
			// <form id="CommentViewForm" accept-charset="utf-8" method="post" action="/comments/add">
			// <div style="display:none;">
			// <input type="hidden" value="POST" name="_method">
			// </div>
			// <div class="input textarea required">
			// <div class="label-wrapper">
			// <label for="CommentComment">Comment</label>
			// </div>
			// <textarea id="CommentComment" rows="6" cols="30" name="data[Comment][comment]"></textarea>
			// </div>
			// </form>
			// </div>
			var commentPost = '<div class="post-comment-container">';
			commentPost += '<form id="CommentViewForm" accept-charset="utf-8" method="post" action="/comments/add">';
			commentPost += '<div style="display:none;"><input type="hidden" value="POST" name="_method"></div>';
			commentPost += '<fieldset><ul class="form-fields">';
			commentPost += '<li><div class="input textarea"><div class="label-wrapper"><label for="CommentComment">Discuss</label></div><textarea id="CommentComment" rows="6" cols="30" name="data[Comment][comment]"></textarea></div></li>';
			commentPost += '</ul></fieldset>'
			commentPost += '</form></div>';

			return commentPost;
		},
		_buildComment : function(comment, permissions) {
			//let's try this to be fast
			/**
			 <div class="info">
			 <span class="user"></span>
			 <span class="datetime"></span>
			 </div>
			 <!-- This is the actual comment
			 <div class="text"></div>
			 */
			//IF permissions are there then these are the top level and override
			//anything at the comment level
			var commentMarkup = '<li class="comment">';
			commentMarkup += '<div class="comment-info"><span class="user">';
			commentMarkup += comment.User.username;
			commentMarkup += '</span>';
			commentMarkup += '<span class="datetime">';
			commentMarkup += comment.Comment.formatted_created;
			commentMarkup += '</span>';
			commentMarkup += '</div>';
			commentMarkup += '<div class="comment-text">';
			var test = comment.Comment.comment
			test = test.replace(/\\n/g, "<br />");
			commentMarkup += test;
			commentMarkup += '</div>';
			commentMarkup += '<div class="actions">'
			if(comment.hasOwnProperty('permissions')) {
				if(comment.permissions.edit) {
					commentMarkup += '<span><a class="link">Edit</a></span>';
				}
				if(comment.permissions.remove) {
					commentMarkup += '<span><a class="link">Delete</a></span>';
				}
			}
			commentMarkup += '</div>';
			commentMarkup += '</li>';

			return commentMarkup;
		},
		// Use the _setOption method to respond to changes to options

		_setOption : function(key, value) {

			switch( key ) {

				case "clear":
					// handle changes to clear option

					break;

			}

			// In jQuery UI 1.8, you have to manually invoke the _setOption method from the base widget

			$.Widget.prototype._setOption.apply(this, arguments);
			// In jQuery UI 1.9 and above, you use the _super method instead
			this._super("_setOption", key, value);

		},
		// Use the destroy method to clean up any modifications your widget has made to the DOM

		destroy : function() {

			// In jQuery UI 1.8, you must invoke the destroy method from the base widget

			$.Widget.prototype.destroy.call(this);
			// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method

		}
	});

}(jQuery) );
