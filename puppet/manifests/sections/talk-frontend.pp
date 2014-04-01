mysql::grant { 'frontend':
  mysql_db         => 'frontend',
  mysql_user       => 'username_here',
  mysql_password   => 'password_here',
  mysql_privileges => 'ALL',
  mysql_host       => 'localhost',
} ->

mysql::queryfile { 'db.sql':
  mysql_file       => '/srv/www/front-end-performance/.db/db.sql',
  mysql_db         => 'frontend',
  mysql_user       => 'username_here',
  mysql_password   => 'password_here',
  mysql_host       => 'localhost',
}
