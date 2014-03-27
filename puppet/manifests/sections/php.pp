$php_version = '5.4.26-1+deb.sury.org~precise+1'

include php
include apt

apt::source { 'php54':
  location    => 'http://ppa.launchpad.net/ondrej/php5-oldstable/ubuntu',
  release     => 'precise',
  repos       => 'main',
  key         => '14aa40ec0831756756d7f66c4f4ea0aae5267a6c',
  key_server  => 'hkp://keyserver.ubuntu.com:80',
  include_src => true
}

class {
  'php::cli':
    ensure  => $php_version,
    require => Apt::Source['php54'];
  'php::composer':;
  'php::dev':
    ensure  => $php_version,
    require => Apt::Source['php54'];
  'php::fpm':;
  'php::pear':;
  'php::phpunit':;

  # Extensions
  'php::extension::apc':
    ensure => absent;
  'php::extension::curl':;
  'php::extension::gd':;
  'php::extension::imagick':;
  'php::extension::mcrypt':;
  'php::extension::memcache':;
  'php::extension::mysql':;
}

php::fpm::conf { 'www': user => 'vagrant' }

file { '/etc/php5/conf.d/apc.ini': ensure => absent }
