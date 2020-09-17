<div class="form">

<?php
$idUserCrypted = crypt::Encrypt($user->id_user);
$scadenzaPin = [0=>Yii::t('lang','Never'),5=>'5 min',10=>'10 min',15=>'15 min',30=>'30 min',60=>'60 min'];
$language = [''=>'','it'=>Yii::t('lang','Italian'),'en'=>Yii::t('lang','English')];
$google2faURL = Yii::app()->createUrl('users/2fa').'&id='.$idUserCrypted;
$google2faRemoveURL = Yii::app()->createUrl('users/2faRemove').'&id='.$idUserCrypted;

include ('js_settings.php');
include ('js_resetpwd.php');
include ('js_masterseed.php');

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'settings-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
));
?>

<div class='section__content section__content--p30'>
	<div class='container-fluid'>
		<div class="row">
			<div class="col-lg-12">
		        <div class="au-card au-card--no-shadow au-card--no-pad bg-overlay--semitransparent">
					<div class="card-header ">
							<i class="fa fa-gear"></i>
						<span class="card-title"><?php echo Yii::t('lang','Settings');?></span>
						<div class="show-rescan text-success">
							<div class="sync-blockchain float-right"></div>
							<div class="sync-difference"></div>
						</div>
					</div>
		            <div class="card-body">
						<div class="table-responsive table--no-card m-b-40">
							<div class="table table-borderless table-data4 " id="settings-grid">
								<div class="card bg-transparent">
									<div class="card-header bg-secondary">
										<strong class="card-title mb-3"><?php echo Yii::t('lang','Security');?></strong>
									</div>
									<div class="card-body">
										<table class="items " style="width:100%;">
											<tbody>

												<tr class="odd">
													<td><p class="card-title"><?php echo Yii::t('lang','PIN setting');?></p></td>
													<td><button type="button" id='pinRemoveButtonModal' class="btn btn-primary btn-sm float-right" data-toggle="modal" style="display:none;"><?php echo Yii::t('lang','Remove');?></button><?php echo $form->dropDownList($userForm,'scadenzaPin',$scadenzaPin,array('class'=>'float-right'));?></td>
												</tr>

												<tr class="even">
													<td><p class="card-title"><?php echo Yii::t('lang','Two factors authentication');?></p></td>
													<td style="text-align:right;">

													<?php if ($user->ga_secret_key == ''){ ?>
															<a href="<?php echo $google2faURL;?>">
																<button type="button" class="btn btn-primary btn-sm float-right"><?php echo Yii::t('lang','Enable');?></button>
															</a>
														<?php }else{ ?>
															<a href="<?php echo $google2faRemoveURL;?>">
																<button type="button" class="btn btn-danger btn-sm float-right"><?php echo Yii::t('lang','Disable');?></button>
															</a>
														<?php } ?>
													</td>
												</tr>
												<?php if ($social->oauth_provider == 'email' ){ ?>
												<tr class="odd">
													<td><p class="card-title"><?php echo Yii::t('lang','Reset password');?></p></td>
													<td><button type="button" class="btn btn-danger pwdreset-button float-right" data-toggle="modal" data-target="#resetpwdModal"><?php echo Yii::t('lang','Reset');?></button><button class="btn btn-warning responsepwd__button float-right" style="display:none;"><span class="responsepwd__text"></span></button></td>
												</tr>
												<?php } ?>
												<tr class="odd">
													<td><p class="card-title"><?php echo Yii::t('lang','Backup Master Seed');?></p></td>
													<td>
														<button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#masterSeedModal">
																<?php echo Yii::t('lang','save');?>
														</button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
			                    </div>
								<div class="card bg-transparent">
									<div class="card-header bg-primary">
										<strong class="card-title mb-3"><?php echo Yii::t('lang','Preferences');?></strong>
									</div>
									<div class="card-body">
										<table class="items " style="width:100%;">
											<tbody>
												<tr class="even">
													<td><p class="card-title"><?php echo Yii::t('lang','Save application on Homepage');?></p></td>
													<td style="text-align:right;">
														<a href="#" onclick="saveOnDesktop();"><button type="button" class="btn btn-primary btn-sm float-right saveOnDesktop"><?php echo Yii::t('lang','save');?></button></a></td>
												</tr>
												<tr class="odd">
													<td><p class="card-title"><?php echo Yii::t('lang','Scan the blockchain');?></p></td>
													<td><button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#rescanModal"><?php echo Yii::t('lang','rescan');?></button></td>
												</tr>

												<tr class="even">
													<td><p class="card-title"><?php echo Yii::t('lang','PUSH notifications');?></p></td>
													<td><button disabled type='button' class="js-push-btn-modal btn btn-sm btn-primary float-right"data-toggle="modal" data-target="#pushEnableModal"><?php echo Yii::t('lang','enable');?></button></td>
												</tr>
												<tr class="odd">
													<td><p class="card-title"><?php echo Yii::t('lang','Select language');?></p></td>
													<td><?php echo $form->dropDownList($userForm,'language',$language,array('class'=>'float-right'));?></td>
												</tr>
											</tbody>
										</table>
									</div>
			                    </div>
							</div>
						</div>
		            </div>
		        </div>
			</div>
		</div>
		<?php echo Logo::footer(); ?>
    </div>
