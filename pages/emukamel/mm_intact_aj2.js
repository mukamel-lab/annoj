	AnnoJ.config = {

		tracks : [

			{
				id : '1_1',
				name : 'Gene Models (mm10_Gencode_vM9.php)',
				type : 'ModelsTrack',
				path : 'Annotation Models',
				data : '/aj2/models/mm10_Gencode_vM9.php',
				iconCls : 'silk_bricks',
				height : 100,
				scale : 1,
				showControls : 1,
			},
			{
				id : '2_1',
				name : 'all_cg_dmr',
				type : 'ModelsTrack',
				path : 'm_intact_aj2',
				data : '/aj2/tracks/emukamel/mm_intact_aj2/all_cg_dmr.php',
				iconCls : 'silk_bricks',
				height : 40,
				scale : 1,
			},
			{
				id : '3_1',
				name : 'mc_AM_E1',
				type : 'MethTrack',
				path : 'DNA methylation',
				data : '/aj2/tracks/emukamel/mm_intact_aj2/DNA_methylation/mc_AM_E1.php',
				iconCls : 'salk_meth',
				height : 40,
				scale : 1,
			},
			{
				id : '3_2',
				name : 'mc_AM_P15P16_L',
				type : 'MethTrack',
				path : 'DNA methylation',
				data : '/aj2/tracks/emukamel/mm_intact_aj2/DNA_methylation/mc_AM_P15P16_L.php',
				iconCls : 'salk_meth',
				height : 40,
				scale : 1,
			},
		],

		active : ['1_1', '3_2', '3_1'],

		title : 'Mm10 Intact',
		genome : '/aj2/genomes/mm10.php',
		bookmarks : '/aj2/includes/common_bookmarks.php',
		analysis : '/aj2/includes/analysis.php',
		location : {
			assembly : '5',
			position : '5000000',
			bases : 20,
			pixels : 1,
		},
		admin : {
			name : 'Eran Mukamel',
			email : 'emukamel@ucsd.edu',
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
