<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'sync_box' => [
		'title'   => __( 'Design Sync', 'fw' ),
		'type'    => 'box',
		'options' => [
			'group_sync' => [
				'type'    => 'group',
				'options' => [
					'mode' => [
						'label'   => __( 'Direction', 'fw' ),
						'type'    => 'select',
						'value'   => 'both',
						'choices' => [
							'both'       => __( 'Two-way — either side updates the other', 'fw' ),
							'to_builder' => __( 'One-way — Theme Settings update the page builder', 'fw' ),
							'off'        => __( 'Off', 'fw' ),
						],
						'desc'    => __( 'What stays in step: color presets, heading and body fonts, H1–H6, link colors, container width, site background, the primary button and Text Styles. Settings one side cannot express are left untouched on the other.', 'fw' ),
					],
				],
			],
		],
	],
];
