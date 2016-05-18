<?php

use Phinx\Migration\AbstractMigration;

class CreateRecoveryTokensTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table( 'recovery_tokens', array(
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ));

        $table->addColumn( 'selector', 'string', array( 'limit' => 16 ));
        $table->addColumn( 'token', 'string', array( 'limit' => 64 ));
        $table->addColumn( 'account_id', 'integer');

        $table->addForeignKey('account_id', 'accounts', 'id', array('delete'=> 'CASCADE', 'update'=> 'NO_ACTION'));
        $table->addIndex('selector', array('unique' => true));
        $table->addIndex('account_id', array('unique' => true));

        $table->addColumn( 'expire', 'datetime' );

        // timestamps
        $table->addColumn( 'dt_create', 'datetime' );
        $table->addColumn( 'dt_update', 'datetime', array( 'null' => true ) );
        $table->addColumn( 'dt_delete', 'datetime', array( 'null' => true ) );

        $table->save( );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable( 'recovery_tokens' );
    }
}
