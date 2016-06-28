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
                $table->addColumn('name', 'string', ['length' => 255, 'default' => 'Unknown']);
                $table->addColumn('image', 'string', ['length' => 255, 'default' => '', 'notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@shoutzor_album') === false) {
            $util->createTable('@shoutzor_album', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255, 'default' => 'Unknown']);
                $table->addColumn('image', 'string', ['length' => 255, 'default' => '', 'notnull' => false]);
                $table->setPrimaryKey(['id']);
            });
        }

        if ($util->tableExists('@shoutzor_media') === false) {
            $util->createTable('@shoutzor_media', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('title', 'string', ['length' => 255, 'default' => '']);
                $table->addColumn('filename', 'text', ['length' => 1000, 'default' => '']);
                $table->addColumn('parsed_filename', 'text', ['length' => 1000, 'default' => '']);
                $table->addColumn('uploader_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('thumbnail', 'text', ['length' => 1000, 'default' => '']);
                $table->addColumn('status', 'boolean', ['length' => 1, 'default' => '0']);
                $table->addColumn('created', 'datetime');
                $table->addColumn('crc', 'text', ['length' => 1000, 'default' => '']);
                $table->addColumn('duration', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->setPrimaryKey(['id']);
                $table->addIndex(array('artist_id'), 'artist_index');
                $table->addIndex(array('uploader_id'), 'uploader_index');
                $table->addIndex(array('status'), 'status_index');
                $table->addIndex(array('created'), 'created_index');
            });
        }

        if ($util->tableExists('@shoutzor_media_artist') === false) {
            $util->createTable('@shoutzor_media_artist', function ($table) {
                $table->addColumn('media_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addColumn('artist_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addUniqueIndex(array('media_id', 'artist_id'), 'media_artist_index');
            });
        }

        if ($util->tableExists('@shoutzor_media_album') === false) {
            $util->createTable('@shoutzor_media_album', function ($table) {
                $table->addColumn('media_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addColumn('album_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addUniqueIndex(array('media_id', 'album_id'), 'media_album_index');
            });
        }

        if ($util->tableExists('@shoutzor_artist_album') === false) {
            $util->createTable('@shoutzor_artist_album', function ($table) {
                $table->addColumn('artist_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addColumn('album_id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => false]);
                $table->addUniqueIndex(array('artist_id', 'album_id'), 'artist_album_index');
            });
        }

        if ($util->tableExists('@shoutzor_requestlist') === false) {
            $util->createTable('@shoutzor_requestlist', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('media_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('requester_id', 'integer', ['unsigned' => true, 'length' => 10, 'notnull' => false]);
                $table->addColumn('requesttime', 'datetime');
                $table->setPrimaryKey(['id']);
                $table->addIndex(array('media_id'), 'artist_index');
                $table->addIndex(array('requester_id'), 'uploader_index');
                $table->addIndex(array('requesttime'), 'requesttime_index');
            });
        }

        if ($util->tableExists('@shoutzor_history') === false) {
            $util->createTable('@shoutzor_history', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('media_id', 'integer', ['unsigned' => true, 'length' => 10]);
                $table->addColumn('requester_id', 'integer', ['unsigned' => true, 'length' => 10, 'notnull' => false]);
                $table->addColumn('played_at', 'datetime');
                $table->setPrimaryKey(['id']);
                $table->addIndex(array('media_id'), 'artist_index');
                $table->addIndex(array('requester_id'), 'uploader_index');
                $table->addIndex(array('played_at'), 'played_at_index');
            });
        }
    },

    /*
     * Enable hook
     */
    'enable' => function ($app) {
    },

    /*
     * Disable hook
     */
    'disable' => function ($app) {
    },

    /*
     * Uninstall hook
     *
     */
    'uninstall' => function ($app) {

        /**
         * @TODO remove the music directory when uninstalled
         */

        //delete our config values
        $app['config']->remove('shoutzor');

        $util = $app['db']->getUtility();

        if ($util->tableExists('@shoutzor_artist')) {
            $util->dropTable('@shoutzor_artist');
        }

        if ($util->tableExists('@shoutzor_album')) {
            $util->dropTable('@shoutzor_album');
        }

        if ($util->tableExists('@shoutzor_media')) {
            $util->dropTable('@shoutzor_media');
        }

        if ($util->tableExists('@shoutzor_media_artist')) {
            $util->dropTable('@shoutzor_media_artist');
        }

        if ($util->tableExists('@shoutzor_media_album')) {
            $util->dropTable('@shoutzor_media_album');
        }

        if ($util->tableExists('@shoutzor_artist_album')) {
            $util->dropTable('@shoutzor_artist_album');
        }

        if ($util->tableExists('@shoutzor_requestlist')) {
            $util->dropTable('@shoutzor_requestlist');
        }

        if ($util->tableExists('@shoutzor_history')) {
            $util->dropTable('@shoutzor_history');
        }
    },

    /*
     * Runs all updates that are newer than the current version.
     *
     */
    'updates' => [],

];
