<div class="main">
	<div class="container category">
		<h1><?php echo $post_info['pc_title'];?></h1>
		<div class="post-date"><?php echo str_replace("-","/",$post_info['post_date']);?></div>
		<div class="row">
			<?php if($post_info['pc_image']) { ?>
				<div class="post-img" style="background-image:url('<?php echo $post_info['pc_image'];?>')">
				</div>
				<br><br>
			<?php } ?>
			<div class="twelve columns post-content">
				<?php echo $post_info['pc_content'] ?>
			</div>
		</div>
	</div>
</div>