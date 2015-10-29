<?php

class ChangeOwnerIdField extends \Migrations\AbstractMigration
{
    public function up()
    {
        $table = $this->table('oauth_sessions');
        $table
            ->changeColumn('owner_id', 'string', [
                    'limit' => 20
                ]);
        $table->update();
    }

    public function down()
    {
        $table = $this->table('oauth_sessions');
        $table->changeColumn('owner_id', 'int', [
                'limit' => 11
            ]);
        $table->update();
    }
}