</div>

<!-- <!-- show Master Seed -->
<div class="modal fade" id="masterSeedModal" tabindex="-1" role="dialog" aria-labelledby="masterSeedModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content bg-secondary ">
			<div class="modal-header">
				<h5 class="modal-title" id="masterSeedModalLabel"><?php echo Yii::t('lang','Backup Master Seed');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="masterSeedMessagePinEnabled" style="display:none;">
	                <p><?php echo Yii::t('lang','WARNING - It is dangerous to show your Master Seed. Continue only if it is necessary because you have lost your previous backup.');?></p>
					<p>
						<?php echo Yii::t('lang','Are you sure to continue?');?>
					</p>
				</div>
				<div class="masterSeedMessagePinDisabled" style="display:none;">
	           <p><?php echo Yii::t('lang','WARNING - You can backup your seed only if secure PIN is enabled.');?></p>
				</div>
			</div>
			<div class="modal-footer">
				<div class="masterSeedMessagePinEnabled" style="display:none;">
					<div class="form-group">
						<button type="button" class="btn btn-secondary " data-dismiss="modal" style="min-width: 100px; padding:2.5px 10px 2.5px 10px; height:30px;">
							<i class="fa fa-backward"></i> <?php echo Yii::t('lang','back');?>
						</button>
						<button type="button" class="btn btn-primary " data-dismiss="modal" id="showMasterSeed" data-toggle="modal" data-target="#showMasterSeedModal" style="min-width: 100px; padding:2.5px 10px 2.5px 10px; height:30px;">
							<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('lang','confirm');?>
						</button>
					</div>
				</div>
				<div class="masterSeedMessagePinDisabled" style="display:none;">
					<div class="form-group">
						<button type="button" class="btn btn-secondary " data-dismiss="modal" style="min-width: 100px; padding:2.5px 10px 2.5px 10px; height:30px;">
							<i class="fa fa-reply"></i> <?php echo Yii::t('lang','close');?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- show Master Seed -->
<div class="modal fade" id="showMasterSeedModal" tabindex="-1" role="dialog" aria-labelledby="showMasterSeedModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content bg-secondary ">
			<div class="modal-header">
				<h5 class="modal-title" id="showMasterSeedModalLabel"><?php echo Yii::t('lang','Master Seed');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<p id='masterSeed' class="alert alert-light"></p>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<button type="button" class="btn btn-secondary " data-dismiss="modal" style="min-width: 100px; padding:2.5px 10px 2.5px 10px; height:30px;">
						<i class="fa fa-reply"></i> <?php echo Yii::t('lang','close');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- RESET PASSWORD -->
<div class="modal fade" id="resetpwdModal" tabindex="-1" role="dialog" aria-labelledby="resetpwdModalLabel" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert-light ">
			<div class="modal-header">
				<h3 class="modal-title" id="resetpwdModalLabel"><?php echo Yii::t('lang','Reset password');?></h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<p>
					<?php echo Yii::t('lang','This operation will send a link to your inbox.');?><br>
					<?php echo Yii::t('lang','Do you want to continue?');?>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Yii::t('lang','cancel');?></button>
				<button type="button"  class="btn btn-primary" data-dismiss="modal" id="resetpwd-button"><?php echo Yii::t('lang','confirm');?></button>
			</div>
		</div>
	</div>
