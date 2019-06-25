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
		<div class="table-responsive separate">
			<table class="table table-striped">
			<tr>
				<th>Sheet Name</th>
				<th>Table</th>
				<th>Records</th>
				<th>Processed</th>
				<th>Status</th>
			</tr>
			<tr v-for="(item, index) in summary" >
				<td>{{ item.sheet }}</td>
				<td>{{ item.table }}</td>
				<td>{{ item.records }}</td>
				<td>{{ item.processed_records }}</td>
				<td>{{ item.status }}</td>
			</tr>
			<tr v-if="show_preview">
				<td colspan="5">
					<b-spinner small type="grow" ></b-spinner>
					Por favor espere, este proceso puede tardar varios minutos...
				</td>
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
