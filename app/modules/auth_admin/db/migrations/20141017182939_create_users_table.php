<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table( 'users', array(
            'engine' => 'InnoDB',
            'collation' => 'utf8_unicode_ci'
        ) );

        $table->addColumn( 'username', 'string', array( 'limit' => 128 ) );
        $table->addColumn( 'password', 'string', array( 'limit' => 255 ) );
        $table->addColumn( 'salt', 'string', array( 'limit' => 255 ) );
        $table->addColumn('first_name', 'string', array( 'limit' => 128 ) )
        $table->addColumn('last_name', 'string', array( 'limit' => 128 ) )
        $table->addColumn( 'email', 'string', array( 'limit' => 255 ) );
        $table->addColumn( 'lang', 'string', array( 'limit' => 10, 'default' => 'en' ) );
        $table->addColumn( 'enabled', 'integer', array( 'null' => true ) );
        $table->addColumn( 'dt_login', 'datetime', array( 'null' => true ) );

        // timestamps
        $table->addColumn( 'dt_create', 'datetime', array( 'null' => true ) );
        $table->addColumn( 'dt_update', 'datetime', array( 'null' => true ) );
        $table->addColumn( 'dt_delete', 'datetime', array( 'null' => true ) );

        // indexes
        $table->addIndex( array( 'username' ), array( 'unique' => true ) );
        $table->addIndex( array( 'email' ), array( 'unique' => true ) );

        $table->save( );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable( 'users' );
    }
}
