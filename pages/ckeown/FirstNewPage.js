	AnnoJ.config = {

		tracks : [

			{
				id : '1_1',
				name : 'Gene Models (aport12.php)',
				type : 'ModelsTrack',
				path : 'Annotation Models',
				data : '/aj2/models/aport12.php',
				iconCls : 'silk_bricks',
				height : 100,
				scale : 1,
				showControls : 1,
			},
			{
				id : '2_1',
				name : 'tmp.out2',
				type : 'MethTrack',
				path : 'data',
				data : '/aj2/tracks/ckeown/FirstNewPage/data/tmp.out2.php',
				iconCls : 'salk_meth',
				height : 40,
				scale : 1,
			},
			{
				id : '2_2',
				name : 'tmp.out',
				type : 'MethTrack',
				path : 'data',
				data : '/aj2/tracks/ckeown/FirstNewPage/data/tmp.out.php',
				iconCls : 'salk_meth',
				height : 40,
				scale : 1,
			},
			{
				id : '2_3',
				name : 'Araport',
				type : 'ModelsTrack',
				path : 'data',
				data : '/aj2/tracks/ckeown/FirstNewPage/data/Araport.php',
				iconCls : 'silk_bricks',
				height : 40,
				scale : 1,
			},
		],

		active : ['2_3', '1_1'],

		title : 'brain mC2',
		genome : '/aj2/genomes/chromosome.php',
		bookmarks : 'common_bookmarks.php',
		analysis : 'analysis.php',
		location : {
			assembly : '1',
			position : '1',
			bases : 20,
			pixels : 1,
		},
		admin : {
			name : 'Huaming',
			email : 'h@salk.edu',
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
