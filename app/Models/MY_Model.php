<?php

namespace App\Models;

use CodeIgniter\Model;

abstract class MY_Model extends Model
{
    protected $table;
    protected $primaryKey;
    protected $allowedFields;
    public static $labelField;

    public function __construct()
    {
        parent::__construct();
        $this->primaryKey = 'id';

        $this->define_allowed_fields();
    }

    private function define_allowed_fields()
    {
        $query = $this->db->query('SELECT * FROM ' . $this->table . ' LIMIT 1');
        $field = $query->getFieldNames();
        if (!empty($field)) {
            foreach ($field as $v) {
                if ($v != $this->primaryKey) {
                    $this->allowedFields[] = $v;
                }
            }
        }
    }

    public function record_count($where = ['is_deleted' => 0], $like = [], $where_in = [])
    {

        $builder = $this->db->table($this->table);

        //where
        $builder->where($where);

        //like
        if (!empty($like)) {
            $builder->like($like);
        }

        //where in
        if (!empty($where_in)) {
            foreach ($where_in as $k => $v) {
                $builder->whereIn(strval($k), $v);
            }
        }

        return $builder->countAllResults();
    }

    public function fetch($item_per_page = 10, $start = 0, $where = ['is_deleted' => 0], $like = [], $order_by = [], $where_in = [], $select = "*")
    {

        $builder = $this->db->table($this->table);

        //select
        if ($select != "*") {
            $builder->select($select);
        }

        //where
        $builder->where($where);

        //like
        if (!empty($like)) {
            $builder->like($like);
        }

        //order by 
        if (!empty($order_by)) {
            foreach ($order_by as $k => $v) {
                $builder->orderBy($k, $v);
            }
        } else {
            $builder->orderBy($this->primaryKey, "DESC");
        }

        //where in
        if (!empty($where_in)) {
            foreach ($where_in as $k => $v) {
                $builder->whereIn(strval($k), $v);
            }
        }

        //limit & offset
        $builder->limit($item_per_page, $start);

        $query = $builder->get()->getResultArray();
        return $query;
    }

    public function get_one($where = ['is_deleted' => 0], $order_by = [], $select = "*", $like = [])
    {
        $builder = $this->db->table($this->table);

        //where
        $builder->where($where);

        //order by 
        if (!empty($order_by)) {
            foreach ($order_by as $k => $v) {
                $builder->orderBy($k, $v);
            }
        } else {
            $builder->orderBy($this->primaryKey, "DESC");
        }

        //select
        if ($select != "*") {
            $builder->select($select);
        }

        //like
        if (!empty($like)) {
            $builder->like($like);
        }
        $builder->limit(1);
        $query = $builder->get()->getRowArray();

        return $query;
    }

