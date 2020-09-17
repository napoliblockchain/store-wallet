<div class="form">
<?php

// Telegram variables
$settings = Settings::load();
$signup = Yii::app()->createUrl('site/signup'); // sign up new user
$URLRecoveryPassword = Yii::app()->createUrl('site/recoverypassword');
$URLContactForm = Yii::app()->createUrl('site/contactForm');

include ('js_login.php');

//reCaptcha2
$reCaptcha2PublicKey = $settings->reCaptcha2PublicKey;

$this->pageTitle=Yii::app()->name . ' - Login';
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>false,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
));

?>
<div class="login-wrap">
	<div class="login-content">
		<div class="login-logo">
			<?php Logo::login(); ?>
		</div>

		<div class="login-form">
			<!-- BUGFIX x Chrome che riempie in automatico i campi successivi -->
			<input style="display:none">
			<input type="password" style="display:none">
			<?php echo $form->hiddenField($model,'oauth_provider',array('value'=>'email')); ?>

			<!-- end bugfix -->
				<div class="form-group">
					<div class="input-group">
                        <div class="input-group-addon">
							<img style="height:25px;" src="css/images/ic_account_circle.svg">
                        </div>
						<?php echo $form->textField($model,'username',array('placeholder'=>Yii::t('lang','Email address'),'class'=>'form-control','style'=>'height:45px;')); ?>

					</div>
					<?php echo $form->error($model,'username',array('class'=>'alert alert-danger')); ?>
				</div>
				<div class="form-group">
					<div class="input-group">
                        <div class="input-group-addon">
							<img style="height:25px;" src="css/images/ic_vpn_key.svg">
                        </div>
						<?php echo $form->passwordField($model,'password',array('placeholder'=>Yii::t('lang','Password'),'class'=>'form-control','style'=>'height:45px;')); ?>

                    </div>
					<?php echo $form->error($model,'password',array('class'=>'alert alert-danger')); ?>
				</div>

				<div class="form-group">
					<div class="input-group">
						<a href="<?php echo $URLRecoveryPassword; ?>"><?php echo Yii::t('lang','Forget password?'); ?></a>
	                </div>
				</div>

				<div class="form-group">
					<?php
					$form->widget('application.extensions.reCaptcha2.SReCaptcha', array(
	        				'name' => 'reCaptcha', //is requred
	        				'siteKey' => $reCaptcha2PublicKey,
	        				'model' => $form,
							'lang' => 'it-IT',
							// 'widgetOptions' => ['style' => 'width: 100%; !important'],
	        				//'attribute' => 'reCaptcha' //if we use model name equal attribute or customize attribute
						)
					);
					?>
					<?php echo $form->error($model,'reCaptcha',array('class'=>'alert alert-danger')); ?>
				</div>
				<div class="form-group">
					<?php echo $form->error($model,'ga_cod',array('class'=>'alert alert-danger')); ?>
				</div>


				<?php echo CHtml::submitButton(Yii::t('lang','sign in'), array('class' => 'au-btn au-btn--block au-btn--blue m-b-20','id'=>'accedi-button')); ?>

				<div class="form-group">
					<div class="input-group">
						<a href="<?php echo $URLContactForm; ?>" target="_blank">
							 <?php echo Yii::t('lang','Did you discover a bug? Please compile this form.');?></a>
					</div>
				</div>

				<div class="form-group">
					<div class="input-group">
						<span><a href="https://www.iubenda.com/privacy-policy/7935688"><?php echo Yii::t('lang','Read our Privacy policy'); ?></a></span>
					</div>
				</div>

				<div class="row">
					<div class="col" style="text-align:center;">
						<img class='login-sponsor' src="<?php echo Yii::app()->request->baseUrl; ?>/css/images/logocomune.png" alt="" >
					</div>
					<div class="col" style="text-align:center;">
						<img class='login-sponsor' width="150" height="150" src="<?php echo Yii::app()->request->baseUrl; ?>/css/images/parthenope.png" alt="" sizes="(max-width: 150px) 100vw, 150px">
					</div>
				</div>
				<?php echo Logo::footer('#333'); ?>
		</div>

	</div>
</div>


<!-- RICHIESTA 2FA -->
<div class="modal fade" id="2faModal" tabindex="-1" role="dialog" aria-labelledby="2faModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content alert-dark text-light">
			<div class="modal-header">
				<h3 class="modal-title" id="2faModalLabel"><?php echo Yii::t('lang','Two factors authentication'); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="input-group">
                        <div class="input-group-addon">
                            <img style="height:25px;" src="css/images/ic_account_google2fa.png">
                        </div>
						<?php echo $form->numberField($model,'ga_cod',array('placeholder'=>Yii::t('lang','Google 2FA'),'class'=>'form-control','style'=>'height:45px;')); ?>
                    </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Yii::t('lang','cancel'); ?></button>
				<button type="button" class="btn btn-primary" id='Conferma2fa' style="min-width:90px;"><?php echo Yii::t('lang','confirm'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php $this->endWidget(); ?>
</div><!-- form -->
