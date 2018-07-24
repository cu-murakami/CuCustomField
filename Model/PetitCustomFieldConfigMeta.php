<?php

/**
 * [Model] PetitCustomFieldConfig
 *
 * @copyright		Copyright, Catchup, Inc.
 * @link			https://catchup.co.jp
 * @package			PetitCustomField
 * @license			MIT
 */
App::uses('PetitCustomField.PetitCustomFieldAppModel', 'Model');

class PetitCustomFieldConfigMeta extends PetitCustomFieldAppModel
{

	/**
	 * ModelName
	 * 
	 * @var string
	 */
	public $name = 'PetitCustomFieldConfigMeta';

	/**
	 * PluginName
	 * 
	 * @var string
	 */
	public $plugin = 'PetitCustomField';

	/**
	 * actsAs
	 * 
	 * @var array
	 */
	public $actsAs = array(
		'BcCache',
		'PetitCustomField.List' => array(
			'scope' => 'petit_custom_field_config_id',
		),
	);

	/**
	 * belongsTo
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'PetitCustomFieldConfig' => array(
			'className'	 => 'PetitCustomField.PetitCustomFieldConfig',
			'foreignKey' => 'petit_custom_field_config_id'
		),
	);

	/**
	 * カスタムフィールド設定メタ情報取得の際に、カスタムフィールド設定情報も併せて取得する
	 * 
	 * @param array $results
	 * @param boolean $primary
	 */
	public function afterFind($results, $primary = false)
	{
		parent::afterFind($results, $primary);
		if ($results) {
			if (ClassRegistry::isKeySet('PetitCustomField.PetitCustomFieldConfigField')) {
				$this->PetitCustomFieldConfigFieldModel = ClassRegistry::getObject('PetitCustomField.PetitCustomFieldConfigField');
			} else {
				$this->PetitCustomFieldConfigFieldModel = ClassRegistry::init('PetitCustomField.PetitCustomFieldConfigField');
			}

			$this->PetitCustomFieldConfigFieldModel->Behaviors->KeyValue->KeyValue = $this->PetitCustomFieldConfigFieldModel;
			foreach ($results as $key => $value) {
				// $data = $this->PetitCustomFieldModel->getSection($Model->id, $this->PetitCustomFieldModel->name);
				// $data = $this->{$this->modelClass}->getSection($foreignId, $this->modelClass);
				// getMax等のfindの際にはモデル名をキーとしたデータが入ってこないため判定
				if (isset($value['PetitCustomFieldConfigMeta'])) {
					$dataField = $this->PetitCustomFieldConfigFieldModel->getSection($value['PetitCustomFieldConfigMeta']['field_foreign_id'], 'PetitCustomFieldConfigField');
					if ($dataField) {
						// マルチチェックの初期値の配列化に対応
						$dataField										 = $this->splitData($dataField);
						$_dataField['PetitCustomFieldConfigField']		 = $dataField;
						$results[$key]['PetitCustomFieldConfigField']	 = $_dataField['PetitCustomFieldConfigField'];
					}
				}
			}
		}
		return $results;
	}

}
