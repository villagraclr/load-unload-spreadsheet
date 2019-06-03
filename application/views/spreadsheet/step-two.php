<div id="step-two">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div id='add_match_sheet_table'>
					<add-match-sheet-table></add-match-sheet-table>
				</div>
			</div>
		</div>
	</div>
</div>
<template id="add-match-template-sheet-table">
	<div class="card">
	  <div class="card-header">
		<h2>Step 2: Associate sheets and tables</h2>
	  </div>
	  <div class="card-body">
		<div v-if="sheets && sheets.length > 0">
			<div class="form-group">
				<label for="email">Sheet:</label>
				<select v-model="selected_sheet" @change="selectTableAvailable($event)" required>
					<option selected>Seleccione</option>
					<option v-for="sheet in sheets" :value="sheet">{{sheet}}</option>
				</select>
			</div>
			<div class="form-group">
				<label for="email">Table:</label>
				<select v-model="selected_table_available" required>
					<option selected>Seleccione</option>
					<option v-for="table in tables_available" :value="table">{{ table }}</option>
				</select>
			</div>
			<div class="form-group">
				<button class="btn btn-success" @click.stop.prevent="sendForm()">Associate</button>
			</div>
		</div>	
		<div class="table-responsive">
			<table class="table">
			<tr>
				<th>Sheet Name</th>
				<th>Table</th>
				<th></th>
			</tr>
			<tr v-for="(item, index) in todos" :key="index" >
				<td>{{ item.sheet }}</td>
				<td>{{ item.tmp_table }}</td>
				<td>
					<button class="btn btn-danger a-btn-slide-text" v-on:click="removeItem(item, index)">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
						<span><strong>Delete</strong></span>
					</button>
				</td>
			</tr>
			</table>
		</div>
		<div v-if="todos && todos.length > 0">
			<?php
			$attributes = array(
					'class' => 'btn btn-primary'
			);
			echo anchor('upload/step3', 'Step 3', $attributes);
			?>
		</div>
	  </div>	  
	</div>
</template>
