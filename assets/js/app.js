
const APPURL = 'http://localhost/cargador/index.php/'

Vue.component('upload-file-db', {
	template: '#template-upload-form',
	created: function() {
		var vm = this;
	},
	data: function() {
		return {
			showUploadForm: true,
			file_data: '',
			form_data: {},
			uploadPercentage: 0,
			count_sheet: 0,
			preview_data : [],
			show_preview: false
		}
	},
	methods: {
		upload: function() {
			this.file_data = $('#userfile').prop('files')[0];
			let url = $('form#upload-form').attr('action');
			
			this.form_data = new FormData();
			this.form_data.append('userfile', this.file_data);
			var self = this
			axios.post(url,
				 this.form_data,
				{
					headers: {
						'Content-Type': 'multipart/form-data'
					},
					onUploadProgress: function( progressEvent ) {
						this.uploadPercentage = parseInt( Math.round( ( progressEvent.loaded * 100 ) / progressEvent.total ) );
					}.bind(this)
				}
			)
			.then((res) => {
				if (res.data.success) {
					app.message = res.data.success;
					this.showUploadForm = false;
					this.loadPreviewData();
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}
			})
			.catch((error) =>{
			  console.log(error);
			});
		},
		loadPreviewData: function(){
			this.show_preview = true;
			var url_preview_data = APPURL+'upload/load-preview-data';
            console.log(url_preview_data);
			axios.get(url_preview_data)
			.then((res) => {
				if (res.data.success) {
					this.preview_data = res.data.top_elements;
					this.show_preview = false;
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}
				
			})
			.catch((error) =>{
			  console.log(error);
			});
		}
	}
});
// var eventBus = new Vue();
Vue.prototype.$eventHub = new Vue();

var app = new Vue({
  	el: '#app',
  	data: {
  		title: '',
		showUploadForm: true,
		message: ''
  	}
});



Vue.component('add-match-sheet-table', {
  template: '#add-match-template-sheet-table',
  data: function() {
    return {
	  sheets: [],
	  tables_available: [],
      selected_sheet: null,
	  selected_table_available: null,
	  form_data: {},
	  todos: [
	  ]
    }
  },
  created () {
	var url = APPURL+'upload/get-worksheets';
	axios.get(url)
	.then((res) => {
		if (res.data.success) {
			this.sheets = res.data.sheets;
		}
		if (res.data.error) {
			$('#error').html(res.data.error)
		}
		
	})
	.catch((error) =>{
	  console.log(error);
	});
  },
	mounted: function(){
		this.$nextTick(this.loadTodo)
	},
  methods: {
	selectTableAvailable: function(event){
		this.selected_sheet = event.target.value;
		
		this.form_data = new FormData();
		this.form_data.append('selected_sheet', this.selected_sheet);
		
		var url_tables_availables = APPURL+'upload/get-tables-available';
		
		axios
		.post(url_tables_availables, this.form_data)
		.then((res) => {
			if (res.data.success) {
				this.tables_available = res.data.tables_available;
			}
			if (res.data.error) {
				$('#error').html(res.data.error)
			}
		})
		.catch((error) =>{
		  console.log(error);
		});
		
	},
	loadTodo: function(){
		var url_asociate = APPURL+'upload/get-associate-sheet-table';
		axios.get(url_asociate)
		.then((res) => {
			if (res.data.success) {
				this.todos = res.data.associate_sheet_table;
			}
			if (res.data.error) {
				$('#error').html(res.data.error)
			}
			
		})
		.catch((error) =>{
		  console.log(error);
		});
	},
	sendForm: function(){
		if (this.selected_sheet && this.selected_table_available) {
			this.form_data = new FormData();
			this.form_data.append('selected_sheet', this.selected_sheet);
			this.form_data.append('selected_table_available', this.selected_table_available);
			
			var url_associate_sheet_table = APPURL+'upload/set-associate-sheet-table';
			
			axios
			.post(url_associate_sheet_table, this.form_data)
			.then((res) => {
				if (res.data.success) {
					this.sheets = res.data.sheets;
					this.tables_available = [];
					this.loadTodo();
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}
			})
			.catch((error) =>{
			  console.log(error);
			});
			
		}
		
	},
	removeItem: function(todo, index) {
       this.todos.splice(index, 1);
		this.form_data = new FormData();
		this.form_data.append('selected_sheet', todo.sheet);
		this.form_data.append('selected_table_available', todo.tmp_table);
		
		var url_reverse_associate_sheet_table = APPURL+'upload/reverse-associate-sheet-table';
		
		axios
		.post(url_reverse_associate_sheet_table, this.form_data)
		.then((res) => {
			if (res.data.success) {
				this.sheets = res.data.sheets;
				this.tables_available = [];
				this.loadTodo();
			}
			if (res.data.error) {
				$('#error').html(res.data.error)
			}
		})
		.catch((error) =>{
		  console.log(error);
		});
	}
  }
  
})

