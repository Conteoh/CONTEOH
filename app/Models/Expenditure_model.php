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

    /**
     * Get expenditure totals for dashboard: today, this week, this month.
     * @param int|null $user_id Filter by user (null = all users)
     * @return array ['today' => float, 'week' => float, 'month' => float]
     */
    public function get_dashboard_totals()
    {
        $where_base = ['is_deleted' => 0];
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end = date('Y-m-d', strtotime('sunday this week'));
        $month_start = date('Y-m-01');
        $month_end = date('Y-m-t');

        $totals = [
            'today' => $this->sum_total_amount(array_merge($where_base, ['date' => $today])),
            'week' => $this->sum_total_amount_date_range($where_base, $week_start, $week_end),
            'month' => $this->sum_total_amount_date_range($where_base, $month_start, $month_end),
        ];

        return $totals;
    }

    /**
     * Sum total_amount for given where conditions.
     */
    protected function sum_total_amount($where)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('total_amount', 'total');
        $builder->where($where);
        $row = $builder->get()->getRowArray();
        return $row && isset($row['total']) ? (float) $row['total'] : 0;
    }

    /**
     * Sum total_amount where date between date_from and date_to (inclusive).
     */
    protected function sum_total_amount_date_range($where_base, $date_from, $date_to)
    {
        $builder = $this->db->table($this->table);
        $builder->selectSum('total_amount', 'total');
        $builder->where($where_base);
        $builder->where('date >=', $date_from);
        $builder->where('date <=', $date_to);
        $row = $builder->get()->getRowArray();
        return $row && isset($row['total']) ? (float) $row['total'] : 0;
    }
}