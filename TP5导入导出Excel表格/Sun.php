<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: 孙洪飞
 * Date: 2017/7/31
 * Time: 10:27
 */
use think\Db;
use app\admin\controller\Base;
class Sun
{

    //导入
    public  $imporpExcelPath ='./test.xlsx'; //导入的excel表格所在路径 一般是先经过上传文件处理后产生的路径  注意不能以/开头 如果有 要去掉
    public  $importColumnList = ['A'=>'user_phone','B'=>'user_name','C'=>'user_sex','D'=>'user_city','E'=>'user_account']; //需要导入的excel表头对应mysql字段名


    //导出
    public  $exportData = [];//需要导出的数据
    public  $excelTableName = '测试导出表格';
    public  $excelColumnKeyAndVlue = [['A','ID','shop_id','25'],['B','店名','shop_name','25'],['C','电话','shop_phone','25'],['D','商家地址','shop_address','25']];
    public  $headlist = ['ID','店名','电话','商家地址'];//头部

    //导出函数
    public function export(){
        //$res = sql语句;//获取数据源
        //$this->excelTableName ='测试导出表格';//导出文件名
        //$this->exportData = $res;//数据
        $this->excel();
    }
    //导入函数 返回数组
    public function import(){
        //上传文件 返回文件名
        //$path = upload('file');
        //$this->imporpExcelPath = $path;//赋值文件路径
        //$data = $this->getExcelData();
        //sql操作

        return $this->getExcelData();
    }


    /**生成excel表导出
     * @throws \PHPExcel_Reader_Exception
     */
    public function excel()
    {
        //引入中心类文件
        include_once EXTEND_PATH.'PHPExcel/Classes/PHPExcel.php';//phpexcel在extend下

        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($this->excelTableName);//给当前活动sheet设置名称

        //循环给表格每一行赋值
        //赋值第一行表头 并设置宽度
        foreach ($this->excelColumnKeyAndVlue as $k=>$v){
            //设置excel表标题
            $PHPSheet->setCellValue($this->excelColumnKeyAndVlue[$k][0].'1',$this->excelColumnKeyAndVlue[$k][1]);
            $PHPSheet->getColumnDimension($this->excelColumnKeyAndVlue[$k][0])->setWidth(25);

        }
        //赋值数据
        foreach ($this->exportData as $k=>$v){

            $c = $k+2;
            foreach ($this->excelColumnKeyAndVlue as $k2=>$v2){

                $PHPSheet->setCellValue($v2[0].$c,$v[$v2[2]]);//表格数据
            }

        }

        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header("Content-Disposition: attachment;filename='".$this->excelTableName.".xlsx'");//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

    }
    /**
     * 导出csv
     * csv文件同txt文件 内存消耗小 可一次导出大量数据
     */
    function csv_export() {

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$this->excelTableName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        //输出Excel列名信息
        foreach ($this->headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;

        //逐行取出数据，不浪费内存
        $count = count($this->exportData);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $this->exportData[$i];
            foreach ($row as $key => $value) {
                $row[$key] = iconv('utf-8', 'gbk', $value);
            }

            fputcsv($fp, $row);
        }
    }

    /**
     * 读取excel数据 返回数组
     * @param $path   需要读取的excel表格所在路径
     * @return array|null
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function getExcelData()
    {
        //后缀
        $extension = strtolower( pathinfo($this->imporpExcelPath, PATHINFO_EXTENSION) );

        if ($extension =='xlsx') {
            vendor('PHPExcel.PHPExcel.Reader.Excel2007');

            $PHPReader = new \PHPExcel_Reader_Excel2007();
        } else if ($extension =='xls') {
            //实例化PHPExcel类
            $PHPReader = new \PHPExcel_Reader_Excel5();

        }

        //读取excel文件内容
        $PHPExcel = $PHPReader->load($this->imporpExcelPath);

        /**读取excel文件中的第一个工作表*/
        $currentSheet = $PHPExcel->getSheet(0);

        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();

        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        /**从第二行开始输出，因为excel表中第一行为列名*/
        $arr = [];
        /**从第二行开始**/
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
            /**从第A列开始输出*/
            for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++) {
                /**ord()将字符转为十进制数*/
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();//按行列取出值

                //遍历excel表头和mysql字段对应关系数组
                foreach ($this->importColumnList as $k=>$column){

                    if($currentColumn == $k){

                       $newdata[$column] = $val;
                    }
                }
            }

            if(isset($newdata) && $newdata != ''){
                $arr[] = $newdata;
            }

            $newdata = '';
        }
        return $arr;
    }
}