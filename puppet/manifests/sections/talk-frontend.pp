mysql::grant { 'frontend':
  mysql_db         => 'frontend',
  mysql_user       => 'root',
  mysql_password   => 'root',
  mysql_privileges => 'ALL',
  mysql_host       => 'localhost',
} ->

mysql::queryfile { 'db.sql':
  mysql_file       => '/srv/www/front-end-performance/.db/db.sql',
  mysql_db         => 'frontend',
  mysql_user       => 'root',
  mysql_password   => 'root',
  mysql_host       => 'localhost',
}
