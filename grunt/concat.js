module.exports = {
	options : {
		separator : ';'
	},
	dist : {
		src : [
			'<%= paths.js %>/libs/*.js',
			'<%= paths.js %>/vendor/*.js',
			'<%= paths.js %>/app/*.js'
		],
		dest : '<%= paths.js %>/built.js',
	}
};
