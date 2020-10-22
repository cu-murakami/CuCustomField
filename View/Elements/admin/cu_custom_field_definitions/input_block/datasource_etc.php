<?php
/**
 * CuCustomField : baserCMS Custom Field
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCustomField.View
 * @license          MIT LICENSE
 */

/**
 * @var BcAppView $this
 * @var string $currentModelName
 */
?>


<tr id="Row<?php echo $currentModelName . Inflector::camelize('datasource'); ?>Group">
	<th class="col-head bca-form-table__label">
		その他の設定
	</th>
	<td class="col-input bca-form-table__input">
		<span id="Row<?php echo $currentModelName . Inflector::camelize('option_meta_table'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.datasource.table', 'テーブル名') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.datasource.table', ['type' => 'text', 'size' => 10, 'placeholder' => 'blog_posts']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.datasource.table') ?>
		</span>
		<span id="Row<?php echo $currentModelName . Inflector::camelize('option_meta_datasource_title'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.datasource.title', 'リストに表示するフィールド') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.datasource.title', ['type' => 'text', 'size' => 10, 'placeholder' => 'name']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.datasource.title') ?>
		</span>
		<span id="Row<?php echo $currentModelName . Inflector::camelize('option_meta_datasource_entity_id'); ?>">
			<?php echo $this->BcForm->label('CuCustomFieldDefinition.option_meta.datasource.entity_id', 'ブログコンテンツID') ?>
			<?php echo $this->BcForm->input('CuCustomFieldDefinition.option_meta.datasource.entity_id', ['type' => 'text', 'size' => 10, 'placeholder' => 'name']) ?>
			<?php echo $this->BcForm->error('CuCustomFieldDefinition.option_meta.datasource.entity_id') ?>
		</span>
	</td>
</tr>
