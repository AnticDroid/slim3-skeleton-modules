<?php

use Phinx\Migration\AbstractMigration;

class CreateMetaTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table( 'meta', array(
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ));

        $table->addColumn( 'name', 'string');
        $table->addColumn( 'value', 'string');
        $table->addColumn( 'account_id', 'integer');

        // timestamps
        $table->addColumn( 'dt_create', 'datetime', array( 'null' => true ) );
        $table->addColumn( 'dt_update', 'datetime', array( 'null' => true ) );
        $table->addColumn( 'dt_delete', 'datetime', array( 'null' => true ) );

        $table->save( );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable( 'meta' );
    }
}
