module.exports =  {
	styles : {
		files : ['<%= paths.style %>/**/*.scss', 'ghost/**/*.scss'],
		tasks : ['sass:dev']
	},
	scripts : {
		files : '<%= concat.dist.src %>',
		tasks : ['concat:dist']
	}
};
