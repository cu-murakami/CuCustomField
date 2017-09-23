<?php

/**
 * [ControllerEventListener] PetitCustomField
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			PetitCustomField
 * @license			MIT
 */
class PetitCustomFieldControllerEventListener extends BcControllerEventListener
{

	/**
	 * 登録イベント
	 *
	 * @var array
	 */
	public $events = array(
		'initialize',
		'Blog.Blog.beforeRender',
		'Blog.BlogPosts.beforeRender',
	);

	/**
	 * petit_custom_fieldヘルパー
	 * 
	 * @var PetitCustomFieldHelper
	 */
	public $PetitCustomField = null;

	/**
	 * petit_custom_field設定情報
	 * 
	 * @var array
	 */
	public $petitCustomFieldConfigs = array();

	/**
	 * petit_custom_fieldモデル
	 * 
	 * @var Object
	 */
	public $PetitCustomFieldModel = null;

	/**
	 * petit_custom_field設定モデル
	 * 
	 * @var Object
	 */
	public $PetitCustomFieldConfigModel = null;

	/**
	 * petit_custom_fieldフィールド名設定データ
	 * 
	 * @var array
	 */
	public $settingsPetitCustomField = array();

	/**
	 * initialize
	 * 
	 * @param CakeEvent $event
	 */
	public function initialize(CakeEvent $event)
	{
		$Controller						 = $event->subject();
		// PetitCustomFieldヘルパーの追加
		$Controller->helpers[]			 = 'PetitCustomField.PetitCustomField';
		$this->settingsPetitCustomField	 = Configure::read('petitCustomField');
	}

