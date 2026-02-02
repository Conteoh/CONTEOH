<?php

namespace App\Models;

class Jogging_model extends MY_Model
{

    protected $table = "jogging";

    public function __construct()
    {
        parent::__construct();

        $this->exportation_exclused_fields = [
            "is_deleted",
            "modified_date",
        ];
        $this->yes_no_fields = [];

	
    }

    public function section_kv_list()
    {
        return [
            "0" => "Morning",
			"1" => "Afternoon",
			"2" => "Evening",
			"3" => "Night",
        ];
    }

    /**
     * Get monthly jogging distance totals for a given year (km per month).
     * @param int $year
     * @return array [month_number => total_km] e.g. [1 => 10.5, 2 => 20, ...]
     */
    public function get_monthly_totals_by_year($year)
    {
        $builder = $this->db->table($this->table);
        $builder->select('MONTH(date) as month, SUM(distance_in_km) as total');
        $builder->where('YEAR(date)', (int) $year);
        $builder->where('is_deleted', 0);
        $builder->groupBy('MONTH(date)');
        $rows = $builder->get()->getResultArray();

        $out = [];
        for ($m = 1; $m <= 12; $m++) {
            $out[$m] = 0;
        }
        foreach ($rows as $row) {
            $m = (int) $row['month'];
            $out[$m] = (float) $row['total'];
        }
        return $out;
    }
}