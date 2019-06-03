
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
			var url_preview_data = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/load-preview-data';
			axios.get(url_preview_data)
			.then((res) => {
				if (res.data.success) {
					this.preview_data = res.data.topfive_elements;
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
	var url = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/get-worksheets';
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
		
		var url_tables_availables = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/get-tables-available';
		
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
		var url_asociate = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/get-associate-sheet-table';
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
			
			var url_associate_sheet_table = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/set-associate-sheet-table';
			
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
		
		var url_reverse_associate_sheet_table = 'http://www.full-stack.cl/load-unload-spreadsheet/upload/reverse-associate-sheet-table';
		
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
  template: '#load-data-template-on-tables'
  
})

var load_on_table = new Vue({
  el: "#load_data_on_tables"
})
