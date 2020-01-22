<?php
/**
 * User: jayinton
 * Date: 2020/1/22
 * Time: 16:51
 */

namespace Evaluation\Behavior;


use Common\Behavior\BaseBehavior;
use Evaluation\BehaviorParam\UpdateEvaluationSummaryBehaviorParam;

class UpdateEvaluationSummaryBehavior extends BaseBehavior
{
    /**
     * @param  UpdateEvaluationSummaryBehaviorParam  $param
     */
    public function run(&$param)
    {
        if($param->target_type == 'product_id'){
            $summary = M('evaluation_summary')->where([
                'target'      => $param->target,
                'target_type' => $param->target_type,
            ])->find();
            M('ticketing_product')->where(['id' => $param->target])->save([
                'evaluate_amount'=> $summary['total_content'],
                'average_evaluate_rate'=> $summary['average_rate'],
            ]);
        }
    }

}