    public function get_all($where = ['is_deleted' => 0], $order_by = ['id' => 'DESC'], $select = "*", $where_in = [], $like = [], $or_like = [], $limit = 0)
    {
        $builder =  $this->db->table($this->table);

        //select
        if ($select != "*") {
            $builder->select($select);
        }

        //ordering
        foreach ($order_by as $k => $v) {
            $builder->orderBy($k, strval($v));
        }

        //where in
        if (!empty($where_in)) {
            foreach ($where_in as $k => $v) {
                $builder->whereIn(strval($k), $v);
            }
        }

        //like
        if (!empty($like)) {
            $builder->like($like);
        }

        if (!empty($or_like)) {
            $builder->groupStart();
            $counter = 0;
            foreach ($or_like as $k => $v) {
                if ($counter == 0) {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            $builder->orLike($k, $v2);
                        }
                    } else {
                        $builder->like($k, $v);
                    }
                } else {
                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            $builder->orLike($k, $v2);
                        }
                    } else {
                        $builder->orLike($k, $v);
                    }
                }
                $counter++;
            }
            $builder->groupEnd();
        }

        //where
        $builder->where($where);

        //limit
        if ($limit > 0) {
            $builder->limit($limit);
        }

        //groupby
        if (!empty($groupby)) {
            $builder->groupby($groupby);
        }

        $query = $builder->get()->getResultArray();
        return $query;
    }

    public function update_data($where = [], $submit_data = [])
    {
        $result = false;
        if (!empty($where) && !empty($submit_data)) {
            $builder =  $this->db->table($this->table);
            $builder->where($where);
            $result = $builder->update($submit_data);
        }
        return $result;
    }

    public function insert_data($submit_data = [])
    {
        $builder =  $this->db->table($this->table);
        $builder->insert($submit_data);
        $insert_id = $this->db->insertID();
        return $insert_id;
    }

    public function get_kv_list(
        $where = ['is_deleted' => 0],
        $value = "title",
        $key = "id",
        $glue = " - ",
        $order_by = ['id' => 'ASC'],
        $where_in_col = "",
        $where_in =  []
    ) {
        $builder =  $this->db->table($this->table);

        //select
        $select = [$key];
        $explode_value = explode(",", $value);
        foreach ($explode_value as $k => $v) {
            $select[] = $v;
        }
        $builder->select(implode(",", $select));

        //order by
        if (!empty($order_by)) {
            foreach ($order_by as $k => $v) {
                $builder->orderBy($k, $v);
            }
        } else {
            $builder->orderBy($this->primaryKey, "DESC");
        }

        //where
        $builder->where($where);

        //where in
        if (!empty($where_in) && !empty($where_in_col)) {
            $builder->whereIn($where_in_col, $where_in);
        }

        //get record from db
        $list = $builder->get()->getResultArray();

        //assembly final result
        $kvList = [];

        if (!empty($list)) {
            foreach ($list as $l) {
                $value_need = array();
                foreach ($explode_value as $v2) {
                    $value_need[] = $l[$v2];
                }

                $kvList[$l[$key]] = implode($glue, $value_need);
            }
        }

        return $kvList;
    }

    public function kv_list_to_info($kv_list = [])
    {
        $kv_info = [];

        foreach ($kv_list as $k => $v) {
            $kv_info[] = [
                'id' => $k,
                'title' => $v,
            ];
        }

        return $kv_info;
    }

    public function yes_no_kv_list()
    {
        return [
            '0' => 'No',
            '1' => 'Yes',
        ];
    }

    public function get_sum($field, $where = ['is_deleted' => 0], $like = [], $where_in = [])
    {
        $builder =  $this->db->table($this->table);

        $builder->selectSum($field);

        $builder->where($where);

        //where in
        if (!empty($where_in)) {
            foreach ($where_in as $k => $v) {
                $builder->whereIn(strval($k), $v);
            }
        }

        //like
        if (!empty($like)) {
            $builder->like($like);
        }

        $result = $builder->get()->getRowArray();

        $amount = 0;
        if (isset($result[$field]) && !empty($result[$field])) {
            $amount = $result[$field];
        }
        return $amount;
    }

    public function get_table_fields($full = false)
    {
        $result = $this->db->getFieldData($this->table);
        if ($full) {
            $response = [];
            foreach ($result as $k => $v) {
                $response[$v->name] = $v->type;
            }
        } else {
            $response = [
                'where' => [],
                'like' => [],
            ];
            foreach ($result as $k => $v) {
                if ($v->type == "int" || $v->type == "tinyint") {
                    $response['where'][] = $v->name;
                } else {
                    $response['like'][] = $v->name;
                }
            }
        }

        return $response;
    }

    public function is_field_exist($target_field = "company_id")
    {
        $query = $this->db->query('SELECT * FROM ' . $this->table . ' LIMIT 1');
        $fields = $query->getFieldNames();

        return in_array($target_field, $fields);
    }

    public function batch_update($data = array(), $key = false)
    {
        if (!$key) {
            $key = $this->primaryKey;
        }
        if (!empty($data)) {
            $builder =  $this->db->table($this->table);

            $builder->updateBatch($data, $key);
        }
    }

    public function batch_insert($data = array())
    {

        if (!empty($data)) {
            $builder =  $this->db->table($this->table);
            $builder->insertBatch($data);
        }
    }

    public function check_is_number_field($field_name)
    {
        $query = $this->db->query('SELECT * FROM ' . $this->table . ' LIMIT 1');
        $fields = $query->getFieldNames();
        $is_number = false;
        foreach ($fields as $field) {
            if ($field == $field_name) {
                if (in_array($field, ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
                    $is_number = true;
                }
            }
        }
        return $is_number;
    }

    //Soft Delete
    public function delete_data($id, $user_id = 0, $origin = "")
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->primaryKey, $id);
        $builder->update(['is_deleted' => 1, 'modified_date' => date("Y-m-d H:i:s"), 'modified_by' => $user_id]);

        //Insert Audit Trail
        $this->Audit_trail_model->insert_data([
            "created_date" => date("Y-m-d H:i:s"),
            "user_id" => $user_id,
            "ref_table" => $this->table,
            "ref_id" => $id,
            "action" => 2,
            "origin" => !empty($origin) ? $origin : null,
        ]);
    }

    public function insert_batch($data_list = [])
    {
        if (!empty($data_list)) {
            $builder =  $this->db->table($this->table);
            $builder->insertBatch($data_list);
        }
    }
}
