<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AE_Post extends Burge_CMF_Controller {

	function __construct()
	{
		parent::__construct();

		$this->lang->load('ae_post',$this->selected_lang);
		$this->load->model("post_manager_model");

	}

	public function index()
	{
		if($this->input->post("post_type")==="add_post")
			return $this->add_post();

		$this->set_posts_info();

		//we may have some messages that our post has been deleted successfully.
		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_link("admin_post",TRUE));
		$this->data['header_title']=$this->lang->line("posts");

		$this->send_admin_output("post");

		return;	 
	}	

	private function set_posts_info()
	{
		$filters=array();
		$filters['lang']=$this->language->get();

		$this->data['posts_info']=$this->post_manager_model->get_posts($filters);

		return;
	}

	private function add_post()
	{
		$post_id=$this->post_manager_model->add_post();

		return redirect(get_admin_post_details_link($post_id));
	}

	public function details($post_id)
	{
		if($this->input->post("post_type")==="edit_post")
			return $this->edit_post($post_id);

		if($this->input->post("post_type")==="delete_post")
			return $this->delete_post($post_id);

		$this->data['post_id']=$post_id;
		$post_info=$this->post_manager_model->get_post($post_id);

		$this->data['langs']=$this->language->get_languages();

		$this->data['post_contents']=array();
		foreach($this->data['langs'] as $lang => $val)
			foreach($post_info as $pi)
				if($pi['pc_lang_id'] === $lang)
				{
					$this->data['post_contents'][$lang]=$pi;
					break;
				}
		if($post_info)
			$this->data['post_info']=array(
				"post_date"=>str_replace("-","/",$post_info[0]['post_date'])
				,"post_allow_comment"=>$post_info[0]['post_allow_comment']
				,"post_active"=>$post_info[0]['post_active']
				,"user_name"=>$post_info[0]['user_name']
				,"user_id"=>$post_info[0]['user_id']
				,"categories"=>$post_info[0]['categories']
				,"post_title"=>$this->data['post_contents'][$this->language->get()]['pc_title']
			);
		else
			$this->data['post_info']=array();

		$this->load->model("category_manager_model");
		$this->data['categories']=$this->category_manager_model->get_hierarchy("checkbox",$this->selected_lang);

		$this->data['message']=get_message();
		$this->data['lang_pages']=get_lang_pages(get_admin_post_details_link($post_id,TRUE));
		$this->data['header_title']=$this->lang->line("post_details")." ".$post_id;

		$this->send_admin_output("post_details");

		return;
	}

	private function delete_post($post_id)
	{
		$this->post_manager_model->delete_post($post_id);

		set_message($this->lang->line('post_deleted_successfully'));

		return redirect(get_link("admin_post"));
	}

	private function edit_post($post_id)
	{
		$post_props=array();
		$post_props['categories']=$this->input->post("categories");

		$post_props['post_date']=$this->input->post('post_date');
		$post_props['post_active']=(int)($this->input->post('post_active') === "on");
		$post_props['post_allow_comment']=(int)($this->input->post('post_allow_comment') === "on");
		
		$post_content_props=array();
		foreach($this->language->get_languages() as $lang=>$name)
		{
			$post_content=$this->input->post($lang);
			$post_content['pc_content']=$_POST[$lang]['pc_content'];
			$post_content['pc_lang_id']=$lang;

			if(isset($post_content['pc_active']))
				$post_content['pc_active']=(int)($post_content['pc_active']=== "on");
			else
				$post_content['pc_active']=0;

			$post_content['pc_gallery']=$this->get_post_gallery($post_id,$lang);

			$post_content_props[]=$post_content;
		}

		$this->post_manager_model->set_post_props($post_id,$post_props,$post_content_props);
		
		set_message($this->lang->line("changes_saved_successfully"));

		redirect(get_admin_post_details_link($post_id));

		return;
	}

	private function get_post_gallery($post_id, $lang)
	{
		$pp=$this->input->post($lang);
		$pp=$pp['pc_gallery'];
		//bprint_r($pp);

		$gallery=array();
		$gallery['last_index']=0;
		$gallery['images']=array();

		$last_index=&$gallery['last_index'];

		if(isset($pp['old_images']))
			foreach($pp['old_images'] as $index)
			{
				$img=$pp['old_image_image'][$index];
				$delete=isset($pp['old_image_delete'][$index]);
				if($delete)
				{
					unlink(POST_GALLERY_DIR."/".$img);
					continue;
				}

				$text=$pp['old_image_text'][$index];
				$gallery['images'][$index]=array(
					"image"	=> $img
					,"text"	=> $text
				);

				$last_index=max(1+$index,$last_index);
			}
		


		if(isset($pp['new_images']))
			foreach($pp['new_images'] as $index)
			{
				$file_names=$_FILES[$lang]['name']['pc_gallery']['new_image'][$index];
				$file_tmp_names=$_FILES[$lang]['tmp_name']['pc_gallery']['new_image'][$index];
				$file_errors=$_FILES[$lang]['error']['pc_gallery']['new_image'][$index];
				$file_sizes=$_FILES[$lang]['size']['pc_gallery']['new_image'][$index];
				$text=$pp['new_text'][$index];
				$watermark=isset($pp['new_image_watermark'][$index]);

				foreach($file_names as $findex => $file_name)
				{
					if($file_errors[$findex])
						continue;

					$extension=pathinfo($file_names[$findex], PATHINFO_EXTENSION);

					if($watermark)
						burge_cmf_watermark($file_tmp_names[$findex]);

					$img_name=$post_id."_".$lang."_".$last_index."_".get_random_word(5).".".$extension;
					$file_dest=POST_GALLERY_DIR."/".$img_name;
					move_uploaded_file($file_tmp_names[$findex], $file_dest);

					$gallery['images'][$last_index++]=array(
						"image"	=> $img_name
						,"text"	=> $text
						);
					//echo "***<br>".$file_name."<br>".$file_sizes[$findex]."<br>".$text."<br>watermark:".$watermark."<br>###<br>";
				}			
			}
		
		//bprint_r($gallery);

		//we need in some positions to check if pc_gallery is null
		if(!sizeof($gallery['images']))
			return NULL;

		return $gallery;
	}
}