<?php
// +----------------------------------------------------------------------
// | NewThink [ Think More,Think Better! ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2030 http://www.sxqibo.com All rights reserved.
// +----------------------------------------------------------------------
// | 版权所有：山西岐伯信息科技有限公司
// +----------------------------------------------------------------------
// | Author: yanghongwei  Date:2023/11/1 Time:16:56
// +----------------------------------------------------------------------

declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use think\migration\Migrator;

class Install extends Migrator
{
    /**
     * 安装文件
     */
    public function change(): void
    {
        // 管理员相关
        $this->admin();
        $this->adminGroup();
        $this->adminGroupAccess();
        $this->adminRule();// 菜单相关
        $this->adminLog();
        // 通用相关
        $this->commonConfig(); // 设置
        $this->commonArea();// 地区
        $this->commonAttachment();// 附件
        $this->commonCaptcha();// 验证码
        $this->commonToken();// token
        // 安全相关
        $this->securityDataRecycle();
        $this->securityDataRecycleLog();
        $this->securitySensitiveData();
        $this->securitySensitiveDataLog();
        // 用户相关
        $this->user();
        $this->userGroup();
        $this->userMoneyLog();
        $this->userRule();
        // 开发相关
        $this->devTestBuild();
        $this->devCrudLog();

    }

