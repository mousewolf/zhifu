<?php

/**
 *  +----------------------------------------------------------------------
 *  | 草帽支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\common\logic;

use app\common\library\enum\CodeEnum;
use think\Db;
use think\Log;

class Api extends BaseLogic
{

    /**
     * 获取所有支持的商户请求识标
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function getAppKeyMap(){
        return $this->modelApi->getColumn([], 'id,key', $key = 'id');
    }

    /**
     * 获取商户API列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param array|bool $field
     * @param string $order
     * @param int|bool $paginate
     * @return mixed
     */
    public function getApiList($where, $field = true, $order = 'create_time', $paginate = 15){
        return $this->modelApi->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取商户API总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getApiCount($where = []){
        return $this->modelApi->getCount($where);
    }

    /**
     *
     * 获取商户支持API
     *
     * @author 勇敢的小笨羊
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getApiInfo($where = [], $field = true){
        return $this->modelApi->getInfo($where, $field);
    }

    /**
     * 编辑商户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array
     */
    public function editApi($data){

        //TODO  验证数据
        $validate = $this->validateApi->scene('edit')->check($data);

        if (!$validate) {

            return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateApi->getError()];
        }
        //TODO 修改数据
        Db::startTrans();
        try{
            //加密KEY
            $data['key']    = data_md5_key($data['secretkey']);
            //应该写入文件  文件名为key 内容为pem内容
            $this->saveRsaPublickKey($data);
            //提交保存
            $this->modelApi->setInfo($data);
            Db::commit();
            return [ 'code' => CodeEnum::SUCCESS, 'msg' => '编辑成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ 'code' => CodeEnum::ERROR , 'msg' => '未知错误'];
        }
    }

    /**
     * 保存上传的key
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     */
    public function saveRsaPublickKey($data){
        $pem = chunk_split($data['secretkey'],64,"\n");//转换为pem格式的公钥
        $content = "-----BEGIN PUBLIC KEY-----".PHP_EOL
            .$pem."-----END PUBLIC KEY-----".PHP_EOL;
        //return [ CodeEnum::SUCCESS ,CRET_PATH.$data['key'],$content];
        if (!is_dir(CRET_PATH . $data['key'])) mkdir(CRET_PATH . $data['key'], 0777);
        file_put_contents(CRET_PATH."{$data['key']}/rsa_public_key.pem",$content);
    }
}