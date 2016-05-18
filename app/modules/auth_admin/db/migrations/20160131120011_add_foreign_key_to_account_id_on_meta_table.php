<?php

use Phinx\Migration\AbstractMigration;

class AddForeignKeyToAccountIdOnMetaTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('meta');

        $table->addForeignKey('account_id', 'accounts', 'id', array('delete'=> 'CASCADE', 'update'=> 'NO_ACTION'))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table->dropForeignKey('account_id');
    }
}
