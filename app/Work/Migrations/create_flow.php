<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 工作流表
        Schema::create('flow', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->comment('流程类别')->nullable();
            $table->string('flow_name')->comment('流程名称');
            $table->string('flow_desc')->comment('流程描述');
            $table->mediumInteger('sort_order')->comment('排序');
            $table->tinyInteger('status')->comment('0 不可用 1 可用');
            $table->integer('uid')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        // 工作流过程表
        Schema::create('flow_process', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('flow_id')->comment('流程ID');
            $table->string('process_name')->comment('步骤名称')->default('步骤');
            $table->char('process_type',10)->comment('步骤类型')->default('');
            $table->string('process_to')->comment('转交下一步骤号')->default('');
            $table->integer('child_id')->comment('is_child子流程id有return_step_to结束后继续父流程下一步')->default(0);
            $table->text('child_relation')->comment('[保留功能]父子流程字段映射关系')->nullable();
            $table->tinyInteger('child_after',1)->comment('子流程 结束后动作 0 结束并更新福流程节点为结束 1 结束并返回福流程步骤 ')->default(0);
            $table->integer('child_back_process')->comment('子流程结束返回的步骤id')->default(0);
            $table->text('return_sponsor_ids')->comment('[保留功能]主办人 子流程结束后下一步的主办人')->nullable();
            $table->text('return_respon_ids')->comment('[保留功能]经办人 子流程结束后下一步的经办人')->nullable();
            $table->text('write_fields')->comment('这个步骤可写的字段')->nullable();
            $table->text('secret_fields')->comment('这个步骤隐藏的字段')->nullable();
            $table->text('lock_fields')->comment('锁定不能更改宏空间的值')->nullable();
            $table->text('check_fields')->comment('字段验证规则')->nullable();
            $table->tinyInteger('auto_person',1)->comment('本步骤的自动选主办人规则,0:不自动选择1:流程发起人2:本部门主管3:指定默认人4:上级主管领导5:一级部门主管6:指定步骤主办人');
            $table->tinyInteger('auto_unlock',1)->comment('是否允许修改主办人auto_type>0 0不允许 1允许（默认）');
            $table->string('auto_sponsor_ids')->comment('3指定步骤主办人ids');
            $table->string('auto_sponsor_text')->comment('3指定步骤主办人text');
            $table->string('auto_respon_ids')->comment('3指定步骤主办人ids');
            $table->string('auto_respon_text')->comment('3指定步骤主办人text');
            $table->string('auto_role_ids')->comment('制定默认角色ids');
            $table->string('auto_role_text')->comment('制定默认角色 text');
            $table->smallInteger('auto_process_sponsor')->comment('[保留功能]指定其中一个步骤的主办人处理');
            $table->text('range_user_ids')->comment('本步骤的经办人授权范围ids');
            $table->text('range_user_text')->comment('本步骤的经办人授权范围text');
            $table->text('range_dept_ids')->comment('本步骤的经办部门授权范围');
            $table->text('range_dept_text')->comment('本步骤的经办部门授权范围text');
            $table->text('range_role_ids')->comment('本步骤的经办角色授权范围ids');
            $table->text('range_role_text')->comment('本步骤的经办角色授权范围text');
            $table->tinyInteger('receive_type',1)->comment('0明确指定主办人1先接收者为主办人');
            $table->tinyInteger('is_user_end',1)->comment('允许主办人在非最后步骤也可以办结流程');
            $table->tinyInteger('is_userop_pass',1)->comment('经办人可以转交下一步');
            $table->tinyInteger('is_sing',1)->comment('会签选项0禁止会签1允许会签（默认） 2强制会签');
            $table->tinyInteger('sign_look',1)->comment('会签可见性0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见');
            $table->tinyInteger('is_back',1)->comment('是否允许回退0不允许（默认） 1允许退回上一步2允许退回之前步骤');
            $table->text('out_condition')->comment('转出条件');
            $table->smallInteger('setleft',5)->comment('左 坐标')->default(100);
            $table->smallInteger('settop')->comment('上 坐标')->default(100);
            $table->text('style')->comment('样式 序列化 ');
            $table->integer('dateline')->default(0);
            $table->integer('wf_mode')->comment('0 单一线性，1，转出条件 2，同步模式 ')->default(0);
            $table->integer('wf_action')->comment('对应方法')->default('view');            

            $table->softDeletes();
            $table->timestamps();
        });

        // 表单
        Schema::create('form', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('表单名称');
            $table->string('name')->comment('表名');
            $table->string('file')->comment('generate file');
            $table->integer('menu')->default(0);
            $table->integer('flow')->default(0);
            $table->longText('ziduan');
            $table->integer('uid');
            $table->integer('status');

            $table->softDeletes();
            $table->timestamps();
        });

        // 表单方法表
        Schema::create('form_function', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fid')->default(0);
            $table->longText('sql')->nullable();
            $table->string('name');
            $table->integer('uid');

            $table->softDeletes();
            $table->timestamps();
        });

        // menu
        Schema::create('menu',function(Blueprint $table){
            $table->increments('id');
            $table->string('url')->nullable();
            $table->string('name');
            $table->integer('uid');

            $table->softDeletes();
            $table->timestamps();
        });

        // news
        Schema::create('news',function(Blueprint $table){
            $table->increments('id');
            $table->integer('uid');
            $table->string('news_title');
            $table->integer('news_type');
            $table->integer('news_top')->default(0)->comment('是否置顶');
            $table->longText('news_context')->nullable();
            $table->string('news_user');
            $table->integer('status')->comment('-1回退修改0 保存中1流程中 2通过')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
        // news_type        
        Schema::create('menu',function(Blueprint $table){
            $table->increments('id');
            $table->string('type');
            $table->integer('uid');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('role',function(Blueprint $table){
            $table->increments('id');
            $table->string('name')->comment('后台组名');
            $table->integer('pid')->comment('父级id');
            $table->tinyInteger('status',1)->comment('是否激活 1：是 0：否');
            $table->integer('sort')->comment('排序权重')->default(0);
            $table->string('remark')->comment('备注说明')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('role_user',function(Blueprint $table){
            $table->integer('user_id');
            $table->integer('role_id');
        });

        Schema::create('run',function(Blueprint $table){
            $table->increments('id');
            $table->integer('pid')->comment('work_run父流转公文ID 值大于0则这个是子流程，完成后或者要返回父流程')->default(0);
            $table->string('from_table')->comment('单据表，不带前缀')->nullable();
            $table->integer('from_id')->nullable();
            $table->smallInteger('pid_flow_step',5)->comment('父pid的flow_id中的第几步骤进入的,取回这个work_flow_step的child_over决定结束子流程的动作')->default(0);
            $table->integer('cache_run_id')->comment('多个子流程时pid无法识别cache所以加这个字段pid>0')->default(0);
            $table->integer('uid')->defalult(0);
            $table->integer('flow_id')->comment('流程id 正常流程')->default(0);
            $table->integer('cat_id')->comment('流程分类ID即公文分类ID')->default(0);
            $table->string('run_name')->comment('公文名称')->default('');
            $table->integer('run_flow_id')->comment('流转到什么流程 最新流程，查询优化，进入子流程时将简化查询，子流程与父流程同步')->default(0);
            $table->string('run_flow_process')->comment('流转到第几步')->nullable();
            $table->string('att_ids')->comment('公文附件ids')->default('');
            $table->integer('end_time')->comment('结束时间')->default(0);
            $table->integer('status')->comment('状态，0流程中，1通过,2回退')->default(0);
            $table->integer('dateline')->default(0);
            $table->integer('is_sing')->default(0);
            $table->integer('sing_id')->nullable(); 

            $table->softDeletes();
            $table->timestamps();

        });

        Schema::create('run_cache',function(Blueprint $table){
            $table->increments('id');
            $table->integer('run_id')->comment(' 缓存run工作的全部流程模板步骤等信息,确保修改流程后工作依然不变');
            $table->integer('form_id')->default(0);
            $table->integer('flow_id')->default(0);
            $table->text('run_form')->comment('模板信息');
            $table->text('run_flow')->comment('流程信息');
            $table->text('run_flow_process')->comment('流程步骤信息');
            $table->integer('dateline')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('run_log',function(Blueprint $table){
            $table->increments('id');
            $table->integer('uid')->default(0);
            $table->integer('from_id')->default(0);
            $table->string('from_table')->default('');
            $table->integer('run_id')->comment('流转id')->default(0);
            $table->integer('run_flow')->comment('流程ID,子流程时区分run step')->default(0);
            $table->text('content')->comment('日志内容')->nullable();
            $table->integer('dateline')->default(0);
            $table->string('btn')->nullable();
            $table->longText('art');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('run_process',function(Blueprint $table){
            $table->increments('id');
            $table->integer('uid')->default(0);
            $table->integer('run_id')->comment('当前流转id')->default(0);
            $table->integer('run_flow')->comment('属于哪个流程的id')->default(0);
            $table->smallInteger('run_flow_process',5)->comment('当前步骤编号')->default(0);
            $table->integer('parent_flow')->comment('上一步流程')->default(0);
            $table->smallInteger('parent_flow_process')->comment('上一步骤号')->default(0);
            $table->integer('run_child')->comment('开始转入子流程run_id 如果转入子流程，则在这里也记录')->default(0);
            $table->text('remark')->comment('备注')->default('');
            $table->tinyInteger('is_receive_type')->comment('是否先接收人为主办人')->default(0);
            $table->tinyInteger('auto_person',4)->comment('')->nullable();
            $table->string('sponsor_text')->nullable();
            $table->string('sponsor_ids')->nullable();
            $table->tinyInteger('is_sponsor',1)->default(0)->comment('是否步骤主办人 0否(默认) 1是');
            $table->tinyInteger('is_singpost',1)->default(0)->comment('是否已会签过');
            $table->tinyInteger('is_back',1)->comment('被退回的 0否(默认) 1是')->default(0);
            $table->tinyInteger('status',1)->comment('状态 0为未接收（默认），1为办理中 ,2为已转交,3为已结束4为已打回')->default(0);
            $table->integer('js_time')->comment('接收时间')->default(0);
            $table->integer('bl_time')->comment('办理时间')->default(0);
            $table->integer('dateline')->default(0);
            $table->integer('wf_mode')->comment('')->nullable();
            $table->string('wf_action')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('run_sign',function(Blueprint $table){
            $table->increments('id');
            $table->integer('uid')->default(0);
            $table->integer('run_id')->default(0);
            $table->integer('run_flow')->default(0);
            $table->smallInteger('run_flow_process')->default(0)->comment('当前步骤编号');
            $table->text('content')->nullable()->comment('会签内容');
            $table->tinyInteger('is_agree',1)->comment('审核意见：1同意；2不同意')->default(0);
            $table->integer('sign_att_id')->default(0);
            $table->tinyInteger('sign_look')->comment('步骤设置的会签可见性,0总是可见（默认）,1本步骤经办人之间不可见2针对其他步骤不可见')->default(0);
            $table->integer('dateline')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flow');
        Schema::dropIfExists('flow_process');
        Schema::dropIfExists('form');
        Schema::dropIfExists('form_function');
        Schema::dropIfExists('menu');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_type');
        Schema::dropIfExists('role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('run');
        Schema::dropIfExists('run_cache');
        Schema::dropIfExists('run_log');
    }
}
