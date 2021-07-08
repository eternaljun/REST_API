<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m210708_141631_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->text()->Null(),
            'surname' => $this->text()->Null(),
            'phone' => $this->integer(11)->Null(),
            'email' => $this->text()->notNull(),
            'password' => $this->text()->notNull(),
            'register_date' => $this->datetime(),
            'token' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