var match = new Vue({
  el: "#add_match_sheet_table"
})

Vue.component('load-data-on-tables', {
	template: '#load-data-template-on-tables',
	data: function() {
		return {
			selected_sheet: '',
			selected_column_table: '',
			selected_column_sheet: '',
			form_data: {},
			associate_columns: [
			],
			column_table_to_associate: [
			],
			column_sheet_to_associate: [
			],
			sheet_availables: [
			],
			table_columns_availables: [
			],
			sheet_columns_availables: [
			],
			sheet_table_asigned: [
			],
			all_sheet_columns: [
			]
		}
	},
	created () {
		this.$nextTick(this.loadTodo);
	},
	methods: {
		loadTodo: function(){
			var url = APPURL+'upload/get-associate-columns';
			axios.get(url)
			.then((res) => {
				if (res.data.success) {
					this.selected_sheet = [];
					this.sheet_availables = [];
					this.table_columns_availables = [];
					this.sheet_columns_availables = [];
					
					this.associate_columns = [];
					this.column_table_to_associate = [];
					this.column_sheet_to_associate = [];
					
					this.sheet_table_asigned = res.data.sheet_table_asigned;
					this.all_sheet_columns = res.data.sheet_columns;
					this.renderTable();
					this.renderSelect();
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}	
			})
			.catch((error) =>{
			  console.log(error);
			});
		},
		getTranslateColumnSheet: function(sheet, key_column){
			for (var property in this.all_sheet_columns) {
				if(property == sheet){
					for (let [key, value] of Object.entries(this.all_sheet_columns[property])){
						if(key == key_column){
							return value;
						}
					}				
				}
			}
			return '';
		},
		isColumnBusy: function(sheet, key_column){
			for (let i=0;i<this.associate_columns.length; i++){
				if(this.associate_columns[i]['sheet'] == sheet && this.associate_columns[i]['value'] == key_column){
					return true;
				}
			}
			return false;
		},
		getIdTable: function(sheet){
			for (let i=0;i<this.column_table_to_associate.length; i++){
				if(this.column_table_to_associate[i]['sheet'] == sheet){
					return this.column_table_to_associate[i]['id'];
				}
			}
			return 0;
		},
		renderSelect: function(){
			let count = 0;
			for (var property in this.all_sheet_columns){
				for (let [key, value] of Object.entries(this.all_sheet_columns[property])){
					if(!this.isColumnBusy(`${property}`, `${key}`)){						
						this.column_sheet_to_associate.push(
						{
							'sheet': `${property}`,
							'key': `${key}`,
							'value': `${value}`
						});
						count++;
					}
				}
				if(count > 0){
					this.sheet_availables.push(
					{
						'key': this.getIdTable(`${property}`),
						'value': `${property}`
					});
				}
				count = 0;
			}
		},
		renderTable: function(){
			if(this.sheet_table_asigned.length > 0){
				let relation;				
				for (let i = 0; i < this.sheet_table_asigned.length; i++) {
					relation = JSON.parse(this.sheet_table_asigned[i]['relation']);
					for (let [key, value] of Object.entries(relation)) {
						if(value !== ''){
							let translate = this.getTranslateColumnSheet(`${this.sheet_table_asigned[i]['sheet']}`, `${value}`);
							this.associate_columns.push(
							{
							'id': `${this.sheet_table_asigned[i]['id']}`,
							'sheet': `${this.sheet_table_asigned[i]['sheet']}`,
							'table': `${this.sheet_table_asigned[i]['tmp_table']}`,
							'key': `${key}`,
							'value': `${value}`,
							'translate_value': translate,
							});
						}
						else{
							this.column_table_to_associate.push(
							{
							'id': `${this.sheet_table_asigned[i]['id']}`,
							'sheet': `${this.sheet_table_asigned[i]['sheet']}`,
							'table': `${this.sheet_table_asigned[i]['tmp_table']}`,
							'key': `${key}`
							});
						}
					}
				}
			}
		},
		selectColumTableAndSheetAvailable: function(){
			let sheet = this.selected_sheet.value;
			this.table_columns_availables = [];
			this.sheet_columns_availables = [];
			this.selected_column_table = '';
			this.selected_column_sheet = '';
			if(sheet != ''){
				let column_table_to_associate = this.column_table_to_associate;
				for(let i  in column_table_to_associate){
					if(`${column_table_to_associate[i]['sheet']}` == sheet){
						this.table_columns_availables.push({
							'key': `${column_table_to_associate[i]['key']}`
						});
					}
				}
				let column_sheet_to_associate = this.column_sheet_to_associate;
				for(let i in this.column_sheet_to_associate){
					if(`${column_sheet_to_associate[i]['sheet']}` == sheet){
						this.sheet_columns_availables.push({
							'key': `${column_sheet_to_associate[i]['key']}`,
							'value': `${column_sheet_to_associate[i]['value']}`
						});
					}
				}
			}
		},
		sendForm: function(){
			if (this.selected_sheet && this.selected_column_table && this.selected_column_sheet) {
				this.form_data = new FormData();
				this.form_data.append('selected_id', this.selected_sheet.id);
				this.form_data.append('selected_key', this.selected_column_table);
				this.form_data.append('selected_value', this.selected_column_sheet);
				
				var url_associate_columns = APPURL+'upload/set-associate-columns';
				
				axios
				.post(url_associate_columns, this.form_data)
				.then((res) => {
					if (res.data.success) {
						this.$nextTick(this.loadTodo);
					}
					if (res.data.error) {
						$('#error').html(res.data.error)
					}
				})
				.catch((error) =>{
				  console.log(error);
				});
				
			}			
		},
		removeItem: function(item, index) {
			this.associate_columns.splice(index, 1);
			this.form_data = new FormData();
			this.form_data.append('selected_id', item.id);
			this.form_data.append('selected_sheet', item.sheet);
			this.form_data.append('selected_table_available', item.tmp_table);
			this.form_data.append('selected_key', item.key);
			this.form_data.append('selected_value', item.value);
			
			var url_reverse_associate_column = APPURL+'upload/reverse-associate-columns';

			axios
			.post(url_reverse_associate_column, this.form_data)
			.then((res) => {
				if (res.data.success) {
					this.$nextTick(this.loadTodo);
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}
			})
			.catch((error) =>{
				console.log(error);
			});
		},
		loadFileInDatabase: function(){
			var url_load_file_in_database = APPURL+'upload/load-file-in-database';

			axios
			.get(url_load_file_in_database)
			.then((res) => {
				if (res.data.success) {
					this.selected_sheet = [];
					this.sheet_availables = [];
					this.table_columns_availables = [];
					this.sheet_columns_availables = [];
					
					this.associate_columns = [];
					this.column_table_to_associate = [];
					this.column_sheet_to_associate = [];
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}
			})
			.catch((error) =>{
				console.log(error);
			});
		}
	}
});

var load_on_table = new Vue({
  el: "#load_data_on_tables"
})
Vue.component('load-summary', {
	template: '#load-summary-template',
	data: function() {
		return {
			show_preview: true,
			summary: [
			]
		}
	},
	mounted () {
		this.$nextTick(this.loadFileInDatabase);
	},
	methods: {
		loadFileInDatabase: function(){
			var url = APPURL+'upload/load-file-in-database';
			axios.get(url)
			.then((res) => {
				if (res.data.success) {
					this.show_preview = false;
					this.summary = res.data.summary;
				}
				if (res.data.error) {
					$('#error').html(res.data.error)
				}	
			})
			.catch((error) =>{
			  console.log(error);
			});
		}
	}
});

var load_summary = new Vue({
  el: "#load_summary"
})