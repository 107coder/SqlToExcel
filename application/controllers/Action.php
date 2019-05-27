<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Action extends CI_Controller{

    private static $Excel_list_name = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    public function __construct(){
        parent::__construct();
        $this->load->model('Index_model','index');
    }
    public function index(){
        $sql = $this->input->post('sql');

        p($sql);
        $status = $this->index->insert($sql);

        p($status);
    }

    /**
     * 上传SQL文件
     */
    public function uploadFile(){
        
    }
    /**
     * 处理sql文件
     */
    public function SqlFile($filename){
        
        $path = base_url("/uploads/").$filename;

        $file = fopen($path, "r") or exit("文件无法打开！");
        $sql = "";

        while(!feof($file))
        {
            $f = fgets($file);

            if($f){//获取的一行内容不为空
        
                $sql.= $f; //拼接行数据，直至遇到 ';' 组合成一条sql语句
                $b = strstr($f, ';');  //如果此行数据包含';' 
                unset($f);//銷毀一行内容
                
                if($b){ //如果凑夠一行sql，則執行
                    // echo $sql."<br>";              
                    $result = $this->db->query($sql);//執行結果var_dump為true，否則為false 
                    
                    $sql = ''; //重新拼接下一条sql语句 
                }  
    
            } 
        }
        
    }

    /**
     * 从数据库中读出数据
     */
    public function Output($dbname,$excel_name){
        $this->load->library('PHPExcel');   //载入PHPExcel类

        $result = $this->db->get($dbname)->result_array();   //获取数据库所有的数据
        $list_array = $this->db->list_fields($dbname);           //获取数据库所有的列属性 用来做Excel的表头
        $list_size = sizeof($list_array);                    //数据列数

        //这里留一个接口，随后改成使用Redis实现

        set_time_limit(0);//解除php執行脚本30秒限制
        $dir = dirname(__FILE__);      //找到当前脚本所在的位置
		$objPHPExcel = new PHPExcel(); //实例化PHPExcel类 等同于在桌面上新建一个Excel文件
		$objSheet = $objPHPExcel->getActiveSheet(); //获取当前活动sheet的操作对象
		$objSheet->setTitle('sheet');	//给当前活动sheet设置名称
        $row_number = 1;
        foreach($list_array as $k => $v){
            $objSheet->setCellValue(self::$Excel_list_name[$k].$row_number,$v);   //设置表格的title，默认使用数据库的表头
        }
		foreach($result as $value){
			// $res = $redis->get('result:'.$v['Partner_Country_Code'].':'.$v['Year']);
            // if(!$redis->sismember('result_set','result:'.$v['Partner_Country_Code'].':'.$v['Year'])) continue;
            // if($value['isFinished'] == 0) continue;
            $row_number = $row_number+1;
            $k = 0;
            foreach($value as $v){
                $objSheet->setCellValue(self::$Excel_list_name[$k].$row_number,$v);   //向表中写数据
                $k++;
            }
        }
		$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007'); //生成Excel文件


        $this->browser_export('Excel7',$excel_name);
		$objWriter->save('php://output');
    }


    /**
     * @description: 在浏览器端输出表格
     * @param {type} 
     * @return: 
     */
    function browser_export($type,$filename)
    {

        if($type == "Excel5"){
            header('Content-Type: application/vnd.ms-excel');   //告诉浏览器要输出Excel03文件
        }else{
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');	//告诉浏览器要输出Excel07文件
        }
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //告诉浏览器输出文件的名称
        header('Cache-Control: max-age=0');  // 禁止缓存 
    }

    /**
     * 只是测试
     */
    public function test(){
        //检测读取SQL文件的功能
        $filename = "student.sql";
        // $this->SqlFile($filename);

        /**
         * 检测输出SQL的文件的功能
         */
        $dbname = "student";
        $excel_name = "student.xlsx";
        $this->Output($dbname,$excel_name);

        /**
         * 获取数据库的列
         */
        // $result = $this->db->list_fields($dbname);
        // p(sizeof($result));
    }
}