<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m220625_131619_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull()->unique(),
            'password_old' => $this->string(),
            'auth_key' => $this->string()->unique(),
            'first_name' => $this->string(32),
            'surname' => $this->string(32),
            'patronymic' => $this->string(32),
            'date_birth' => $this->date(),
            'city' => $this->string(32),
            'phone_number' => $this->string(11)->unique(),
            'avatar' => $this->string(),
            'avatar_initial' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
