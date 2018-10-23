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
use think\Image;


/**
 * 文件处理逻辑
 */
class File extends BaseLogic
{

    /**
     * 图片上传
     * small,medium,big
     */
    public function picUpload($name = 'file', $thumb_config = ['small' => 100, 'medium' => 500, 'big' => 1000])
    {

        $object_info = request()->file($name);

        $sha1  = $object_info->hash();

        $object = $object_info->move(UPLOAD_PATH);

        $save_name = $object->getSaveName();

        $save_path = UPLOAD_PATH . $save_name;

        $picture_dir_name = substr($save_name, 0, strrpos($save_name, DS));

        $filename = $object->getFilename();

        $thumb_dir_path = UPLOAD_PATH . $picture_dir_name . DS . 'thumb';

        !file_exists($thumb_dir_path) && @mkdir($thumb_dir_path, 0777, true);

        Image::open($save_path)->thumb($thumb_config['small']   , $thumb_config['small'])->save($thumb_dir_path  . DS . 'small_'  . $filename);
        Image::open($save_path)->thumb($thumb_config['medium']  , $thumb_config['medium'])->save($thumb_dir_path . DS . 'medium_' . $filename);
        Image::open($save_path)->thumb($thumb_config['big']     , $thumb_config['big'])->save($thumb_dir_path    . DS . 'big_'    . $filename);

        $data = ['name' => $filename, 'src' =>  '/uploads/'.$picture_dir_name. DS . $filename, 'sha1' => $sha1];


        unset($object);

       return [ CodeEnum::ERROR,'',$data];
    }


    /**
     * 获取指定目录下的所有文件
     * @param null $path
     * @return array
     */
    public function getFileByPath($path = null)
    {
        $dirs = new \FilesystemIterator($path);
        $arr = [];
        foreach ($dirs as $v)
        {
            if($v->isdir())
            {
                $_arr = $this->getFileByPath($path ."/". $v->getFilename());
                $arr = array_merge($arr,$_arr);
            }else{
                $arr[] = $path . "/" . $v->getFilename();
            }
        }
        return $arr;
    }

    /**
     * 上传公钥
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function uploadRsaPublicKey(){

    }
}