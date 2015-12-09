<?php

return [

    /*
     * Installation hook.
     */
    'install' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@shoutzor_artist') === false) {
            $util->createTable('@shoutzor_artist', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@shoutzor_music') === false) {
            $util->createTable('@shoutzor_music', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('artist_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('filename', 'text', ['length' => 1000, 'default' => '']);
                $table->addColumn('uploader_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('is_video', 'boolean', ['default' => false]);
                $table->addColumn('status', 'boolean', ['length' => 1, 'default' => '0']);
                $table->addColumn('created', 'datetime');
                $table->setPrimaryKey(['id']);
                $table->addIndex(array('artist_id'), 'artist_index');
                $table->addIndex(array('uploader_id'), 'uploader_index');
                $table->addIndex(array('status'), 'status_index');
                $table->addIndex(array('created'), 'created_index');
            });
        }
    },

    /*
     * Enable hook
     *
     */
    'enable' => function ($app) {
    },

    /*
     * Uninstall hook
     *
     */
    'uninstall' => function ($app) {

        /**
         * @TODO remove the music directory when uninstalled
         */

        // remove the config
        $app['config']->remove('shoutzor');

        $util = $app['db']->getUtility();

        //Drop the shoutzor_music table
        if ($util->tableExists('@shoutzor_music')) {
            $util->dropTable('@shoutzor_music');
        }

        //Drop the shoutzor_artist table
        if ($util->tableExists('@shoutzor_artist')) {
            $util->dropTable('@shoutzor_artist');
        }
    },

    /*
     * Runs all updates that are newer than the current version.
     *
     */
    'updates' => [],

];