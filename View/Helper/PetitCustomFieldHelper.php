<?php
/**
 * [Helper] PetitCustomField
 *
 * @link			http://www.materializing.net/
 * @author			arata
 * @package			PetitCustomField
 * @license			MIT
 */
class PetitCustomFieldHelper extends AppHelper {
/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcForm', 'Blog.Blog', 'BcBaser', 'BcTime', 'BcText');
	
	public $customFieldConfig = array();
	
	public function __construct(\View $View, $settings = array()) {
		parent::__construct($View, $settings);
		
		$this->customFieldConfig = Configure::read('petitCustomField');
	}
	
/**
 * 配列とキーを指定して値を取得する
 * - グループ指定のある配列に対応
 * 
 * @param int $key
 * @param array $array
 * @param string $noValue
 * @return mixied
 */
	public function arrayValue($key, $array, $noValue = '') {
		if (is_numeric($key)) {
			$key = (int) $key;
		}
		if (isset($array[$key])) {
			return $array[$key];
		}
		// グループ指定がある場合の判定
		foreach ($array as $group => $list) {
			if (isset($list[$key])) {
				return $list[$key];
			}
		}
		return $noValue;
	}
	
/**
 * フォームのタイプを判定して、タイプ別の入力フォームを生成する
 * 
 * @param array $data
 * @param string $section モデル名を指定: 複数モデルのデータの場合、ここで指定したモデル名のデータを利用する
 * @param array $options
 * @return array
 */
	public function getFormOption($data = array(), $section = '', $options = array()) {
		$formOption = array();
		
		if ($data) {
			$modelName = key($data);
			// モデル名の指定を優先する
			if ($section) {
				$modelName = $section;
			}
			// フィールドのタイプを判定用に設定する
			$fieldType = $data[$modelName]['field_type'];
			$_formOption = array(
				'type' => $fieldType,
			);
			
			switch ($fieldType) {
				case 'text':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, array('size' => $data[$modelName]['size']));
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, array('maxlength' => $data[$modelName]['max_length']));
					}
					if ($data[$modelName]['counter']) {
						$_formOption = array_merge($_formOption, array('counter' => $data[$modelName]['counter']));
					}
					if ($data[$modelName]['placeholder']) {
						$_formOption = array_merge($_formOption, array('placeholder' => $data[$modelName]['placeholder']));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'textarea':
					if ($data[$modelName]['rows']) {
						$_formOption = array_merge($_formOption, array('rows' => $data[$modelName]['rows']));
					}
					if ($data[$modelName]['cols']) {
						$_formOption = array_merge($_formOption, array('cols' => $data[$modelName]['cols']));
					}
					if ($data[$modelName]['placeholder']) {
						$_formOption = array_merge($_formOption, array('placeholder' => $data[$modelName]['placeholder']));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'date':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, array('size' => $data[$modelName]['size']));
					} else {
						$_formOption = array_merge($_formOption, array('size' => 12));
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, array('maxlength' => $data[$modelName]['max_length']));
					} else {
						$_formOption = array_merge($_formOption, array('maxlength' => 10));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'datetime':
					if ($data[$modelName]['size']) {
						$_formOption = array_merge($_formOption, array('size' => $data[$modelName]['size']));
					} else {
						$_formOption = array_merge($_formOption, array('size' => 12));
					}
					if ($data[$modelName]['max_length']) {
						$_formOption = array_merge($_formOption, array('maxlength' => $data[$modelName]['max_length']));
					} else {
						$_formOption = array_merge($_formOption, array('maxlength' => 10));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'select':
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, array('options' => $option));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'radio':
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, array('options' => $option));
					}
					if ($data[$modelName]['separator']) {
						$_formOption = array_merge($_formOption, array('separator' => $data[$modelName]['separator']));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'checkbox':
					if ($data[$modelName]['label_name']) {
						$_formOption = array_merge($_formOption, array('label' => $data[$modelName]['label_name']));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
				
				case 'multiple':
					$_formOption['type'] = 'select';
					if ($data[$modelName]['choices']) {
						$option = $this->textToArray($data[$modelName]['choices']);
						$_formOption = array_merge($_formOption, array('options' => $option, $fieldType => 'checkbox'));
					}
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'pref':
					$_formOption['type'] = 'select';
					$_formOption = array_merge($_formOption, array('options' => $this->BcText->prefList()));
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				case 'wysiwyg':
					if ($data[$modelName]['rows']) {
						$_formOption = array_merge($_formOption, array('height' => $data[$modelName]['rows']));
					} else {
						$_formOption = array_merge($_formOption, array('height' => '200px'));
					}
					if ($data[$modelName]['cols']) {
						$_formOption = array_merge($_formOption, array('width' => $data[$modelName]['cols']));
					} else {
						$_formOption = array_merge($_formOption, array('width' => '100%'));
					}
					$_formOption = array_merge($_formOption, array(
						'editor_tool_type' => $data[$modelName]['editor_tool_type'],
					));
					$formOption = Hash::merge($formOption, $_formOption);
					break;
					
				default:
					$formOption = Hash::merge($formOption, $_formOption);
					break;
			}
		}
		
		return $formOption;
	}
	
