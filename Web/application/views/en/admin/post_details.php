<div class="main">
	<div class="container">
		<h1>{post_details_text} {post_id}
			<?php 
			if($post_info && $post_info['post_title']) 
				echo $comma_text." ".$post_info['post_title'];
			?>
		</h1>
		
		<?php 
			if(!$post_info) {
		?>
			<h4>{not_found_text}</h4>
		<?php 
			}else{ 
		?>
			<div class="container">
				<?php echo form_open(get_admin_post_details_link($post_id),array()); ?>
					<input type="hidden" name="post_type" value="edit_post" />
					<div class="row even-odd-bg" >
						<div class="three columns">
							<span>{creator_user_text}</span>
						</div>
						<div class="six columns">
							<span>
								<?php echo $code_text." ".$post_info['user_id']." - ".$post_info['user_name'];?>
							</span>							
						</div>
					</div>
					<div class="row even-odd-bg" >
						<div class="three columns">
							<span>{active_text}</span>
						</div>
						<div class="six columns">
							<input type="checkbox" class="graphical" 
								<?php if($post_info['post_active']) echo "checked"; ?>
							/>
						</div>
					</div>
					<div class="row even-odd-bg" >
						<div class="three columns">
							<span>{allow_comment_text}</span>
						</div>
						<div class="six columns">
							<input type="checkbox" class="graphical" 
								<?php if($post_info['post_allow_comment']) echo "checked"; ?>
							/>
						</div>
					</div>
					<div class="tab-container">
						<ul class="tabs">
							<?php foreach($post_contents as $pc) { ?>
								<li>
									<a href="#post_<?php echo $pc['pc_lang_id'];?>">
										<?php echo $langs[$pc['pc_lang_id']];?>
									</a>
								</li>
							<?php } ?>
						</ul>
						<script type="text/javascript">
							$(function(){
							   $('ul.tabs').each(function(){
									var $active, $content, $links = $(this).find('a');
									$active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
									$active.addClass('active');

									$content = $($active[0].hash);

									$links.not($active).each(function () {
									   $(this.hash).hide();
									});

									$(this).on('click', 'a', function(e){
									   $active.removeClass('active');
									   $content.hide();

									   $active = $(this);
									   $content = $(this.hash);

									   $active.addClass('active');

									   $content.show();						   	

									   e.preventDefault();
									   
									   <?php if(0) { ?>
										   //since each tab has different height, 
										   //we should reequalize  height of sidebar and main div.
										   //may be a bad hack,
										   //which should be corrected in future versions.
										   //
										   //what should we  do ?
										   //we should allow developers to register a list of functions 
										   //to be called on document\.ready event,
										   //but each function has a priority, 
										   //so we can sort their execution by that priority.
										   //and this will solve the problem
										   //for example in this situation, in each load, we should first equalize height of
										   //all tabs, and then call setupMovingHeader 
										   //in this way we don't need to call setupMovingHeader in each tab change event
										<?php } ?>
									   setupMovingHeader();
									});
								});
							});
						</script>
						<?php foreach($post_contents as $pc) {?>
							<div class="tab" id="post_<?php echo $pc['pc_lang_id'];?>">
								<div class="container">
									<div class="row even-odd-bg" >
										<div class="three columns">
											<label>{name_text}</label>
											
										</div>
										<div class="six columns">
											<label>{value_text}</label>
											<input type="text" />
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<br><br>
					<div class="row">
							<div class="four columns">&nbsp;</div>
							<input type="submit" class=" button-primary four columns" value="{submit_text}"/>
					</div>				
				</form>
			</div>
		<?php 
			}
		?>
		



	</div>
</div>