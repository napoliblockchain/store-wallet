<?php
//CARICO PROVINCE E COMUNI
$tipoUtenza = CHtml::listData(UsersType::model()->findAll(), 'id_users_type', 'desc');


$disabled = 'disabled';
if (($model->isNewRecord) || Yii::app()->user->objUser['privilegi'] == 20)
	$disabled = '';

?>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
)); ?>
<?php //echo $form->errorSummary($model, '', '', array('class' => 'alert alert-danger')); ?>
		<div class="form-group">
			<?php echo $form->labelEx($model,'email'); ?>
			<?php echo $form->textField($model,'email',array('size'=>100,'readonly'=>!$model->isNewRecord,'class'=>'form-control')); ?>
			<?php echo $form->error($model,'email',array('class'=>'alert alert-danger')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>250,'class'=>'form-control')); ?>
			<?php echo $form->error($model,'name',array('class'=>'alert alert-danger')); ?>
		</div>

		<div class="form-group">
			<?php echo $form->labelEx($model,'surname'); ?>
			<?php echo $form->textField($model,'surname',array('size'=>60,'maxlength'=>250,'class'=>'form-control')); ?>
			<?php echo $form->error($model,'surname',array('class'=>'alert alert-danger')); ?>
		</div>

		<?php if ($model->isNewRecord){ ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'id_users_type'); ?>
				<?php echo $form->dropDownList($model,'id_users_type',$tipoUtenza,array('class'=>'form-control'));	?>
			</div>

			<div class="form-group">
				<?php $model->password = Utils::passwordGenerator(); ?>
				<?php echo $form->labelEx($model,'password'); ?>
				<?php echo $form->textField($model,'password',array('size'=>60,'maxlength'=>250,'placeholder'=>'Password','class'=>'form-control')); ?>
				<?php echo $form->error($model,'password',array('class'=>'alert alert-light')); ?>
			</div>


			<?php //echo $form->hiddenField($model,'id_users_type',array('value'=>3)); ?>
			<?php echo $form->hiddenField($model,'activation_code',array('value'=>md5($model->password))); ?>
			<?php echo $form->hiddenField($model,'status_activation_code',array('value'=>0)); ?>

			<div class="form-group">
				<p><i>Salvare la password nel caso in cui non verrà inviata la mail.</i></p>

				<?php echo $form->labelEx($model,'Invia email'); ?>
				<?php echo $form->checkBox($model,'send_mail'); ?>
			</div>
		<?php }else{ ?>

			<?php echo $form->hiddenField($model,'password'); ?>
			<?php echo $form->hiddenField($model,'activation_code'); ?>
			<?php echo $form->hiddenField($model,'status_activation_code'); ?>
		<?php } ?>




	<div class="form-group">
		<?php echo CHtml::submitButton(($model->isNewRecord ? 'Inserisci' : 'Salva'), array('class' => 'btn btn-primary')); ?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->
