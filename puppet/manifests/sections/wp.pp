# Install WP-CLI
class { 'wp::cli':
  ensure  => installed,
  version => '0.14.1',
}
