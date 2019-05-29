
<div id="app">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php
				$i = 1;
				foreach($topfive_elements as $key => $elements):
				?>
				<div class="card" >
					<div class="card-header">
						<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapse<?php echo $i?>" aria-expanded="false" aria-controls="collapse<?php echo $i?>">
						<?php
							echo $key;
						?>
						</button>						
					  </div>
					  <div class="collapse" id="collapse<?php echo $i?>">
						<div class="card-body">
							<div class="card">
								<div class="card-header">
									<ul class="nav nav-tabs card-header-tabs" id="dynamic_tab<?php echo $i?>" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" href="#preview<?php echo $i?>" role="tab" aria-controls="preview<?php echo $i?>" aria-selected="true">Preview</a>
										</li>
										<li class="nav-item">
											<a class="nav-link"  href="#macth<?php echo $i?>" role="tab" aria-controls="macth<?php echo $i?>" aria-selected="false">Match Column</a>
										</li>
									</ul>
								</div>
								<div class="card-body">
									<div class="tab-content mt-3">
										<div class="tab-pane active" id="preview<?php echo $i?>" role="tabpanel">
											<?php
											$columns = array();
											if (array_key_exists($key, $cols_name)) {
												$columns = $cols_name[$key];
											}
											?>
											<div class="table-responsive">
											  <table class="table">
												<thead>
												 <tr>
													<?php
														foreach($columns as $col):
													?>
													 
														<th><?php echo $col?></th>
													  
													<?php
														endforeach;
													?>
													</tr>
												</thead>
												<tbody>												  
													<?php 
														foreach ($elements as $key => $element):
													?>
														<tr>
														<?php 
														foreach ($element as $value):
														?>
															<td><?php echo $value?></td>
														<?php 
															endforeach;
														?>
														</tr>	
													<?php 
														endforeach;
													?>
												</tbody>
											  </table>
											</div>
										</div>
										<div class="tab-pane" id="macth<?php echo $i?>" role="tabpanel" aria-labelledby="macth<?php echo $i?>-tab">  
											b
										</div>
									</div>
								</div>
							</div>
						</div>
				</div>
				<?php
				$i++;
				endforeach;
				?>
			</div>
		</div>
	</div>
</div>
<script>
	$( document ).ready(function() {
		$('.class-collapse').collapse();
		$('.card-header-tabs a').on('click', function (e) {
			e.preventDefault();
			$(this).tab('show');
		})
	});	
</script>