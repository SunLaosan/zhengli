<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: 品花
 * Date: 2017/7/31
 * Time: 10:27
 */
use think\Db;
use app\admin\controller\Base;
class Sun //表格导出!!!
{

    //导入
    public  $imporpExcelPath ='./expressTeml (9).xlsx'; //导入的excel表格所在路径
    public  $importColumnList = ['A'=>'user_phone','B'=>'user_name','C'=>'user_sex','D'=>'user_city','E'=>'user_alipay_account']; //需要导入的excel表头对应mysql字段名


    //导出
    public  $exportData = [];//需要导出的数据
    public  $excelTableName = '测试导出表格';
    public  $excelColumnKeyAndVlue = [['A','ID','shop_id','25'],['B','店名','shop_name','25'],['C','电话','shop_phone','25'],['D','商家地址','shop_address','25']];

    public function export(){
        $res =   Db::table('dy_shop')->field('shop_id,shop_name,shop_phone,shop_address')->select();
        $this->excelTableName ='测试导出表格';
        $this->exportData = $res;
        $this->excel();
    }

    public function import(){

        dump($this->getExcelData());
    }


    /**生成excel表导出
     * @param $data 需要导出的数据
     * @throws \PHPExcel_Reader_Exception
     */
    public function excel()
    {

        include_once EXTEND_PATH.'PHPExcel/Classes/PHPExcel.php';

        $PHPExcel = new \PHPExcel();//实例化
        $PHPSheet = $PHPExcel->getActiveSheet();
        $PHPSheet->setTitle($this->excelTableName);//给当前活动sheet设置名称

        foreach ($this->excelColumnKeyAndVlue as $k=>$v){
            //设置excel表标题
            $PHPSheet->setCellValue($this->excelColumnKeyAndVlue[$k][0].'1',$this->excelColumnKeyAndVlue[$k][1]);
            $PHPSheet->getColumnDimension($this->excelColumnKeyAndVlue[$k][0])->setWidth(25);

        }
        foreach ($this->exportData as $k=>$v){

            $c = $k+2;
            foreach ($this->excelColumnKeyAndVlue as $k2=>$v2){

                $PHPSheet->setCellValue($v2[0].$c,$v[$v2[2]]);//表格数据
            }

        }

//        $PHPSheet->setCellValue("A".(count($this->exportData)+5),"1.物流公司的名称，请使用模板中列出的标准命名方式，否则将会影响订单中物流信息显示。");
//        $PHPSheet->setCellValue("A".(count($this->exportData)+6),"2.不需要的物流公司的一整行都可以删除");
//        $PHPSheet->setCellValue("A".(count($this->exportData)+7),"3.您修改的地方是订单编号，和物流单号(填写单号时候要对应物流名称)");
//        $PHPSheet->setCellValue("A".(count($this->exportData)+8),"4.这四行文字说明可以不用动");




        $PHPWriter = \PHPExcel_IOFactory::createWriter($PHPExcel,"Excel2007");//创建生成的格式
        header("Content-Disposition: attachment;filename='".$this->excelTableName.".xlsx'");//下载下来的表格名
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //表示在$path路径下面生成demo.xlsx文件

    }


    /**
     * 读取excel数据
     * @param $path   需要读取的excel表格所在路径
     * @return array|null
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function getExcelData(){

        include_once EXTEND_PATH.'PHPExcel/Classes/PHPExcel.php';

        $PHPExcel = new \PHPExcel();

        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new \PHPExcel_Reader_Excel2007();
        if(!$PHPReader->canRead($this->imporpExcelPath)){
            $PHPReader = new \PHPExcel_Reader_Excel5();
            if(!$PHPReader->canRead($this->imporpExcelPath)){
//                echo 'no Excel';
                return null;
            }
        }

        $PHPExcel = $PHPReader->load($this->imporpExcelPath);
        /**读取excel文件中的第一个工作表*/

        $currentSheet = $PHPExcel->getSheet(0);
        /**取得最大的列号*/
        $allColumn = $currentSheet->getHighestColumn();
        /**取得一共有多少行*/
        $allRow = $currentSheet->getHighestRow();

        /**从第二行开始输出，因为excel表中第一行为列名*/

        $arr = [];
        for($currentRow = 2;$currentRow <= $allRow;$currentRow++){
            /**从第A列开始输出*/

            for($currentColumn= 'A';$currentColumn<= $allColumn; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();
                /**ord()将字符转为十进制数*/

//                if(empty($val)){
////                        continue;
//                    break;
//                }



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