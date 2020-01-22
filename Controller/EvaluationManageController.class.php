<?php
/**
 * User: jayinton
 * Date: 2019/12/21
 * Time: 21:17
 */

namespace Evaluation\Controller;


use Common\Controller\AdminBase;
use Evaluation\Service\EvaluationService;

class EvaluationManageController extends AdminBase
{
    function lists()
    {
        $this->display();
    }

    function edit()
    {
        $this->display();
    }

    function doEdit()
    {
        $data = I('post.');
        if (empty($data['id'])) {
            $data['create_time'] = time();
            $data['update_time'] = time();
            $res = EvaluationService::createItem($data);
        } else {
            $id = $data['id'];
            unset($data['id']);
            $data['update_time'] = time();
            $res = EvaluationService::updateItem($id, $data);
        }
        $this->ajaxReturn($res);
    }

    function doDelete()
    {
        $id = I('post.id');
        $res = EvaluationService::deleteItem($id);
        $this->ajaxReturn($res);
    }

    function getDetail()
    {
        $id = I('id');
        $res = EvaluationService::getById($id);
        $this->ajaxReturn($res);
    }

    function getList()
    {
        $review_status = I('review_status', '');
        $page = I('page', 1);
        $limit = I('limit', 15);
        $target_type = I('target_type', '');
        $where = [];
        if ($review_status !== '') {
            $where['review_status'] = $review_status;
        }
        if ($target_type !== '') {
            $where['target_type'] = $target_type;
        }
        $res = EvaluationService::getList($where, 'id desc', $page, $limit);
        $lists = $res['data']['items'];
        foreach ($lists as &$item){
            $item['create_time_date'] = date("Y-m-d H:i:s", $item['create_time']);
            $target_info = [
                'name' => '',
            ];
            if($item['target_type'] == 'shop_id'){
                $info = M('foodshop_shop')->where(['id' =>$item['target'] ])->find();
                $target_info['name'] = '【美食店铺】'.$info['name'];
            } else if($item['target_type'] == 'product_id'){
                $info = M('ticketing_product')->where(['id' =>$item['target'] ])->find();
                $target_info['name'] = '【门票产品】'.$info['product_name'];
            }
            $item['target_info'] = $target_info;
        }
        $res['data']['items'] = $lists;
        $this->ajaxReturn($res);
    }

}