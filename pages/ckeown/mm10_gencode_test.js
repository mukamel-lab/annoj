	AnnoJ.config = {

		tracks : [

			{
				id : '1_1',
				name : '31-08',
				type : 'MethTrack',
				path : 'tracks',
				data : '/aj2/tracks/ckeown/mm10_gencode_test/tracks/31-08.php',
				iconCls : 'salk_meth',
				height : 40,
				scale : 1,
			},
			{
				id : '2_1',
				name : 'Gene Models (gencodevM9.php)',
				type : 'ModelsTrack',
				path : 'Annotation Models',
				data : '/aj2/models/gencodevM9.php',
				iconCls : 'silk_bricks',
				height : 100,
				scale : 1,
				showControls : 1,
			},
		],

		active : ['2_1', '1_1'],

		title : 'mm10 gencode7 test',
		genome : '/aj2/genomes/mm10.php',
		bookmarks : 'common_bookmarks.php',
		analysis : 'analysis.php',
		location : {
			assembly : '1',
			position : '1',
			bases : 20,
			pixels : 1,
		},
		admin : {
			name : 'Chris Keown',
			email : 'christopher.keown@gmail.com',
			notes : '',
		},
		settings : {
			baseline : 0,
			scale : 2,
			multi : 1,
			yaxis : 20,
		},
		citation : '',
		jbuilder : 4.0,
	};
