<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%auth}}`.
 *
 */
class m220717_110113_create_auth_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%auth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->string(),
            'client_id' => $this->string(),
            'auth_code' => $this->string(),
            'secret_key' => $this->string(),
            'access_token' => $this->string(),
            'refresh_token' => $this->string(),
            'tokens_create' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex(
            '{{%ids}}',
            '{{%auth}}',
            ['user_id', 'client_id'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            '{{%ids}}',
            '{{%auth}}'
        );

        $this->dropTable('{{%auth}}');
    }
}
