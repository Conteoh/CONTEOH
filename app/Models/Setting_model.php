<?php 
namespace App\Models;
class Setting_model extends MY_Model{

    protected $table = 'setting';

    public function __construct()
    {
        parent::__construct();
    }
    
    public function item_per_page_kv_list()
    {
        return [
            '10' => '10',
            '20' => '20',
            '50' => '50',
            '100' => '100',
        ];
    }

    public function get_paging($item_per_page, $pagenum, $total_item, $page, $url)
    {

        $start = (int)(($page - 1) / $pagenum) * $pagenum + 1;
        $end = $start + $pagenum - 1;
        $next = $page + 1;
        $pre = $page - 1;

        $total_page = ceil($total_item / $item_per_page);
        $paging = '';
        if ($total_item > $item_per_page) {
            $paging .= '<ul class="pagination">';

            if ($page > 1) {

                if (strpos($url, '?')) {

                    $url_array = explode('?', $url);

                    $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . '1?' . $url_array[1] . '">&laquo;</a></li>';
                    $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . $pre . '?' . $url_array[1] . '">&lsaquo;</li>';
                } else {
                    $paging .= '<li class="pagination-btn"><a href="' . $url . '1">&laquo;</a></li>';
                    $paging .= '<li class="pagination-btn"><a href="' . $url . $pre . '">&lsaquo;</li>';
                }
            }

            if ($total_page <= $pagenum) {

                for ($i = $start; $i <= $total_page; $i++) {
                    if ($i == $page) {

                        $paging .= '<li class="active pagination-btn"><a href="javascript:void(0)">' . $i . '</a></li>';
                    } else {

                        if (strpos($url, '?')) {

                            $url_array = explode('?', $url);
                            $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . $i . '?' . $url_array[1] . '">' . $i . '</a></li>';
                        } else {
                            $paging .= '<li class="pagination-btn"><a href="' . $url . $i . '">' . $i . '</a></li>';
                        }
                    }
                }
            } else {
                if ($page > 5) {
                    $end = $page + 5;
                    if ($end > $total_page) {
                        $end = $total_page;
                    }

                    $start = $end - ($pagenum - 1);

                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $page) {
                            $paging .= '<li class="active pagination-btn"><a href="javascript:void(0)">' . $i . '</a></li>';
                        } else {

                            if(strpos($url,'?')){
							
								$url_array = explode('?',$url);													
								$paging .= '<li class="pagination-btn"><a href="'.$url_array[0].$i.'?'.$url_array[1].'">'.$i.'</a></li>';
													
							}else{
                                $paging .= '<li class="pagination-btn"><a href="' . $url . $i . '">' . $i . '</a></li>';
                            }

                        }
                    }
                } else {
                    if ($end > $total_page) {
                        $end = $total_page;
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $page) {
                            $paging .= '<li class="active pagination-btn"><a href="javascript:void(0)">' . $i . '</a></li>';
                        } else {

                            if (strpos($url, '?')) {

                                $url_array = explode('?', $url);
                                $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . $i . '?' . $url_array[1] . '">' . $i . '</a></li>';
                            } else {
                                $paging .= '<li class="pagination-btn"><a href="' . $url . $i . '">' . $i . '</a></li>';
                            }
                        }
                    }
                }
            }

            if ($page < $total_page) {

                if (strpos($url, '?')) {

                    $url_array = explode('?', $url);

                    $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . $next . '?' . $url_array[1] . '">&rsaquo;</a></li>';
                    $paging .= '<li class="pagination-btn"><a href="' . $url_array[0] . $total_page . '?' . $url_array[1] . '">&raquo;</a></li>';
                } else {
                    $paging .= '<li class="pagination-btn"><a href="' . $url . $next . '">&rsaquo;</a></li>';
                    $paging .= '<li class="pagination-btn"><a href="' . $url . $total_page . '">&raquo;</a></li>';
                }
            }

            $paging .= '</ul>';
        }

        return $paging;
    }
}
?>