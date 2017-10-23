<?php

use Phinx\Migration\AbstractMigration;

class CentralizedTables extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
       public function change() {

        $table = $this->table('applications');
        $table->addColumn('keyword', 'string', array('limit' => 150))
                ->addColumn('name', 'string', array('limit' => 200))
                ->addColumn('description', 'text')
                ->addColumn('status', 'string', array('limit' => 20, 'default' => "active"))
                ->addColumn('createdAt', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('keyword'), array('unique' => true))
                ->create();


        $table1 = $this->table('application_access');
        $table1->addColumn('app_keyword', 'string', array('limit' => 150))
                ->addColumn('username', 'string', array('limit' => 150))
                ->addColumn('status', 'string', array('limit' => 30, 'default' => "active"))
                ->addColumn('last_login', 'datetime')
                ->create();


        $table2 = $this->table('application_permissions');
        $table2->addColumn('keyword', 'string', array('limit' => 150))
                ->addColumn('app_keyword', 'string', array('limit' => 150))
                ->addColumn('description', 'text')
                ->addColumn('status', 'string', array('limit' => 30, 'default' => "active"))
                ->addColumn('createdAt', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
                ->create();


        $table3 = $this->table('user_permissions');
        $table3->addColumn('app_keyword', 'string', array('limit' => 150))
                ->addColumn('permission_keyword', 'string', array('limit' => 150))
                ->addColumn('username', 'string', array('limit' => 150))
                ->addColumn('status', 'string', array('limit' => 30, 'default' => "granted"))
                ->create();
    }

    
}