</div>

<!-- operazione di RESCAN -->
<div class="modal fade" id="rescanModal" tabindex="-1" role="dialog" aria-labelledby="rescanModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content bg-secondary ">
			<div class="modal-header">
				<h5 class="modal-title" id="rescanModalLabel"><?php echo Yii::t('lang','Rescan');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
                <p><?php echo Yii::t('lang','This operation tries to restore the transactions history. Scanning may consume a lot of memory and slow down normal phone activity.');?></p>
				<p>
					<?php echo Yii::t('lang','Are you sure to continue?');?>
				</p>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo Yii::t('lang','cancel');?></button>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-primary" id="rescan" data-dismiss="modal"><?php echo Yii::t('lang','Confirm');?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<!--  nuovo PIN -->
<div class="modal fade " id="pinNewModal" tabindex="-1" role="dialog" aria-labelledby="pinNewModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content bg-secondary ">
			<div class="modal-header">
				<h5 class="modal-title" id="pinNewModalLabel"><?php echo Yii::t('lang','New PIN');?></h5>
			</div>
			<div class="modal-body ">
            <div class="pin-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="dropNumpad(true);"><?php echo Yii::t('lang','cancel');?></button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinNewButton" ><?php echo Yii::t('lang','Confirm');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- VERIFICA  PIN -->
<div class="modal fade " id="pinVerifyModal" tabindex="-1" role="dialog" aria-labelledby="pinVerifyModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content alert-light  ">
			<div class="modal-header">
				<h5 class="modal-title" id="pinVerifyModalLabel"><?php echo Yii::t('lang','PIN Verify');?></h5>
			</div>
			<div class="modal-body ">
                <div class="pin-confirm-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="dropNumpad(true);"><?php echo Yii::t('lang','cancel');?></button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinVerifyButton" ><?php echo Yii::t('lang','confirm');?></button>
				</div>
			</div>
		</div>
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
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinRequestButton"><?php echo Yii::t('lang','Confirm');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- RIMUOVI PIN -->
<div class="modal fade " id="pinRemoveModal" tabindex="-1" role="dialog" aria-labelledby="pinRemoveModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content alert-light  ">
			<div class="modal-header">
				<h5 class="modal-title" id="pinRemoveModalLabel"><?php echo Yii::t('lang','Remove PIN');?></h5>
			</div>
			<div class="modal-body ">
                <div class="pin-remove-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="dropNumpad(false);" id="pinCancelButton"><?php echo Yii::t('lang','cancel');?></button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinRemoveButton" ><?php echo Yii::t('lang','Confirm');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ABILITA PUSH -->
<div class="modal fade " id="pushEnableModal" tabindex="-1" role="dialog" aria-labelledby="pushEnableModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert-light  ">
			<div class="modal-header">
				<h5 class="modal-title" id="pushEnableModalLabel"><?php echo Yii::t('lang','Push Notifications');?></h5>
			</div>
			<div class="modal-body ">
                <p><b><?php echo Yii::t('lang','Enabling');?>:</b>
                    <br><?php echo Yii::t('lang','By enabling this setting you will receive <b> push </b> notifications when there are new transactions on your wallet. ');?>
					<br> <br><i><?php echo Yii::t('lang','Notifications are enabled for each device. To receive notifications on other devices you need to log in from each one, enable it and make sure you are online.');?>
						</i>
                </p>
                <p>
					<?php echo Yii::t('lang','Be sure to reply <b>Allow </b>when prompted');?>
                </p>
                <p><b><?php echo Yii::t('lang','Disabling');?>:</b>
					<br><?php echo Yii::t('lang','By disabling <b> push </b> notifications, you will no longer receive messages when there are transactions on your wallet.');?> </b> <br> <br>
					<i> <?php echo Yii::t('lang','Disabling push notifications from this device may also eliminate the subscription of any other connected devices.');?> </i>
                </p>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary" data-dismiss="modal" ><?php echo Yii::t('lang','cancel');?></button>
				</div>
				<div class="form-group">
					<button type="button" class="js-push-btn btn btn-primary" data-dismiss="modal"><?php echo Yii::t('lang','Confirm');?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