/**
 * タイプに応じたフォームの入力形式を出力する
 * 
 * @param string $field
 * @param array $options
 * @return string
 */
	public function input($field, $options = array()) {
		$fieldType = $options['type'];
		$formString = '';
		
		switch ($fieldType) {
			case 'date':
				$options['type'] = 'text';
				$formString = $this->BcForm->datepicker($field, $options);
				break;
			
			case 'datetime':
				$options['type'] = 'text';
				$formString = $this->BcForm->dateTimePicker($field, $options);
				break;
			
			case 'wysiwyg':
				$editorOptions = array(
					'editor' => $this->_View->viewVars['siteConfig']['editor'],
					'editorEnterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br'],
					// 'enterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br'],
					// 'editorEnterBr' => $this->_View->viewVars['siteConfig']['editor_enter_br']
					// 'editorUseDraft' => true,
					// 'editorDraftField' => 'detail_draft',
					'editorWidth' => $options['width'],
					'editorHeight' => $options['height'],
					'editorToolType' => $options['editor_tool_type'],
				);
				$options = array_merge($editorOptions, $options);
				$formString = $this->BcForm->ckeditor($field, $options);
				break;
			
			default:	
				$formString = $this->BcForm->input($field, $options);
				break;
		}
		
		return $formString;
	}
	
/**
 * テキスト情報を配列形式に変換して返す
 * - 改行で分割する
 * - 区切り文字で分割する
 * 
 * @param string $str
 * @return mixed
 */
	public function textToArray($str = '') {
		// "CR + LF: \r\n｜CR: \r｜LF: \n"
		$code = array('\r\n', '\r');
		// 文頭文末の空白を削除する
		$str = trim($str);
		// 改行コードを統一する（改行コードを変換する際はダブルクォーテーションで指定する）
		//$str = str_replace($code, '\n', $str);
		$str = preg_replace('/\r\n|\r|\n/', "\n", $str);
		// 分割（結果は配列に入る）
		$str = preg_split('/[\s,]+/', $str);
		//$str = explode('\n', $str);
		
		// 区切り文字を利用して、キーと値を指定する場合の処理
		$keyValueArray = array();
		foreach ($str as $key => $value) {
			$array = preg_split('/[:]+/', $value);
			if (count($array) > 1) {
				$keyValueArray[$array[1]] = $array[0];
			} else {
				$keyValueArray[$key] = $value;
			}
		}
		if ($keyValueArray) {
			return $keyValueArray;
		}
		
		return $str;
	}
	
/**
 * 各フィールド別の表示判定を行う
 * 
 * @param array $data
 * @param array $options
 * @return boolean
 */
	public function judgeShowFieldConfig($data = array(), $options = array()) {
		$_options = array(
			'field' => '',
		);
		$options = array_merge($_options, $options);
		
		if ($data) {
			if (isset($data['PetitCustomFieldConfigField'])) {
				if ($data['PetitCustomFieldConfigField'][$options['field']]) {
					return true;
				}
			} else {
				$key = key($data);
				if ($data[$key][$options['field']]) {
					return true;
				}
			}
		}
		return false;
	}
	
