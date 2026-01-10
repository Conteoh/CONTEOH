<?php 
namespace App\Models;
class Audit_trail_model extends MY_Model{

    protected $table = 'audit_trail';
	
	public const ACTION_ADD = 0;
	public const ACTION_EDIT = 1;
	public const ACTION_DELETE = 2;
	public const PLATFORM_DEALER = 1;
	public const PLATFORM_PARTNER = 2;
	public const PLATFORM_TUNER = 3;

    public function __construct()
    {
        parent::__construct();
    }

    public function insert_data($submit_data = [])
	{
        if(isset($submit_data['action']) && $submit_data['action'] == self::ACTION_EDIT) {
			$before_data = [];
			if(isset($submit_data['before'])){
				$before_data = json_decode($submit_data['before'],true);
			}
			$after_data = [];
			if(isset($submit_data['after'])){
				$after_data = json_decode($submit_data['after'],true);
			}
			foreach($before_data as $k => $v){
				if(isset($after_data[$k]) && $after_data[$k] == $v){
					unset($after_data[$k]);
					unset($before_data[$k]);
				}
				if(empty($after_data[$k]) && empty($before_data[$k])){
					unset($after_data[$k]);
					unset($before_data[$k]);
				}
			}
			$submit_data['before'] = json_encode($before_data);
			$submit_data['after'] = json_encode($after_data);
		}
		$builder =  $this->db->table($this->table);
        $builder->insert($submit_data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}
}
?>