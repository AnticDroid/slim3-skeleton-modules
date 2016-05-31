<?php

use Phinx\Migration\AbstractMigration;

class AddFirstLastNameColumnsToAccountsTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function change()
    {
        $table = $this->table('accounts');
        $table->addColumn('first_name', 'string', array('after' => 'username'))
        	->addColumn('last_name', 'string', array('after' => 'first_name'))
            ->update();
    }
}
