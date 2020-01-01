	AnnoJ.config = {

		tracks : [

			{
				id : '1_1',
				name : 'Gene Models (hg38_Gencode_v24.php)',
				type : 'ModelsTrack',
				path : 'Annotation Models',
				data : '/aj2/models/hg38_Gencode_v24.php',
				iconCls : 'silk_bricks',
				height : 100,
				scale : 1,
				showControls : 1,
			},
		],

		active : ['1_1'],

		title : 'hg38_Gencode_v24',
		genome : '/aj2/genomes/hg38.php',
		bookmarks : 'common_bookmarks.php',
		analysis : 'analysis.php',
		location : {
			assembly : '1',
			position : '1',
			bases : 20,
			pixels : 1,
		},
		admin : {
			name : 'Junhao Li',
			email : 'jul307@ucsd.edu',
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
