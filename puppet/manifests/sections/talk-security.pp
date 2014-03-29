class { 'nodejs':
  version => 'stable',
  make_install => false,
} ->

package { 'phantomjs':
  provider => npm
}

mysql::grant { 'security':
  mysql_db         => 'security',
  mysql_user       => 'username_here',
  mysql_password   => 'password_here',
  mysql_privileges => 'ALL',
  mysql_host       => 'localhost',
} ->

mysql::queryfile { 'security.sql':
  mysql_file       => '/srv/www/security/.db/security.sql',
  mysql_db         => 'security',
  mysql_user       => 'username_here',
  mysql_password   => 'password_here',
  mysql_host       => 'localhost',
}
