/**
 * 小程序蓝牙封装
 * 当前为支付宝版本 微信版改方法名称及命令发送解析方法即可
 * 先判断用户系统 存入globalData的platform 安卓苹果有区别 读和写的uuid可能为同一个 看设备
 * 失败或者成功 都要关闭蓝牙模块 否则再次搜索会出现不显示已搜索到的设备情况
 * Created by 孙洪飞 2019/02/21.
 */

const UUID_IBT_SERVICES         = '';//蓝牙设备主uuid
const UUID_IBT_READ             = '';//读
const UUID_IBT_WRITE            = '';//写入
const APPLE_UUID_IBT_SERVICES   = '';//苹果用 主UUID
const APPLE_UUID_IBT_WRITE      = '';//苹果用 发送命令
const APPLE_UUID_IBT_READ       = '';//苹果用 接收返回值

class Blue {
    //this为蓝牙页面
    //构造函数**********************************************************************************/
    constructor(my, listener, global) {
        this.chonglian = true;//判断断开是否为异常断开 重连 
        this.my = my;
        this.listener = listener;//listener为调用页的this 传入调用页数据
        this.global = global;//app.globalData的数据
        this.openlock = null;//未找到设备提示方法

        this.my.offBluetoothDeviceFound();//关闭寻找新设备事件监听
        this.my.offBLEConnectionStateChanged();//关闭连接状态监听
        my.onBluetoothDeviceFound(this.onDeviceScan.bind(this))//监听寻找到新设备事件
        my.onBLEConnectionStateChanged(this.onDeviceConnectStateChange.bind(this))//监听连接状态事件
        my.onBluetoothAdapterStateChange(this.onDeviceStateChange.bind(this))//监听本机蓝牙状态
    }
    //初始化蓝牙***************************************************************************************** */
    startScan(success, fail) {
        var that = this;
        this.my.openBluetoothAdapter({
            success: res => {
                console.log('初始化蓝牙成功');
                this.my.startBluetoothDevicesDiscovery({//搜索附近蓝牙
                    services: [UUID_IBT_SERVICES],//过滤不是自己要找的 传入主uuid
                    allowDuplicatesKey: false,//是否允许重复上报同一设备
                    success: res => {
                        //20秒后搜索不到指定设备 提示未找到设备
                        var openlock1 = setTimeout(function () {
                          if (!that.global.device_id) {
                           console.log('未找到设备,停止搜索')
                            that.my.stopBluetoothDevicesDiscovery(); //停止搜索
                            that.my.offBluetoothDeviceFound();//关闭寻找新设备事件监听
                            that.my.offBLEConnectionStateChanged();//移除连接事件监听

                            that.my.showToast({
                              content: '没有找到设备,请重试',
                            });
                            return;
                          }
                        }, 20000);
                        that.openlock = openlock1;
                    },
                    fail,
                })
            },
            fail: err => {
                this.my.showToast({
                    content: '请先打开蓝牙',
                });
                return;
            },
        })
    }
    //扫描发现了设备  过滤设备(第一个监听)******************************************************************************************* */
    onDeviceScan(deviceObj) {
        console.log('发现了设备', deviceObj);
        deviceObj.devices.forEach(device => {//遍历
            //通过名字判断
            if (device.name == this.global.dv_name || device.localName == this.global.dv_name) {
                     //连接
                    console.log('停止搜索')
                    this.my.stopBluetoothDevicesDiscovery(); //停止搜索
                    this.my.offBluetoothDeviceFound();//关闭寻找新设备事件监听
                    this.connectDevice(device);
            }
        })
    }
    //连接蓝牙****************************************************************************************** */
    connectDevice(device) {
        console.log('开始连接设备')
        this.device = device;

        var that = this;
        //连接
        this.my.connectBLEDevice({
            deviceId: device.deviceId,//蓝牙的id
            success: (res) => {
                console.log("连接成功", res);

                this.my.getBLEDeviceServices({//获取蓝牙服务
                    deviceId: this.device.deviceId,
                    success: this.onGetDeviceServices.bind(this),
                    fail: err => {
                        console.log("获取蓝牙服务失败", err);
                        this.my.showToast({
                            content: '获取蓝牙服务失败',
                        });
                        return;
                    }
                })

            },
            fail: (err) => {
                console.log('蓝牙连接失败',err);
                this.my.showToast({
                    content: '蓝牙连接失败',
                });
                return;
            }
        })
    }
    //获取蓝牙服务成功后 获取蓝牙服务特征值********************************************************************************************* */
    onGetDeviceServices(char) {
        var that = this;
        var sid = this.global.platform == 'Android' ? UUID_IBT_SERVICES : APPLE_UUID_IBT_SERVICES;
        console.log('获取到服务值', char);
        console.log("获取服务成功");

        this.my.getBLEDeviceCharacteristics({//获取服务特征值
            deviceId: that.device.deviceId,
            serviceId: sid,//蓝牙服务uuid
            success: this.onGetDeviceCharacteristics.bind(this),
            fail: err => {
                this.my.showToast({
                    content: '获取特征值失败',
                });
                return;
            }
        })
    }
    //服务特征值获取成功后 启动特征值变化notify功能********************************************************************************************** */
    onGetDeviceCharacteristics(res) {
        var that = this;
        console.log("获取特征值成功,监听启动异步,发送命令")
        console.log('获取到服务特征值', res);
        var sid = this.global.platform == 'Android' ? UUID_IBT_SERVICES : APPLE_UUID_IBT_SERVICES;
        var cid = this.global.platform == 'Android' ? UUID_IBT_READ : APPLE_UUID_IBT_READ;

        //启动异步APPLE_UUID_IBT_WRITE
        this.my.notifyBLECharacteristicValueChange({
            deviceId: this.device.deviceId,
            serviceId: sid,//特征值对应服务uuid
            characteristicId: cid,//蓝牙特征值uuid
            state: true,
            success: res => {
                console.log('启动异步通知成功');
                this.my.offBLECharacteristicValueChange();//关闭返回值监听 避免重复注册监听
                this.my.onBLECharacteristicValueChange(this.onDeviceValueChange.bind(this));//监听返回值数值变化

                setTimeout(function() {
                    that.sendCmd('你要发送的命令');
                }, 500)
            },
            fail: err => {
                this.my.showToast({
                    content: '启动异步通知错误',
                });
                return;
            }
        });
    }
    //发送命令****************************************************************************/
    sendCmd(cmd) {
        var buffer = this.stringToHex(cmd);
        var sid = this.global.platform == 'Android' ? UUID_IBT_SERVICES : APPLE_UUID_IBT_SERVICES;
        var cid = this.global.platform == 'Android' ? UUID_IBT_WRITE : APPLE_UUID_IBT_WRITE;
        var that = this;

        this.my.writeBLECharacteristicValue({
            deviceId: this.device.deviceId,
            serviceId: sid,
            characteristicId: cid,//写入uuid
            value: buffer,
            success: function(res) {
                console.log('命令发送成功');
            },
            fail: function(res) {
                console.log('命令发送失败',res)
            }
        })
    }
    //关闭蓝牙设备 没有断开连接********************************************************************************************** */
    stopScan() {
        this.my.closeBluetoothAdapter({
            success : (res)=>{console.log('关闭蓝牙模块成功')},
            fail : (res)=>{console.log('关闭蓝牙模块失败')},
        })
    }
    //断开连接 关闭蓝牙模块*************************************************************************/
    closeBle() {
      var that = this;
      this.my.disconnectBLEDevice({
        deviceId : this.device.deviceId,
        success : (res)=>{
            console.log('断开连接成功')
            my.closeBluetoothAdapter({
              success : (res)=>{console.log('关闭蓝牙模块成功')},
              fail : (res)=>{console.log('关闭蓝牙模块失败')},
            })
          },
        fail : (res)=>{console.log('断开连接失败')},
      })
     
    }
//监听返回值变化********************************************************************************************** */
    onDeviceValueChange(value) {
        console.log("返回的值:", this.hexToString(value.value));

        var that = this;
        var backValue = this.hexToString(value.value);
        //后续处理逻辑
        //......
        that.chonglian = false;
        setTimeout(function () {
            that.closeBle();
        },500)
        clearTimeout(that.openlock);
        return;
    }
    //连接事件监听****************************************************************************************/
    onDeviceConnectStateChange(res){
      console.log('监听到连接事件',res);
      if (res.connected == false && this.chonglian == true) {
        //重新连接
         this.connectDevice(this.device);
      }
    }
    //蓝牙状态改变*******************************************************************************************/
    onDeviceStateChange(res){
      console.log('蓝牙状态改变');
    }
    //命令处理及解析 微信不是这个方法(文档有)******************************************************************************************** */
    //发送处理
    stringToHex(str){
　　　　var val="";
　　　　for(var i = 0; i < str.length; i++){

　　　　　　if(val == "")
　　　　　　　　val = str.charCodeAt(i).toString(16);
　　　　　　else
　　　　　　　　val += str.charCodeAt(i).toString(16);
　　　　}
　　　　return val;
　　}
    //返回处理
    hexToString(str){
      var arr = new Array();
            for (var i = 0; i < str.length/2; i++) {
                var aa = str.slice(i*2, (i+1)*2);
                arr.push(aa);
            }
      var val = "";
            for (var i = 0; i < arr.length; i++) {
                val += String.fromCharCode(parseInt(arr[i], 16));    //将分组后的16进制字符串转10进制Unicode码,然后将Unicode码转换为字符
            }
　　　　return val;
　　}
}
module.exports = Blue
//页面引用方法
// const Blue = require('../../utils/blue')
// that.blue = new Blue(my, that, app.globalData);
// that.blue.startScan(() => {
//     //开启搜索成功
// }, err => {
//     //开启搜索失败
//     console.log(err)
// })