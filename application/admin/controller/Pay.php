<?php
/**
 * +---------------------------------------------------------------------+
 * | Yubei         | [ WE CAN DO IT JUST THINK ]
 * +---------------------------------------------------------------------+
 * | Licensed    | http://www.apache.org/licenses/LICENSE-2.0 )
 * +---------------------------------------------------------------------+
 * | Author       | Brian Waring <BrianWaring98@gmail.com>
 * +---------------------------------------------------------------------+
 * | Company   | 小红帽科技      <Iredcap. Inc.>
 * +---------------------------------------------------------------------+
 * | Repository | https://github.com/BrianWaring/Yubei
 * +---------------------------------------------------------------------+
 */

namespace app\admin\controller;


use app\common\library\enum\CodeEnum;

class Pay extends BaseAdmin
{
    /**
     * 支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function channel(){
        return $this->fetch();
    }

    /**
     * 银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function bank(){
        return $this->fetch();
    }

    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getCodeList(){

        $where = [];
        //code
        !empty($this->request->param('code')) && $where['code']
            = ['eq', $this->request->param('code')];
        //name
        !empty($this->request->param('name')) && $where['name']
            = ['like', '%'.$this->request->param('name').'%'];

        $data = $this->logicPay->getCodeList($where, true, 'create_time desc', false);

        $count = $this->logicPay->getCodeCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getChannelList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('keywords')) && $where['id|name']
            = ['like', '%'.$this->request->param('keywords').'%'];

        $data = $this->logicPay->getChannelList($where,true, 'create_time desc',false);

        $count = $this->logicPay->getChannelCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getBankList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('keywords')) && $where['id|name']
            = ['like', '%'.$this->request->param('keywords').'%'];

        $data = $this->logicBank->getBankerList($where,true, 'create_time desc',false);

        $count = $this->logicBank->getBankerCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 新增支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addChannel()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->addChannel($this->request->post()));
        //交易方式列表
        $this->assign('code',$this->logicPay->getCodeList([]));

        return $this->fetch();
    }

    /**
     * 新增支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addCode()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->addCode($this->request->post()));

        return $this->fetch();
    }

    /**
     * 新增支付银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addBank()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicBank->saveBankInfo($this->request->post()));

        return $this->fetch();
    }

    /**
     * 编辑支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->editChannel($this->request->post()));
        //交易方式列表
        $this->assign('code',$this->logicPay->getCodeList([]));
        //获取渠道详细信息
        $this->assign('channel',$this->logicPay->getChannelInfo(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }

    /**
     * 编辑支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editCode(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->editCode($this->request->post()));
        //获取支付方式详细信息
        $this->assign('code',$this->logicPay->getCodeInfo(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }

    /**
     * 编辑支付银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editBank(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicBank->savelogicBank->saveBankerInfo($this->request->post()));
        //获取支付方式详细信息
        $this->assign('bank',$this->logicBank->getBankerInfo(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }

    /**
     * 删除渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function delChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->delChannel(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }
}