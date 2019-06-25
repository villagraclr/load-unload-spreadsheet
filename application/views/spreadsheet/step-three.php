<div id="step-two">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div id='load_data_on_tables'>
					<load-data-on-tables></load-data-on-tables>
				</div>
			</div>
		</div>
	</div>
</div>
<template id="load-data-template-on-tables">
	<div class="card">
		<div class="card-header">
			<h2>Step 3: Execute load data on tables</h2>
		</div>
		<div class="card-body">
			<div v-if="column_sheet_to_associate && column_sheet_to_associate.length > 0">
				<div class="form-group">
					<label for="email">Sheet:</label>
					<select v-model="selected_sheet" @change="selectColumTableAndSheetAvailable()" required>
						<option value='' selected>Seleccione</option>
						<option v-for="item in sheet_availables" v-bind:value="{ id: item.key, value: item.value }"	selected>
							 {{ item.value }}
						   </option>
					   </select>
				</div>
				<div class="form-group">
					<label for="email">Sheet Column:</label>
					<select v-model="selected_column_sheet" required>
						<option v-for="item in sheet_columns_availables" :value="item.key">{{ item.value }}</option>
					</select>
				</div>
				<div class="form-group">
					<label for="email">Table Column:</label>
					<select v-model="selected_column_table" required>
						<option v-for="item in table_columns_availables" :value="item.key">{{item.key}}</option>
					</select>
				</div>
				<div class="form-group">
					<button class="btn btn-success" @click.stop.prevent="sendForm()">Associate</button>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-striped">
				<tr>
					<th>Sheet Name</th>
					<th>Table</th>
					<th>Sheet column</th>
					<th>Table column</th>
					<th></th>
				</tr>
				<tr v-for="(item, index) in associate_columns" :key="index" >
					<td>{{ item.sheet }}</td>
					<td>{{ item.table }}</td>
					<td>{{ item.key }}</td>
					<td>{{ item.translate_value }}</td>
					<td>
						<button class="btn btn-danger a-btn-slide-text" v-on:click="removeItem(item, index)">
							<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
							<span><strong>Delete</strong></span>
						</button>
					</td>
				</tr>
				</table>
			</div>
			<div v-if="associate_columns && associate_columns.length > 0">
				<div class="form-group">
					<?php
					$attributes = array(
							'class' => 'btn btn-primary'
					);
					echo anchor('upload/process', 'Load File in Database', $attributes);
					?>
				</div>
			</div>
		</div>	  
	</div>
</template>
