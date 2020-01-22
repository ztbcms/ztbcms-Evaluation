<?php
/**
 * User: jayinton
 * Date: 2019/12/20
 * Time: 17:57
 */

namespace Evaluation\Service;


use Evaluation\BehaviorParam\UpdateEvaluationSummaryBehaviorParam;
use System\Service\BaseService;
use Think\Hook;

class EvaluationService extends BaseService
{
    /**
     * 根据ID获取
     *
     * @param $id
     *
     * @return array
     */
    static function getById($id)
    {
        $res = self::find('Evaluation/EvaluationContent', ['id' => $id]);
        $res['data']['images'] = json_decode($res['data']['images'], 1);
        if (empty($res['data']['images'])) {
            $res['data']['images'] = [];
        }

        return $res;
    }


    /**
     * 获取列表
     *
     * @param array  $where
     * @param string $order
     * @param int    $page
     * @param int    $limit
     * @param bool   $isRelation
     *
     * @return array
     */
    static function getList($where = [], $order = '', $page = 1, $limit = 20, $isRelation = false)
    {
        $res = self::select('Evaluation/EvaluationContent', $where, $order, $page, $limit, $isRelation);
        foreach ($res['data']['items'] as &$item) {
            $item['images'] = json_decode($item['images'], 1);
            if (empty($item['images'])) {
                $item['images'] = [];
            }
        }
        return $res;
    }

    /**
     * 添加
     *
     * @param array $data
     *
     * @return array
     */
    static function createItem($data = [])
    {
        if (is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        $data['create_time'] = time();
        $data['update_time'] = time();
        $res = self::create('Evaluation/EvaluationContent', $data);
        if (!$res) {
            return self::createReturn(false, null, '操作失败');
        }
        $evaluationId = $res['data'];
        if (!$res) {
            return self::createReturn(false, null, '操作失败');
        }
        $total_content = 0;
        $average_rate = 5;
        //更新统计
        $res = self::updateEvaluationSummary($data['target'], $data['target_type']);

        if($res['status']){
            $total_content = $res['data']['total_content'];
            $average_rate = $res['data']['average_rate'];
        }

        return self::createReturn(true, [
            'evaluation_id' => $evaluationId,
            'total_content' => $total_content,
            'average_rate'  => $average_rate,
        ], '操作成功');
    }

    /**
     * 更新
     *
     * @param       $id
     * @param array $data
     *
     * @return array
     */
    static function updateItem($id, $data = [])
    {
        if (is_array($data['images'])) {
            $data['images'] = json_encode($data['images']);
        }
        $item = D('Evaluation/EvaluationContent')->where(['id' => $id])->find();
        if(!$item){
            return self::createReturn(false, null , '找不到信息');
        }
        $data['update_time'] = time();
        $res =  self::update('Evaluation/EvaluationContent', ['id' => $id], $data);
        if (isset($data['review_status'])) {
            //更新统计
            self::updateEvaluationSummary($item['target'], $item['target_type']);
        }
        return $res;
    }

    /**
     * 删除
     *
     * @param $id
     *
     * @return array
     */
    static function deleteItem($id)
    {
        return self::delete('Evaluation/EvaluationContent', ['id' => $id]);
    }

    /**
     * 更新统计
     * @param $target
     * @param $target_type
     *
     * @return array
     */
    static function updateEvaluationSummary($target, $target_type)
    {
        $where = [
            'target'        => $target,
            'target_type'   => $target_type,
            'review_status' => 1,
        ];
        $total_content = M('evaluation_content')->where($where)->count();
        $total_rate = M('evaluation_content')->where($where)->sum('rate');
        $average_rate = 5;
        if ($total_content != 0) {
            $average_rate = $total_rate / $total_content;
        }
        $average_rate = round($average_rate, 1);
        //统计
        $evaluation_summary = M('evaluation_summary')->where([
            'target'      => $target,
            'target_type' => $target_type,
        ])->find();
        if ($evaluation_summary) {
            $res = M('evaluation_summary')->where([
                'id' => $evaluation_summary['id']
            ])->save([
                'total_content' => $total_content,
                'average_rate'  => $average_rate,
                'update_time'   => time(),
            ]);
        } else {
            $res = M('evaluation_summary')->add([
                'target'        => $target,
                'target_type'   => $target_type,
                'total_content' => $total_content,
                'average_rate'  => $average_rate,
                'update_time'   => time(),
            ]);
        }

        if ($res) {
            Hook::listen('evaluation_summary_updated', UpdateEvaluationSummaryBehaviorParam::create([
                'target'        => $target,
                'target_type'   => $target_type,
            ]));
            return self::createReturn(true, [
                'target'        => $target,
                'target_type'   => $target_type,
                'total_content' => $total_content,
                'average_rate'  => $average_rate,
            ]);
        } else {
            return self::createReturn(false, null, '操作失败');
        }
    }

    //获取评论统计
    static function getEvaluationSummaryByTarget($target, $target_type)
    {
        $summary = M('evaluation_summary')->where([
            'target'      => $target,
            'target_type' => $target_type,
        ])->find();

        return self::createReturn(true, [
            'total_content' => isset($summary['total_content'])
                ? intval($summary['total_content']) : 0,
            'average_rate'  => isset($summary['average_rate'])
                ? floatval($summary['average_rate']) : 5,
        ]);
    }
}