    // +----------------------------------------------------------------------
    // | 管理员相关
    // +----------------------------------------------------------------------
    public function admin(): void
    {
        if (!$this->hasTable('admin')) {
            $table = $this->table('admin', [
                'id' => false,
                'comment' => '管理员',
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
                'comment' => '管理员-分组表',
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

    public function adminGroupAccess(): void
    {
        if (!$this->hasTable('admin_group_access')) {
            $table = $this->table('admin_group_access', [
                'id' => false,
                'comment' => '管理员-分组权限关联',
                'row_format' => 'DYNAMIC',
                'primary_key' => ['admin_id', 'group_id'],
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('admin_id', 'integer', ['comment' => '管理员ID', 'signed' => false, 'null' => false])
                ->addColumn('group_id', 'integer', ['comment' => '分组ID', 'signed' => false, 'null' => false])
                // 索引
                ->addIndex(['admin_id', 'group_id'], ['unique' => true])
                ->create();
        }
    }

    private function adminRule(): void
    {
        if (!$this->hasTable('admin_rule')) {
            $table = $this->table('admin_rule', [
                'id' => false,
                'comment' => '管理员-菜单和权限规则',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // ID相关
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('pid', 'integer', ['signed' => false, 'default' => 0, 'comment' => '上级ID', 'null' => false])
                // 名称类型相关
                ->addColumn('type', 'enum', ['values' => 'menu_dir,menu,button', 'default' => 'menu', 'comment' => '类型：menu_dir=菜单目录,menu=菜单项,button=页面按钮', 'null' => false])
                ->addColumn('title', 'string', ['limit' => 50, 'default' => '', 'comment' => '标题', 'null' => false])
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '规则名称', 'null' => false])
                // 其他相关
                ->addColumn('path', 'string', ['limit' => 255, 'default' => '', 'comment' => '路由路径', 'null' => false])
                ->addColumn('icon', 'string', ['limit' => 50, 'default' => '', 'comment' => '图标', 'null' => false])
                ->addColumn('menu_type', 'enum', ['values' => 'tab,link,iframe', 'default' => null, 'comment' => '菜单类型:tab=选项卡,link=链接,iframe=Iframe', 'null' => true])
                ->addColumn('url', 'string', ['limit' => 255, 'default' => '', 'comment' => '链接', 'null' => false])
                ->addColumn('component', 'string', ['limit' => 255, 'default' => '', 'comment' => '组件', 'null' => false])
                ->addColumn('keepalive', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => 0, 'comment' => '缓存:0=否,1=是', 'null' => false])
                ->addColumn('extend', 'enum', ['values' => 'none,add_rules_only,add_menu_only', 'default' => 'none', 'comment' => '扩展属性:none=无,add_rules_only=只添加为路由,add_menu_only=只添加为菜单', 'null' => true])
                ->addColumn('remark', 'string', ['limit' => 255, 'default' => '', 'comment' => '备注', 'null' => false])
                ->addColumn('weigh', 'integer', ['default' => 0, 'comment' => '权重', 'null' => false])
                ->addColumn('status', 'enum', ['values' => 'normal,hidden,disabled', 'default' => 'normal', 'comment' => '状态:normal=正常,hidden=隐藏,disabled=禁用', 'null' => false])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '更新时间', 'null' => false])
                // 索引
                ->addIndex(['pid'])
                ->addIndex(['name'], ['unique' => true])
                ->create();
        }
    }

    public function adminLog(): void
    {
        if (!$this->hasTable('admin_log')) {
            $table = $this->table('admin_log', [
                'id' => false,
                'comment' => '管理员-日志表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('admin_id', 'integer', ['comment' => '管理员ID', 'signed' => false, 'null' => false])
                // 内容相关
                ->addColumn('username', 'string', ['limit' => 20, 'default' => '', 'comment' => '用户名', 'null' => false])
                ->addColumn('url', 'string', ['limit' => 255, 'default' => '', 'comment' => '操作URL', 'null' => false])
                ->addColumn('title', 'string', ['limit' => 50, 'default' => '', 'comment' => '日志标题', 'null' => false])
                ->addColumn('data', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null, 'comment' => '请求数据'])
                ->addColumn('ip', 'string', ['limit' => 15, 'default' => '', 'comment' => 'IP地址', 'null' => false])
                ->addColumn('user_agent', 'string', ['limit' => 255, 'default' => '', 'comment' => 'user-agent', 'null' => false])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                // 索引
                ->addIndex(['admin_id'], ['unique' => false])
                ->create();
        }
    }

    // +----------------------------------------------------------------------
    // | 通用相关
    // +----------------------------------------------------------------------

    public function commonConfig(): void
    {
        if (!$this->hasTable('common_config')) {
            $table = $this->table('config', [
                'id' => false,
                'comment' => '通用-系统配置',
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

    public function commonArea(): void
    {
        if (!$this->hasTable('common_area')) {
            $table = $this->table('area', [
                'id' => false,
                'comment' => '通用-地区',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('pid', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'default' => 0, 'comment' => '上级地区ID', 'null' => false])
                // 名称相关
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '地区名称', 'null' => false])
                ->addColumn('short_name', 'string', ['limit' => 50, 'default' => '', 'comment' => '地区简称', 'null' => false])
                ->addColumn('merger_name', 'string', ['limit' => 255, 'default' => '', 'comment' => '地区全称', 'null' => false])
                // 内容相关
                ->addColumn('level', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_TINY, 'default' => null, 'comment' => '层级:1=省,2=市,3=区/县', 'null' => false])
                ->addColumn('code', 'string', ['limit' => 20, 'default' => '', 'comment' => '地区编码', 'null' => false])
                ->addColumn('zip_code', 'string', ['limit' => 20, 'default' => '', 'comment' => '邮政编码', 'null' => false])
                ->addColumn('pinyin', 'string', ['limit' => 255, 'default' => '', 'comment' => '拼音', 'null' => false])
                ->addColumn('first', 'string', ['limit' => 50, 'default' => '', 'comment' => '首字母', 'null' => false])
                ->addColumn('lng', 'string', ['limit' => 20, 'default' => '', 'comment' => '经度', 'null' => false])
                ->addColumn('lat', 'string', ['limit' => 20, 'default' => '', 'comment' => '纬度', 'null' => false])
                // 索引
                ->addIndex(['pid'])
                ->addIndex(['code'], ['unique' => true])
                ->create();
        }
    }

    private function commonAttachment(): void
    {
        if (!$this->hasTable('common_attachment')) {
            $table = $this->table('attachment', [
                'id' => false,
                'comment' => '通用-附件',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关ID
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                ->addColumn('admin_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '管理员ID', 'null' => false])
                ->addColumn('user_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '上传用户ID', 'null' => false])
                // 名称
                ->addColumn('name', 'string', ['limit' => 50, 'default' => '', 'comment' => '名称', 'null' => false])
                // 附件相关
                ->addColumn('url', 'string', ['limit' => 255, 'default' => '', 'comment' => 'url', 'null' => false])
                ->addColumn('width', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'default' => 0, 'comment' => '宽度', 'null' => false])
                ->addColumn('height', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'default' => 0, 'comment' => '高度', 'null' => false])
                ->addColumn('size', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_BIG, 'default' => 0, 'comment' => '大小', 'null' => false])
                ->addColumn('mime_type', 'string', ['limit' => 30, 'default' => '', 'comment' => 'mine类型', 'null' => false])
                ->addColumn('quote', 'integer', ['signed' => false, 'limit' => MysqlAdapter::INT_SMALL, 'default' => 0, 'comment' => '引用次数', 'null' => false])
                ->addColumn('storage', 'string', ['limit' => 30, 'default' => '', 'comment' => '存储引擎', 'null' => false])
                ->addColumn('sha1', 'string', ['limit' => 40, 'default' => '', 'comment' => 'sha1', 'null' => false])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('last_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '最后上传时间', 'null' => false])
                // 索引
                ->addIndex(['admin_id'])
                ->addIndex(['user_id'])
                ->addIndex(['name'])
                ->addIndex(['url'])
                ->addIndex(['sha1'])
                ->create();
        }
    }

    private function commonCaptcha(): void
    {
        if (!$this->hasTable('common_captcha')) {
            $table = $this->table('captcha', [
                'id' => false,
                'comment' => '通用-验证码',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 验证码相关
                ->addColumn('key', 'string', ['limit' => 32, 'default' => '', 'comment' => '验证码KEY', 'null' => false])
                ->addColumn('code', 'string', ['limit' => 32, 'default' => '', 'comment' => '验证码(加密后)', 'null' => false])
                ->addColumn('captcha', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => true, 'default' => null, 'comment' => '验证码数据'])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('expire_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '失效时间', 'null' => false])
                // 索引
                ->addIndex(['key'], ['unique' => true])
                ->create();
        }
    }


    private function commonToken(): void
    {
        if (!$this->hasTable('common_token')) {
            $table = $this->table('common_token', [
                'id' => false,
                'comment' => '通用-TOKEN',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                // 相关内容
                ->addColumn('token', 'string', ['limit' => 50, 'default' => '', 'comment' => 'Token', 'null' => false])
                ->addColumn('type', 'string', ['limit' => 30, 'default' => '', 'comment' => '类型,如admin,user等', 'null' => false])
                ->addColumn('user_id', 'integer', ['signed' => false, 'default' => 0, 'comment' => '用户ID', 'null' => false])
                // 时间相关
                ->addColumn('expire_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '失效时间', 'null' => false])
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                // 索引
                ->addIndex(['token'], ['unique' => true])
                ->create();
        }
    }


    // +----------------------------------------------------------------------
    // | 安全相关
    // +----------------------------------------------------------------------

    private function securityDataRecycle(): void
    {
        if (!$this->hasTable('security_data_recycle')) {
            $table = $this->table('security_data_recycle', [
                'id' => false,
                'comment' => '安全-回收规则表',
                'row_format' => 'DYNAMIC',
                'primary_key' => 'id',
                'collation' => 'utf8mb4_unicode_ci',
            ]);

            $table
                ->addColumn('id', 'integer', ['comment' => 'ID', 'signed' => false, 'identity' => true, 'null' => false])
                // 规则名称
                ->addColumn('name','string', ['limit' => 50, 'default' => '', 'comment' => '规则名称', 'null' => false])
                ->addColumn('controller','string', ['limit' => 50, 'default' => '', 'comment' => '控制器', 'null' => false])
                ->addColumn('controller_name','string', ['limit' => 50, 'default' => '', 'comment' => '控制器名', 'null' => false])
                ->addColumn('data_table','string', ['limit' => 50, 'default' => '', 'comment' => '对应数据表', 'null' => false])
                ->addColumn('primary_key','string', ['limit' => 50, 'default' => '', 'comment' => '数据表主键', 'null' => false])
                ->addColumn('status', 'enum', ['values' => '0,1', 'default' => '1', 'comment' => '状态:0=禁用,1=启用', 'null' => false])
                // 时间相关
                ->addColumn('create_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '创建时间', 'null' => false])
                ->addColumn('update_time', 'biginteger', ['signed' => false, 'limit' => 16, 'default' => 0, 'comment' => '更新时间', 'null' => false])
                // 索引
                ->addIndex(['name'])
                ->addIndex(['controller'])
                ->addIndex(['controller_name'])
                ->addIndex(['data_table'])
                ->addIndex(['primary_key'])
                ->addIndex(['status'])
                ->create();
        }
    }

    private function securityDataRecycleLog(): void
    {
    }

    private function securitySensitiveData(): void
    {
    }

    private function securitySensitiveDataLog(): void
    {
    }



    // +----------------------------------------------------------------------
    // | 用户相关
    // +----------------------------------------------------------------------
    private function user(): void
    {
    }

    private function userGroup(): void
    {
    }

    private function userMoneyLog(): void
    {
    }

    private function userRule(): void
    {
    }

    // +----------------------------------------------------------------------
    // | 开发相关
    // +----------------------------------------------------------------------
    private function devTestBuild(): void
    {
    }

    private function devCrudLog(): void
    {
    }


}
