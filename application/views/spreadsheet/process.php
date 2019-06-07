<div id="step-two">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div id='load_summary'>
					<load-summary></load-summary>
				</div>
			</div>
		</div>
	</div>
</div>
<template id="load-summary-template">
	<div class="card">
		<div class="card-header">
			<h2>Summary report</h2>
		</div>
		<div v-if="show_preview" class="mt-3">  
		  <b-button variant="primary" disabled>
			<b-spinner small type="grow"></b-spinner>
			Proccesing data...
		  </b-button>
		</div>
		<div class="table-responsive separate">
			<table class="table table-striped" v-if="summary && summary.length > 0">
			<tr>
				<th>Sheet Name</th>
				<th>Table</th>
				<th>Records</th>
				<th>Processed</th>
			</tr>
			<tr v-for="(item, index) in summary" >
				<td>{{ item.sheet }}</td>
				<td>{{ item.table }}</td>
				<td>{{ item.records }}</td>
				<td>{{ item.processed_records }}</td>
			</tr>
			</table>
		</div>
		<div class="card-body">
			
			<div>
				<div class="form-group">
					<?php
					$attributes = array(
							'class' => 'btn btn-primary'
					);
					echo anchor('/', 'Salir', $attributes);
					?>
				</div>
			</div>
		</div>	  
	</div>
</template>
