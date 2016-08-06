<?php

#############################################################################
#	Version 1.2014.08.21
#  	Original developed to OpenCart 1.5.6.4 by  Joel Correia, web: www.joelcorreia.com email: email@joelcorreia.com
#
#############################################################################
class ControllerModulecategoryimporter extends Controller {
	private $error = array();

	public function index()
	{

		$this->load->language('module/category_importer');

		$this->document->setTitle = $this->language->get('heading_title');
		$this->load->model('setting/setting');
		$this->data['observations'] = "";

		$this->data['text_none'] =  $this->language->get('text_none');
		$this->data['heading_title'] =  $this->language->get('heading_title');
		$this->data['text_import_or_sincronize_title'] =  $this->language->get('text_import_or_sincronize_title');
		$this->data['text_csv_example_title'] =  $this->language->get('text_csv_example_title');
		$this->data['text_csv_head_example'] =  $this->language->get('text_csv_head_example');
		$this->data['text_csv_row_example'] =  $this->language->get('text_csv_row_example');
		$this->data['text_execute'] =  $this->language->get('text_execute');
		$this->data['text_upload_file'] =  $this->language->get('text_upload_file');
		$this->data['entry_version_status'] =  $this->language->get('entry_version_status');
		$this->data['text_export_title'] =  $this->language->get('text_export_title');
		$this->data['text_download_csv_title'] =  $this->language->get('text_download_csv_title');
		$this->data['text_download_csv_observations'] =  $this->language->get('text_download_csv_observations');

		$this->data['error_permission'] =  $this->language->get('error_permission');


		$observations0 = "";


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate()))
		{
			$this->model_setting_setting->editSetting('category_importer', $this->request->post);

			$mysqli_link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

			$post_categories = '';
			if (isset($this->request->post['categories']))
				$post_categories = $this->request->post['categories'];
		 	else
		 		$post_categories = '';

		 		//$post_categories = str_replace('&gt;','>',$post_categories);
			/*
				Components
				Components -> Mice and Trackballs
				Components -> Monitors
				Components -> Monitors -> test 1
				Components -> Monitors -> test 2
				Components -> Printers
				Components -> Printers -> asdadasd
				Components -> Scanners
				Components -> Web Cameras
			*/
			$lines = explode("\n", $post_categories);
			$n_lines = 0;
			$n_category = 0;
			$new_categories = 0;
			foreach ($lines as $line) {

				$categories = explode(" -&gt; ", $line);
				$number_of_categories = count($categories);
				$number_of_categories_before_that_exists = 0;
				$array_of_parents_categories_id = array();				

				$level = 0;
				$parent_category_id = 0;
				$category_id = 0;
				$parent_category_name = '';

				foreach ($categories as $category) {
					$category = trim($category);
					$category = str_replace('	','',str_replace('  ',' ',str_replace('  ',' ',$category)));
					$n_category++;
					$level++;

					if($level==1)
						$category_id = $this->db->query("(SELECT MAX(IFNULL(category_id,0)) category_id FROM " . DB_PREFIX . "category_description WHERE name = '" . mysqli_real_escape_string($mysqli_link, $category) . "' LIMIT 1) UNION (SELECT 0)")->row['category_id'];
					else
						$category_id = $this->db->query("(SELECT IFNULL(" . DB_PREFIX . "category.category_id,0) category_id FROM  " . DB_PREFIX . "category_description, " . DB_PREFIX . "category WHERE  " . DB_PREFIX . "category_description.category_id= " . DB_PREFIX . "category.category_id AND  " . DB_PREFIX . "category_description.name = '" . mysqli_real_escape_string($mysqli_link, $category) . "' AND  " . DB_PREFIX . "category.parent_id=". $parent_category_id. " LIMIT 1) UNION (SELECT 0)")->row['category_id'];

					$array_of_parents_categories_id[] = $category_id;
					/*
					if($level>=2 && $category_id>0)
					{
						//$current_parent_category_id = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = " . $category_id)->row['parent_id'];
						$category_id = 0;
					}
					*/

					if($category_id==0 && $category!='')
					{
						//insert new category
						$this->load->model('localisation/language');
						$this->data['languages'] = $this->model_localisation_language->getLanguages();

						foreach ($this->data['languages'] as $language)
						{
							$data_new_category['category_description'][$language['language_id']] =
							array(
								'name'             => $category,
								'description'      => $category,
								'meta_keyword'     => $category,
								'meta_description' => $category
							);
						}
						$data_new_category['category_store'][0] = 0;

						$this->load->model('setting/store');
						$this->data['stores'] = $this->model_setting_store->getStores();
						foreach ($this->data['stores'] as $store)
						{
							$data_new_category['category_store'][] = $store['store_id'];
						}

						$data_new_category['parent_id'] = $parent_category_id;
						$data_new_category['column'] = 0;
						$data_new_category['top'] = 1;
						$data_new_category['sort_order'] = 0;
						$data_new_category['status'] = 1;
						$data_new_category['keyword'] = $category;

						$this->load->model('catalog/category');
						$this->model_catalog_category->addCategory($data_new_category);

						$category_id = $this->db->query("(SELECT MAX(IFNULL(category_id,0)) category_id FROM " . DB_PREFIX . "category_description WHERE name = '" . mysqli_real_escape_string($mysqli_link, $category) . "' LIMIT 1) UNION (SELECT 0)")->row['category_id'];
						$new_categories++;
					}

					$parent_category_id = $category_id;
					$parent_category_name = $category;
				}
			    $n_lines++;
			}

			$this->session->data['success'] = $this->language->get('text_success') . $new_categories;

			mysqli_close($link);
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');

		$this->data['text_choose_file_upload'] = $this->language->get('text_choose_file_upload');
		$this->data['text_upload_file'] = $this->language->get('text_upload_file');

		$this->data['entry_version_status'] = $this->language->get('entry_version_status');
		$this->data['entry_author'] = $this->language->get('entry_author');
		$this->data['entry_help'] = $this->language->get('entry_help');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
        $this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->error['sort_order'])) {
			$this->data['error_sort_order'] = $this->error['sort_order'];
		} else {
			$this->data['error_sort_order'] = '';
		}
		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}

        $this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER .'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER .'index.php?route=extension/module&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => HTTPS_SERVER .'index.php?route=module/category_importer&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('module/category_importer', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['export_csv'] = $this->url->link('module/category_importer/download', 'token=' . $this->session->data['token'], 'SSL');
		//$this->data['export_csv'] = $this->data['export_csv'] . '&attribute1=attribute1value&&attribute2=attribute2value';

		$this->data['modules'] = array();

		if (isset($this->request->post['category_importer_module'])) {
			$this->data['modules'] = $this->request->post['category_importer_module'];
		} elseif ($this->config->get('category_importer_module')) {
			$this->data['modules'] = $this->config->get('category_importer_module');
		}


        $this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'module/category_importer.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

        $this->response->setOutput($this->render());
	}

	private function validate() {

		if (!$this->user->hasPermission('modify', 'module/category_importer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function download() {

		//if (($this->request->server['REQUEST_METHOD'] == 'GET') && ($this->validate())){
		if ($this->validate()) {

		$csv_file = '';

		$this->load->model('catalog/category');
		 	$results = $this->model_catalog_category->getCategories(0);
			foreach ($results as $result) {
				$csv_file .=sprintf("%s\n",str_replace('&gt;','->',$result['name']));
				/*
				$this->data['categories'][] = array(
					'category_id' => $result['category_id'],
					'name'        => $result['name'],
					'sort_order'  => $result['sort_order'],
					'selected'    => isset($this->request->post['selected']) && in_array($result['category_id'], $this->request->post['selected']),
					'action'      => $action
				);
				*/
			}

			$this->response->addHeader('Content-Type: text/txt;');
			$this->response->addHeader(sprintf('Content-Disposition: attachment; filename=categories%s.txt',date("Ymd")));
			$this->response->setOutput($csv_file);

		} else {

			// return a permission error page
			return $this->forward('error/permission');
		}
	}



}
?>