	/**
	 * blogBeforeRender
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogBeforeRender(CakeEvent $event)
	{
		$Controller = $event->subject();
		$this->setUpModel($Controller);

		// プレビューの際は編集欄の内容を送る
		// 設定値を送る
		$Controller->viewVars['customFieldConfig'] = $this->settingsPetitCustomField;

		// プレビュー判定の仕様が3系から4系で変わっている
		if (version_compare($Controller->siteConfigs['version'], '4.0.0', '>=')) {
			$isPreview = $Controller->BcContents->preview;
		} else {
			$isPreview = $Controller->preview;
		}

		if ($isPreview) {
			if (!empty($Controller->request->data['PetitCustomField'])) {
				$Controller->viewVars['post']['PetitCustomField'] = $Controller->request->data['PetitCustomField'];

				$this->PetitCustomFieldModel->publicConfigData = $this->petitCustomFieldConfigs;

				$fieldConfigField																			 = $this->PetitCustomFieldConfigModel->PetitCustomFieldConfigMeta->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->petitCustomFieldConfigs['PetitCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$defaultFieldValue[$this->petitCustomFieldConfigs['PetitCustomFieldConfig']['content_id']]	 = Hash::combine($fieldConfigField, '{n}.PetitCustomFieldConfigField.field_name', '{n}.PetitCustomFieldConfigField');
				$this->PetitCustomFieldModel->publicFieldConfigData											 = $defaultFieldValue;
			}
		}
	}

	/**
	 * blogPostsBeforeRender
	 * 
	 * @param CakeEvent $event
	 */
	public function blogBlogPostsBeforeRender(CakeEvent $event)
	{
		$Controller = $event->subject();
		$this->setUpModel($Controller);

		// 設定値を送る
		$Controller->viewVars['customFieldConfig'] = $this->settingsPetitCustomField;

		if (!$this->petitCustomFieldConfigs) {
			return;
		}

		// ブログ記事編集画面で実行
		// - startup で処理したかったが $Controller->request->data に入れるとそれを全て上書きしてしまうのでダメだった
		if ($Controller->request->params['action'] == 'admin_edit') {
			$Controller->request->data['PetitCustomFieldConfig'] = $this->petitCustomFieldConfigs['PetitCustomFieldConfig'];

			if ($this->petitCustomFieldConfigs['PetitCustomFieldConfig']['status']) {
				$fieldConfigField = $this->PetitCustomFieldConfigMetaModel->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->petitCustomFieldConfigs['PetitCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$Controller->set('fieldConfigField', $fieldConfigField);

				// フィールド設定から初期値を生成
				$defaultFieldValue								 = Hash::combine($fieldConfigField, '{n}.PetitCustomFieldConfigField.field_name', '{n}.PetitCustomFieldConfigField.default_value');
				$this->PetitCustomFieldModel->keyValueDefaults	 = array('PetitCustomField' => $defaultFieldValue);
				$defalut										 = $this->PetitCustomFieldModel->defaultValues();
				// 初期値と存在値をマージする
				if (!empty($Controller->request->data['PetitCustomField'])) {
					$Controller->request->data['PetitCustomField'] = Hash::merge($defalut['PetitCustomField'], $Controller->request->data['PetitCustomField']);
				} else {
					$Controller->request->data['PetitCustomField'] = $defalut['PetitCustomField'];
				}
			}
		}

		// ブログ記事追加画面で実行
		if ($Controller->request->params['action'] == 'admin_add') {
			$Controller->request->data['PetitCustomFieldConfig'] = $this->petitCustomFieldConfigs['PetitCustomFieldConfig'];

			if ($this->petitCustomFieldConfigs['PetitCustomFieldConfig']['status']) {
				$fieldConfigField = $this->PetitCustomFieldConfigMetaModel->find('all', array(
					'conditions' => array(
						'PetitCustomFieldConfigMeta.petit_custom_field_config_id' => $this->petitCustomFieldConfigs['PetitCustomFieldConfig']['id']
					),
					'order'		 => 'PetitCustomFieldConfigMeta.position ASC',
					'recursive'	 => -1,
				));
				$Controller->set('fieldConfigField', $fieldConfigField);

				// フィールド設定から初期値を生成
				if (empty($Controller->request->data['PetitCustomField'])) {
					$defaultFieldValue								 = Hash::combine($fieldConfigField, '{n}.PetitCustomFieldConfigField.field_name', '{n}.PetitCustomFieldConfigField.default_value');
					$this->PetitCustomFieldModel->keyValueDefaults	 = array('PetitCustomField' => $defaultFieldValue);
					$defalut										 = $this->PetitCustomFieldModel->defaultValues();
					$Controller->request->data['PetitCustomField']	 = $defalut['PetitCustomField'];
				}
			}
		}
	}

	/**
	 * モデル登録用メソッド
	 * 
	 * @param Controller $Controller
	 */
	private function setUpModel($Controller)
	{
		if (ClassRegistry::isKeySet('PetitCustomField.PetitCustomFieldConfig')) {
			$this->PetitCustomFieldConfigModel = ClassRegistry::getObject('PetitCustomField.PetitCustomFieldConfig');
		} else {
			$this->PetitCustomFieldConfigModel = ClassRegistry::init('PetitCustomField.PetitCustomFieldConfig');
		}
		// $this->petitCustomFieldConfigs = $this->PetitCustomFieldConfigModel->read(null, $Controller->BlogContent->id);
		$this->petitCustomFieldConfigs					 = $this->PetitCustomFieldConfigModel->find('first', array(
			'conditions' => array('PetitCustomFieldConfig.content_id' => $Controller->BlogContent->id),
			'recurseve'	 => -1,
		));
		$this->PetitCustomFieldModel					 = ClassRegistry::init('PetitCustomField.PetitCustomField');
		$this->PetitCustomFieldModel->publicConfigData	 = $this->petitCustomFieldConfigs;

		if (ClassRegistry::isKeySet('PetitCustomField.PetitCustomFieldConfigMeta')) {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::getObject('PetitCustomField.PetitCustomFieldConfigMeta');
		} else {
			$this->PetitCustomFieldConfigMetaModel = ClassRegistry::init('PetitCustomField.PetitCustomFieldConfigMeta');
		}
	}

}
