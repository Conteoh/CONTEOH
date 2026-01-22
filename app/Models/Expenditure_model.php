<?php

namespace App\Models;

class Expenditure_model extends MY_Model
{

    protected $table = "expenditure";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        $this->yes_no_fields = [];

	
    }

    public function record_count_with_tags($where = ['is_deleted' => 0], $like = [], $tags_filter = [], $where_in = [])
    {
        $builder = $this->db->table($this->table);

        //where
        $builder->where($where);

        //like
        if (!empty($like)) {
            $builder->like($like);
        }

        //tags filter - expenditure tags field should contain any of the selected tags
        if (!empty($tags_filter)) {
            $builder->groupStart();
            $first = true;
            foreach ($tags_filter as $tag) {
                if ($first) {
                    $builder->like('tags', $tag);
                    $first = false;
                } else {
                    $builder->orLike('tags', $tag);
                }
            }
            $builder->groupEnd();
        }

        //where in
        if (!empty($where_in)) {
            foreach ($where_in as $k => $v) {
                $builder->whereIn(strval($k), $v);
            }
        }

        return $builder->countAllResults();
    }

    public function fetch_with_tags($item_per_page = 10, $start = 0, $where = ['is_deleted' => 0], $like = [], $tags_filter = [], $order_by = [], $where_in = [], $select = "*")
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

        //tags filter - expenditure tags field should contain any of the selected tags
        if (!empty($tags_filter)) {
            $builder->groupStart();
            $first = true;
            foreach ($tags_filter as $tag) {
                if ($first) {
                    $builder->like('tags', $tag);
                    $first = false;
                } else {
                    $builder->orLike('tags', $tag);
                }
            }
            $builder->groupEnd();
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

    
}