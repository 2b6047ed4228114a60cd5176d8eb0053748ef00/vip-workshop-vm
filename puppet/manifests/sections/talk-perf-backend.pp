mysql::grant { 'perf_backend':
  mysql_db         => 'perf_backend',
  mysql_user       => 'root',
  mysql_password   => 'root',
  mysql_privileges => 'ALL',
  mysql_host       => 'localhost',
} ->

mysql::queryfile { 'perf-backend.sql':
  mysql_file       => '/srv/www/perf-backend/.db/db.sql',
  mysql_db         => 'perf_backend',
  mysql_user       => 'root',
  mysql_password   => 'root',
  mysql_host       => 'localhost',
}
