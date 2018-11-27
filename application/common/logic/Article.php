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
use think\Db;
use think\Log;
use app\common\library\enum\CodeEnum;

class Article extends BaseLogic
{
    /**
     * 获取文章列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getArticleList($where = [], $field = true, $order = 'create_time desc',$paginate = 15)
    {
        return $this->modelArticle->getList($where, $field, $order,$paginate);
    }

    /**
     * 获取通知列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     *
     * @return mixed
     */
    public function getNoticeList($where = [], $field = true, $order = 'create_time desc',$paginate = 15)
    {
        return $this->modelNotice->getList($where, $field, $order,$paginate);
    }

    /**
     * 获取文章总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getArticleCount($where = []){
        return $this->modelArticle->getCount($where);
    }

    /**
     * 获取知总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getNoticeCount($where = []){
        return $this->modelNotice->getCount($where);
    }

    /**
     * 获取文章信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getArticleInfo($where = [], $field = true)
    {
        return $this->modelArticle->getInfo($where, $field);
    }

    /**
     * 获取通知信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getNoticeInfo($where = [], $field = true)
    {
        return $this->modelNotice->getInfo($where, $field);
    }

    /**
     * 文章信息编辑
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $data
     * @return array|string
     */
    public function saveArticle($data = [])
    {

        //数据验证
        $validate = $this->validateArticle->check($data);

        if (!$validate) {

            return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateArticle->getError()];
        }

        Db::startTrans();
        try{

            $this->modelArticle->setInfo($data);
            Db::commit();
            return [ 'code' =>  CodeEnum::SUCCESS, 'msg' => '文章编辑成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ 'code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }

    }

    /**
     * 通知信息编辑
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $data
     * @return array|string
     */
    public function saveNotice($data = [])
    {
        //数据验证
        $validate = $this->validateNotice->check($data);

        if (!$validate) {

            return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateNotice->getError()];
        }

        Db::startTrans();
        try{
            //数据处理
            if(!empty($data['attachments']))  $data['attachments'] = json_encode(array_values($data['attachments']));

            $this->modelNotice->setInfo($data);
            Db::commit();
            return [ 'code' => CodeEnum::SUCCESS, 'msg' => '通知编辑成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ 'code' => CodeEnum::ERROR, 'msg' => '未知错误'];
        }

    }

    /**
     * 文章删除
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return array|string
     */
    public function delArticle($where = [])
    {
        Db::startTrans();
        try{
            $this->modelArticle->deleteInfo($where);
            Db::commit();
            return [1,'文章删除成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [0,'未知错误'];
        }
    }

    /**
     * 改变文章状态
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $id 文章ID
     * @param int $value 状态值
     * @return array|string
     */
    public function setChannelStatus($id,$value = 0){
        Db::startTrans();
        try{
            $this->modelArticle->setFieldValue(['id'=>$id], $field = 'status', $value);
            Db::commit();
            return [1,'修改成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [0,'未知错误'];
        }
    }
}
