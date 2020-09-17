<?php
//richiamo tutte le funzioni javascript
include ('js_pin.php');

$modifyURL = Yii::app()->createUrl('users/update').'&id='.crypt::Encrypt($model->id_user);
?>
<div class='section__content section__content--p30'>
<div class='container-fluid'>

	<div class="row">
		<div class="col-lg-6">
			<div class="au-card au-card--no-shadow au-card--no-pad bg-overlay--semitransparent">
				<div class="card-header ">
					<i class="fa fa-user"></i>
					<span class="card-title "><?php echo Yii::t('lang','Profile');?></span>
				</div>
				<div class="card-body ">
					<div class="table-responsive table--no-card m-b-40">
						<?php $this->widget('zii.widgets.CDetailView', array(
							'htmlOptions' => array('class' => 'table table-borderless  table-earning '),
							'data'=>$model,
							'attributes'=>array(
								[
									'label'=>Yii::t('model','email'),
									'value'=>$model->email,
									//'visible'=>($social->oauth_provider <> '' ? false : true)

									],
								[
									'label'=>Yii::t('model','First name'),
									'value'=>$model->name,
								],
								[
									'label'=>Yii::t('model','Last name'),
									'value'=>$model->surname,
								],
								// [
								// 	'label'=>Yii::t('model','Username'),
								// 	'value'=>$social->username,
								// ],
								// [
								// 	'label'=>'Telegram ID',
								// 	'value'=>$model->telegram_id,
								// 	'visible'=>($model->telegram_id == 0 ? false : true)
								//
								// ],

							),
						));
						?>
					</div>
					<p>
						<?php echo Yii::t('lang','If you wish to change your profile, please do it by Napay App.'); ?>
					</p>

				</div>


			</div>
		</div>
	</div>
	<?php echo Logo::footer(); ?>
</div>
</div>





<!-- RICHIESTA PIN -->
<div class="modal fade " id="pinRequestModal" tabindex="-1" role="dialog" aria-labelledby="pinRequestModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content alert-light  ">
			<div class="modal-header">
				<h5 class="modal-title" id="pinRequestModalLabel"><?php echo Yii::t('lang','PIN Request');?></h5>
			</div>
			<div class="modal-body ">
				<center>
					<input type='hidden' id='pin_password' class='form-control' readonly="readonly"/>
                    <input type='hidden' id='pin_password_confirm' class='form-control' readonly="readonly"/>
                </center>
                <div class="pin-confirm-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinRequestButton"><?php echo Yii::t('lang','Confirm');?>'</button>
				</div>
			</div>
		</div>
	</div>
</div>
