<?php

namespace App\Libraries;

use CodeIgniter\Library\BaseController;
use Cloudinary\Cloudinary;

class Cloudinary_client
{
	public $cloudinary;
	protected $Cloudinary_account_model;
	protected $Cloudinary_upload_log_model;

	protected $cloudinary_account_id;
	protected $cloud_name;

	public function __construct()
	{
		//Load Model
		$this->Cloudinary_account_model = model('App\Models\Cloudinary_account_model');
		$this->Cloudinary_upload_log_model = model('App\Models\Cloudinary_upload_log_model');

		//預設的帳號
		$option_list = [
			[
				"id" => 1,
				"cloud_name" => $_ENV['CLOUDINARY_CLOUD_NAME'],
				"api_key" => $_ENV['CLOUDINARY_API_KEY'],
				"api_secret" => $_ENV['CLOUDINARY_API_SECRET'],
			]
		];

		$result_list = $this->Cloudinary_account_model->get_all([
			'is_deleted' => 0,
			'status' => 1
		]);
		if (!empty($result_list)) {
			foreach ($result_list as $result) {
				if ($result['cloud_name'] != $_ENV['CLOUDINARY_CLOUD_NAME']) {
					$option_list[] = [
						"id" => $result['id'],
						"cloud_name" => $result['cloud_name'],
						"api_key" => $result['api_key'],
						"api_secret" => $result['api_secret'],
					];
				}
			}
		}

		//随机使用一个账号
		$random_index = array_rand($option_list);
		$option = $option_list[$random_index];

		$this->cloudinary_account_id = $option['id'];
		$this->cloud_name = $option['cloud_name'];

		$this->cloudinary = new Cloudinary(
			[
				'cloud' => [
					"cloud_name" => $option['cloud_name'],
					"api_key" => $option['api_key'],
					"api_secret" => $option['api_secret'],
				],
			]
		);
	}

	public function upload($path, $config = [], $user_id = 0)
	{
		$result = ($this->cloudinary->UploadApi())->upload($path, ["transformation" => [$config]]);

		$result_path = $result['secure_url'] ?? '';

		if (!empty($result_path)) {
			$this->Cloudinary_upload_log_model->insert_data([
				'created_date' => date('Y-m-d H:i:s'),
				'user_id' => $user_id,
				'cloudinary_account_id' => $this->cloudinary_account_id,
				'cloud_name' => $this->cloud_name,
				'result_path' => $result_path,
			]);
		}

		return $result_path;
	}
}
