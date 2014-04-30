define(['backbone'], function(Backbone) {
    return Backbone.Model.extend({
        url: 'profiles/profile',
        validation: {
            email: {
                required: true,
                pattern: 'email',
                msg: 'Please enter a valid email'
            },
            'first_name': {
                required: true,
            },
            'last_name': {
                required: true,
            }
        }
    });
});