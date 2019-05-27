
	<div id="app">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2>Form Load Excel Files</h2>
					<?php if(validation_errors()) { ?>
					<div class="alert alert-danger">
						<?php echo validation_errors(); ?>
					</div>
					<?php } ?>
					<?php echo form_open_multipart('upload/load'); ?>
						<div class="form-group">
						<?php
							$data = array(
									'name'          => 'file',
									'id'            => 'file'
							);
							echo form_upload($data);

						?>
						</div>
					<?php echo form_close(); ?>
				</div>  
			</div>
		</div>
	</div>

