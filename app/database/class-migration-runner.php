<?php

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    class MigrationRunner {

        global $wpdb;

        public function migrate($direction = 'up')
        {
            switch ($direction) {
                case 'up':
                    foreach ($this->getPendingMigrationFiles() as $migration_file) {
                        [$up, $down] = $this->loadMigrationFromFile($migration_file);
                        $this->runUpMigrationSql($up);
                    }
                    break;

                case 'down':
                    foreach ($this->getRanMigrationFiles() as $migration_file) {
                        [$up, $down] = $this->loadMigrationFromFile($migration_file);
                        $this->runDownMigrationSql($down);
                    }
                    break;

                default:
                    throw new \Exception("Migration direction parameter invalid", 1);
                    break;
            }

        }

        protected function getPendingMigrationFiles()
        {
            //TODO
        }

        protected function getRanMigrationFiles()
        {
            //TODO
        }

        protected function loadMigrationFromFile($migration_file)
        {
            //TODO
        }

        protected function runSql($sql)
        {
            $result = dbDelta($sql);

            //TODO: check result
        }

        protected function runUpMigrationSql($sql)
        {
            $success = $this->runSql($sql);
            if($success){
                $this->markMigrationAsRan($migration_file);
            } else {
                throw new \Exception("Bad migration, see error logs", 1);
            }
        }

        protected function runDownMigrationSql($sql)
        {
            $success = $this->runSql($sql);
            if($success){
                $this->unmarkMigrationAsRan($migration_file);
            } else {
                throw new \Exception("Bad migration, see error logs", 1);
            }
        }

        protected function markMigrationAsRan($migration_file)
        {
            //TODO
        }

        protected function unmarkMigrationAsRan($migration_file)
        {
            //TODO
        }
    }

?>
