
<?php

class Admin_StatisticController extends FrontBaseAction {

    /**
     * (non-PHPdoc)
     * @see FrontBaseAction::init()
     */
    public function init() {
        parent::init();
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/helper/core.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/notifications/jgrowl.min.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('https://www.gstatic.com/charts/loader.js', 'text/javascript'));
        $this->view->headScript()->appendFile($this->autorefresh->autoRefreshRewriter('/ad-min/assets/js/plugins/sparkline.min.js', 'text/javascript'));
        $this->view->headLink()->appendStylesheet($this->autorefresh->autoRefreshRewriter("/ad-min/assets/css/site/_dev.css"));
        $this->loadJs(array('common', 'statistic'));
        $this->view->headTitle( "Thống Kê");
    }

    /**
     * Search page
     */
    public function indexAction() {
        if ($this->request->isPost()) {
            $orderMdl = new Order();
            $from_date = "";
            $error = array();
            if (empty($this->post_data["from_date"]) == true) {
                $error = "Vui lòng chọn ngày";
            }
            if (empty($this->post_data["to_date"]) == true) {
                $error = "Vui lòng chọn ngày";
            }
            if (empty($error) == true) {
                $from_date = str_replace('/', '-', $this->post_data["from_date"]);
                $to_date = str_replace('/', '-', $this->post_data["to_date"]);
                
                $from_date = date("Y-m-d H:i:s", strtotime($from_date));
                $to_date = date("Y-m-d H:i:s", strtotime($to_date));
                
                //build params
                $params["from_date"] = $from_date;
                $params["to_date"] = $to_date;
                //get data
                $order_list = $orderMdl->fetchAllOrder($params);
                //build response
                $res["number_of_order"] = 0;
                $res["number_of_order_delivered"] = 0;
                $res["number_of_order_canceled"] = 0;
                $res["number_of_order_processing"] = 0;
                $res["total_money"] = 0;
                $res["total_discount"] = 0;
                $res["total_earned_money"] = 0;
                if (empty($order_list) == false) {
                    $res["number_of_order"] = count($order_list);
                    foreach ($order_list as $key => $value) {
                        if ($value["is_pay"] == 1 && is_numeric($value["total"])) {
                            $res["total_earned_money"] = $res["total_earned_money"] + $value["total"];
                        }
                        if (is_numeric($value["total"])) {
                            $res["total_money"] = $res["total_money"] + $value["total"];
                        }
                        if (is_numeric($value["discount"]) > 0) {
                            $res["total_discount"] = $res["total_discount"] + $value["discount"];
                        }
                        if ($value["status"] == 4) {
                            $res["number_of_order_delivered"] = $res["number_of_order_delivered"] + 1;
                        } else if ($value["status"] == 5) {
                            $res["number_of_order_canceled"] = $res["number_of_order_canceled"] + 1;
                        } else {
                            $res["number_of_order_processing"] = $res["number_of_order_processing"] + 1;
                        }
                    }
                }
                if ($res["total_money"] > 0) {
                    $res["total_earned_money"] = $res["total_money"] - $res["total_discount"];
                }
                $chart_data = $this->_chartData($res);
                //order chart data
                $res["order_chart_title"] = "Thông tin bán hàng";
                $res["order_chart_sub_title"] = "Tổng số Đơn Hàng, "
                        . "Đơn Hàng đã giao, "
                        . "Đơn Hàng đã hủy, "
                        . "Đơn Hàng đang xử lý";
                $res["order_chart_data"] = $chart_data["order_chart_data"];
                //revenue chart data
                $res["revenue_chart_title"] = "Thông tin doanh số";
                $res["revenue_chart_sub_title"] = "Doanh Thu, "
                        . "Chiết khấu, "
                        . "Lợi Nhuận";
                $res["revenue_chart_data"] = $chart_data["revenue_chart_data"];
                $this->view->statistic = $res;
            }
            $this->view->post_data = $this->post_data;
            $this->view->error = $error;
        }
    }
    /**
     * 
     * @param type $data
     * @param type $f
     * @param type $t
     * @return array
     */
    private function _chartData($data) {
        $chart_data = array();
        $chart_data["order_chart_data"][] = array(
            "", 
            "Tổng số Đơn Hàng", 
            "Đơn Hàng đã giao",
            "Đơn Hàng đã hủy",
            "Đơn Hàng đang xử lý");
        $chart_data["order_chart_data"][] = array(
            "Đơn Hàng", 
            $data["number_of_order"], 
            $data["number_of_order_delivered"], 
            $data["number_of_order_canceled"], 
            $data["number_of_order_processing"]);
        
        $chart_data["revenue_chart_data"][] = array(
            "", 
            "Doanh Thu",
            "Chiếc Khấu",
            "Lợi Nhuận");
        $chart_data["revenue_chart_data"][] = array(
            "Doanh Thu", 
            array("v" => $data["total_money"], "f" => number_format($data["total_money"]).'đ'),  
            array("v" => $data["total_discount"], "f" => number_format($data["total_discount"]).'đ'),  
            array("v" => $data["total_earned_money"], "f" => number_format($data["total_earned_money"]).'đ'));
        
        return $chart_data;
    }

}
