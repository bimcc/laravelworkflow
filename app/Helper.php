<?php
namespace App;

use App\Work\Workflow;


class Helper {
    public static function status($status)
    {
            switch ($status)
            {
                case 0:
                    return '<span class="label radius">保存中</span>';
                    break;
                case 1:
                    return '<span class="label radius" >流程中</span>';
                    break;
                case 2:
                    return '<span class="label label-success radius" >审核通过</span>';
                    break;
                default: //-1
                    return '<span class="label label-danger radius" >退回修改</span>';
            }
    }

    public static function btn($wf_fid,$wf_type,$status)
    {
        $work = new Workflow();
        $url = "/wf/wfcheck?wf_type={$wf_type}&wf_title=2&wf_fid={$wf_fid}";
        $url_star = "/wf/wfstart?wf_type={$wf_type}&wf_title=2&wf_fid={$wf_fid}";
        switch ($status)
        {
            case 0:
                return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"'.$url_star.'","450","350")>发起工作流</span>';
                break;
            case 1:
                    $st = 0;
                    $flowinfo =  $work->workflowInfo($wf_fid,$wf_type,['uid'=>session('uid'),'role'=>session('role')]);

            if($flowinfo!=-1){
                    $user = explode(",", $flowinfo['status']['sponsor_ids']);
                    if($flowinfo['sing_st']==0){
                            if($flowinfo['status']['auto_person']==3||$flowinfo['status']['auto_person']==4){
                                    if (in_array(session('uid'), $user)) {
                                            $st = 1;
                                    }
                            }
                            if($flowinfo['status']['auto_person']==5){
                                    if (in_array(session('role'), $user)) {
                                            $st = 1;
                                    }
                            }
                    }else{
                        if($flowinfo['sing_info']['uid']==session('id')){
                                $st = 1;
                        }
                    }
                }else{
                        return '<span class="btn  radius size-S">无权限</span>';
                }	
                if($st == 1){
                        return "<span class='btn  radius size-S' onclick=layer_show('审核','{$url}','850','650')>审核</span>";
                }else{
                        return '<span class="btn  radius size-S">无权限</span>';
                }

                case 100:
                        echo "<span class='btn btn-primary' onclick=layer_show('代审','{$url}&sup=1','850','650')>代审</span>";
                break;

                break;
                default:
                return '';
        }
    }
}