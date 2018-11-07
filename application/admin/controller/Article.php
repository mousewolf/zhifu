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


namespace app\admin\controller;
use app\common\library\enum\CodeEnum;

/**
 * 文章控制器
 */
class Article extends BaseAdmin
{

    /**
     * 文章列表
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
     * 文章列表
     * @url /article/getList?page=1&limit=10&id=8&author=&title=
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList(){
        $where = [];

        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];

        !empty($this->request->param('author')) && $where['author']
            = ['like', '%'.$this->request->param('author').'%'];

        !empty($this->request->param('title')) && $where['title']
            = ['like', '%'.$this->request->param('title').'%'];

        $data = $this->logicArticle->getArticleList($where, true, 'create_time desc', false);

        $count = $this->logicArticle->getArticleCount($where);

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
            ]
        );
    }

    /**
     * 文章添加
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function add()
    {
        
        $this->articleCommon();
        
        return $this->fetch();
    }

    /**
     * 文章编辑
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function edit()
    {
        
        $this->articleCommon();
        
        $article = $this->logicArticle->getArticleInfo(['id' => $this->request->param('id')]);

        !empty($article) && $article['img_ids_array'] = str2arr($article['img_ids']);
        
        $this->assign('article', $article);
        
        return $this->fetch();
    }

    /**
     * 文章添加与编辑通用方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function articleCommon()
    {
        $this->request->isPost() && $this->result($this->logicArticle->editArticle($this->request->param()));
    }

    /**
     * 数据状态设置
     */
    public function changeStatus()
    {
        $this->result($this->logicArticle->setStatus('Article', $this->request->param()));
    }

    /**
     * 文章图片上传
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function upload(){
        $this->request->isPost() && $this->result($this->logicFile->picUpload());
    }
}
