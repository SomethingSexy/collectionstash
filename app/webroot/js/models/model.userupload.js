var UserUploadModel = Backbone.Model.extend({
	url : function() {
		return '/user_uploads/upload/' + this.id;
	}
});
