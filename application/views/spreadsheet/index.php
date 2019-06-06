<div id="app">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div id='upload_file_db'>
					<upload-file-db></upload-file-db>
				</div>
			</div>
		</div>
	</div>
</div>
<template id="template-upload-form" v-once>
	<div class="card">
	  <div class="card-header">
		<h2>Step 1: Upload Form</h2>
	  </div>
	  <div class="card-body">
		<div v-if="showUploadForm">
			<h5 class="card-title">Excel</h5>
			<?php if(validation_errors()) { ?>
			<div class="alert alert-danger">
			<?php echo validation_errors(); ?>
			</div>
			<?php } ?>
			
			<?php
			$attributes = array('accept-charset' => 'utf-8', 'id' => 'upload-form', 'v-on:submit.prevent' => 'upload');

			echo form_open_multipart('upload/do_upload', $attributes); 
			?>
			<progress max="100" :value.prop="uploadPercentage"></progress>
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
			<p id="error" class="text-danger pt-3"></p>
		</div>
		<div v-if="!showUploadForm">
			<?php
			$attributes = array(
					'class' => 'btn btn-primary'
			);
			echo anchor('upload/step2', 'Step 2', $attributes);
			?>
			<div class="divider">
			</div>
			<div v-if="show_preview" class="mt-3">  
			  <b-button variant="primary" disabled>
				<b-spinner small type="grow"></b-spinner>
				Loading preview...
			  </b-button>
			</div>
			<div v-else class="mt-3">
				<b-card no-body v-for="(rowcolumn, name, index) in preview_data" class="mt-3">
					<b-row>
						<b-col md="12">
							<b-card-body>
								<b-card-text>
									<b-button v-b-toggle="'collapse' + index" variant="primary">Sheet {{name}}</b-button>
									  <b-collapse :id="'collapse' + index" visible>
										<b-card>
											<div class="table-responsive">
												<table class="table table-striped">
												<thead>
													<tr v-for="(rowth, key) in rowcolumn" v-if="key < 1">
														<th v-for="(colth, key2) in rowth">{{colth}}  </td>
													</tr>
												</thead>
												<tbody>
													<tr v-for="(rowtd, key3) in rowcolumn" v-if="key3 >= 1">
														<td v-for="(coltd, key4) in rowtd">{{coltd}}</td>
													</tr>
												</tbody>
												</table>
											</div>
										</b-card>
									  </b-collapse>
								</b-card-text>
							</b-card-body>
						</b-col>
					</b-row>
				</b-card>
			</div>
		</div>
	  </div>
	</div>
</template>