/**
 * カスタムフィールドが有効になっているか判定する
 * 
 * @param array $data
 * @return boolean
 */	
	public function judgeStatus($data = array()) {
		if ($data) {
			if (isset($data['PetitCustomFieldConfigField'])) {
				if ($data['PetitCustomFieldConfigField']['status']) {
					return true;
				}
			} else {
				$key = key($data);
				if ($data[$key]['status']) {
					return true;
				}
			}
		}
		return false;
	}
	
/**
 * 未使用状態を判定する
 * 
 * @param array $data
 * @param string $modelName
 * @return boolean 未使用状態
 */
	public function allowPublish($data, $modelName = '') {
		if ($modelName) {
			$data = $data[$modelName];
		} else {
			if (isset($data['PetitCustomFieldConfigField'])) {
				$data = $data['PetitCustomFieldConfigField'];
			} elseif (isset($data['PetitCustomFieldConfig'])) {
				$data = $data['PetitCustomFieldConfig'];
			}
		}
		$allowPublish = (int)$data['status'];
		return $allowPublish;
	}
	
/**
 * KeyValu形式のデータを、['Model']['key'] = value に変換する
 * 
 * @param array $data
 * @return array
 */
	public function convertKeyValueToModelData($data = array()) {
		$dataField = array();
		if (isset($data['PetitCustomFieldConfigField'])) {
			$dataField[]['PetitCustomFieldConfigField'] = $data['PetitCustomFieldConfigField'];
		}
		
		$detailArray = array();
		foreach ($dataField as $value) {
			$keyArray = preg_split('/\./', $value['PetitCustomFieldConfigField']['key'], 2);
			$detailArray[$keyArray[0]][$keyArray[1]] = $value['PetitCustomFieldConfigField']['value'];
		}
		return $detailArray;
	}
	
/**
 * プチ・カスタムフィールド一覧を表示する
 *
 * @param array $post
 * @param array $options
 * @return void
 */
	public function showPetitCustomField($post = array(), $options = array()) {
		$_options = array(
			'template' => 'petit_custom_field_block'
		);
		$options = Set::merge($_options, $options);
		extract($options);
		
		$this->BcBaser->element($template, array('plugin' => 'petit_custom_field', 'post' => $post));
	}
	
/**
 * フィールド名を指定して、プチカスタムフィールドのデータを取得する
 * - セレクト、ラジオ、日付 に対応。テキストの場合はそのまま表示する
 * 
 * @param array $post
 * @param string $field
 * @param array $option
 * @return string
 */
	public function getPdcfData($post = array(), $field = '', $option = array()) {
		$data = '';
		$_options = array(
			'invisible' => false,
			'format' => 'Y-m-d',
		);
		$options = Hash::merge($_options, $option);
		if (!$field) {
			return '';
		}
		if (!isset($post['PetitCustomField'])) {
			return '';
		}
		if ($this->judgeStatus($post)) {
			
			switch ($field) {
				case 'type_select':
					// セレクトの場合
					if (!empty($post['PetitCustomField']['type_select'])) {
						$config = Configure::read('petitCustomField.type_select');
						if (!$post['PetitCustomField']['type_select']) {
							if($options['invisible']) {
								// 空文字にして表示なしにする
								$config[$post['BlogContent']['id']]['0'] = '';
							}
						}
						$data = $config[$post['BlogContent']['id']][$post['PetitCustomField']['type_select']];
					}
					break;
					
				case 'type_radio':
					// ラジオの場合
					if (!empty($post['PetitCustomField']['type_radio'])) {
						$config = Configure::read('petitCustomField.type_radio');
						if (!$post['PetitCustomField']['type_radio']) {
							if($options['invisible']) {
								// 空文字にして表示なしにする
								$config[$post['BlogContent']['id']]['0'] = '';
							}
						}
						$data = $config[$post['BlogContent']['id']][$post['PetitCustomField']['type_radio']];
					}
					break;
					
				case 'type_date':
					$data = $this->BcTime->format($options['format'], $post['PetitCustomField'][$field], $invalid = false, $userOffset = null);
					break;
				
				default:
					$data = $post['PetitCustomField'][$field];
					break;
			}
			
		}
		return $data;
	}
	
}
