
<div id="app">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="card">
					  <div class="card-header">
						Upload Form
					  </div>
					  <div class="card-body">
						<h5 class="card-title">Excel</h5>
						<?php if(validation_errors()) { ?>
						<div class="alert alert-danger">
						<?php echo validation_errors(); ?>
						</div>
						<?php } ?>
						<?php echo form_open_multipart('upload/do_upload'); ?>
						<div class="form-group">
							<?php
							$data = array(
							'name'          => 'userfile',
							'id'            => 'userfile',
							'accept'	=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel'
							);
							echo form_upload($data);

							?>
						</div>
						<div class="form-group">
							 <?php
								$attributes = array(
										'class' => 'btn btn-primary'
								);
								echo form_submit('send', 'Import', $attributes);
							?>
						</div>
						
					  </div>
					</div>
				</div>
			</div>
		</div>
	</div>
