<?php

/**
 * [Config] PetitCustomField
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
/**
 * システムナビ
 */
$config['BcApp.adminNavi.petit_custom_field'] = array(
	'name'		 => 'プチ・カスタムフィールドプラグイン',
	'contents'	 => array(
		array('name'	 => '設定一覧',
			'url'	 => array(
				'admin'		 => true,
				'plugin'	 => 'petit_custom_field',
				'controller' => 'petit_custom_field_configs',
				'action'	 => 'index')
		)
	)
);
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'PetitCustomField' => [
				'title' => 'プチ・カスタムフィールドプラグイン', 
				'url' => [
					'admin' => true, 
					'plugin' => 'petit_custom_field', 
					'controller' => 'petit_custom_field_configs',
					'action' => 'index',
				]
			],
		]
]];

/**
 * プチ・カスタムフィールド用設定
 * 
 */
$config['petitCustomField'] = array(
	// フィールドタイプ種別
	'field_type'		 => array(
		'基本'	 => array(
			'text'		 => 'テキスト',
			'textarea'	 => 'テキストエリア',
			'date'		 => '日付（年月日）',
			'datetime'	 => '日時（年月日時間）',
		),
		'選択'	 => array(
			'select'	 => 'セレクトボックス',
			'radio'		 => 'ラジオボタン',
			'checkbox'	 => 'チェックボックス',
			'multiple'	 => 'マルチチェックボックス',
			'pref'		 => '都道府県リスト',
		),
		'コンテンツ'	 => array(
			'wysiwyg' => 'Wysiwyg Editor',
			'googlemaps' => 'Googleマップ',
		//'upload' => 'FileUpload',
		),
	),
	// エディターのタイプ
	'editor_tool_type'	 => array(
		'simple' => 'Simple',
		'normal' => 'Normal',
	),
	// 入力チェック種別
	'validate'			 => array(
		'HANKAKU_CHECK'	 => '半角英数チェック',
		'NUMERIC_CHECK'	 => '数字チェック',
		'NONCHECK_CHECK' => 'チェックボックス未入力チェック',
		'REGEX_CHECK'	 => '正規表現チェック',
	),
	// 文字変換種別
	'auto_convert'		 => array(
		'NO_CONVERT'		 => 'しない',
		'CONVERT_HANKAKU'	 => '半角変換',
	),
	'form_place'		 => array(
		'normal' => 'コンテンツ編集領域の下部',
		'top'	 => 'コンテンツ編集領域の上部',
	),
	// 必須選択
	'required'			 => array(
		0	 => '必須としない',
		1	 => '必須とする',
	),
);
/**
 * プチ・カスタムフィールド管理画面表示用設定
 * 
 */
$config['petitCustomFieldConfig'] = array(
	'submenu' => false
);