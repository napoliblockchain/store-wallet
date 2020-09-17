<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
)); ?>
<?php echo $form->errorSummary($model, '', '', array('class' => 'alert alert-danger')); ?>
	<div class="col-md-6">
	    <div class="card border border-primary bg-teal">
			<div class="card-footer">
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon"><?php echo Yii::t('lang','Verification code');?></div>
						<?php echo $form->textField($model,'ga_cod',array('class'=>'form-control')); ?>
					</div>
					<?php echo $form->error($model,'ga_cod',array('class'=>'alert alert-danger')); ?>
				</div>
			</div>
	    </div>
	</div>


	<div class="form-group">
		<?php echo CHtml::submitButton(Yii::t('lang','Confirm'), array('class' => 'btn btn-primary')); ?>
	</div>


<?php $this->endWidget(); ?>

</div><!-- form -->
