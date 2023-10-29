<?php

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

class Install extends Migrator
{
    /**
     * 安装文件
     */
    public function change()
    {
        // 管理员相关
        $this->admin();
        $this->adminGroup();
        $this->adminGroupAccess();
        $this->adminLog();
        // 地区
        $this->area();
        // 附件
        $this->attachmennt();
        // 验证码
        $this->captcha();
        // 设置
        $this->config();
        // 菜单
        $this->menuRule();
        // 数据安全
        $this->securityDartaRecycle();
        $this->securityDataRecycleLog();
        $this->securitySensitiveData();
        $this->securitySensitiveDataLog();
        // 测试
        $this->testBuild();
        // token
        $this->token();
        // 用户
        $this->user();
        $this->userGroup();
        $this->userMoneyLog();
        $this->userRule();
        // 开发
        $this->crudLog();
    }

    public function admin(): void
    {
        if (!$this->hasTable('admin')) {
            $table = $this->table('admin', [
                'id' => false,
                'comment' => '管理员表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                // 基本信息
                ->addColumn('username', 'string', ['limit' => 20, 'default' => '', 'comment' => '用户名', 'null' => false])
                ->addColumn('nickname', 'string', ['limit' => 50, 'default' => '', 'comment' => '昵称', 'null' => false])
                ->addColumn('avatar', 'string', ['limit' => 255, 'default' => '', 'comment' => '头像', 'null' => false])
                ->addColumn('email', 'string', ['limit' => 50, 'default' => '', 'comment' => '邮箱', 'null' => false])
                ->addColumn('mobile', 'string', ['limit' => 11, 'default' => '', 'comment' => '手机', 'null' => false])
                ->addColumn('signature', 'string', ['limit' => 255, 'default' => '', 'comment' => '签名', 'null' => false])
                // 登录相关
                ->addColumn('login_failure', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '登录失败次数', 'null' => false])
                ->addColumn('last_login_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '上次登录时间', 'null' => true])
                // 权限相关
                ->addColumn('password', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码', 'null' => false])
                ->addColumn('salt', 'string', ['limit' => 255, 'default' => '', 'comment' => '密码盐', 'null' => false])
                // 状态
                ->addColumn('status', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '状态:0=禁用,1=正常', 'null' => false])
                // 创建时间
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                // 更新时间
                ->addColumn('update_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '更新时间', 'null' => false])
                // 索引
                ->addIndex(['username'], ['unique' => true])
                ->addIndex(['email'], ['unique' => true])
                ->addIndex(['mobile'], ['unique' => true])
                ->create();
        }
    }

    public function adminGroup(): void
    {
        if (!$this->table('admin_group')) {
            $table = $this->table('admin_group', [
                'id' => false,
                'comment' => '管理员分组表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
            $table
                // ID相关
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('pid', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'default' => 0, 'comment' => '上级分组ID', 'null' => false])
                // 权限规则ID
                ->addColumn('rule_ids', 'string', ['limit' => 255, 'default' => '', 'comment' => '权限规则ID', 'null' => false])
                // 组名
                ->addColumn('name', 'string', ['limit' => 30, 'default' => '', 'comment' => '组名', 'null' => false])
                // 状态
                ->addColumn('status', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 1, 'comment' => '状态:0=禁用,1=正常', 'null' => false])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '更新时间', 'null' => false])
                // 索引
                ->addIndex(['name'], ['unique' => true])
                ->create();
        }
    }

    public function config(): void
    {
        if (!$this->hasTable('config')) {
            $table = $this->table('config', [
                'id' => false,
                'comment' => '系统配置',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                // 分组
                ->addColumn('group', 'string', ['limit' => 30, 'default' => '', 'comment' => '分组', 'null' => false])
                // 变量相关
                ->addColumn('name', 'string', ['limit' => 30, 'default' => '', 'comment' => '变量名', 'null' => false])
                ->addColumn('title', 'string', ['limit' => 50, 'default' => '', 'comment' => '变量标题', 'null' => false])
                ->addColumn('tip', 'string', ['limit' => 100, 'default' => '', 'comment' => '变量描述', 'null' => false])
                ->addColumn('type', 'string', ['limit' => 30, 'default' => '', 'comment' => '变量输入组件类型', 'null' => false])
                // 内容相关
                ->addColumn('value', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null, 'comment' => '变量值'])
                ->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null, 'comment' => '字典数据'])
                // 其他相关
                ->addColumn('rule', 'string', ['limit' => 100, 'default' => '', 'comment' => '验证规则', 'null' => false])
                ->addColumn('extend', 'string', ['limit' => 255, 'default' => '', 'comment' => '扩展属性', 'null' => false])
                ->addColumn('allow_del', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '允许删除:0=否,1=是', 'null' => false])
                ->addColumn('weigh', 'integer', ['default' => 0, 'comment' => '权重', 'null' => false])
                // 索引
                ->addIndex(['name'], ['unique' => true])
                ->create();
        }
    }

}