Exec { path => '/bin:/usr/bin:/usr/local/bin:/usr/sbin:/sbin' }

import 'helpers/*'
import 'sections/*'

# Make sure apt-get is up-to-date before we do anything else
stage { 'updates': before => Stage['main'] }
class { 'updates': stage => updates }

# updates
class updates {
    exec { 'apt-get update':
        command => 'apt-get update --quiet --yes',
        timeout => 0
    }

    if 'virtualbox' != $virtual {
        exec { 'apt-get upgrade':
            command => 'apt-get upgrade --quiet --yes',
            timeout => 0
        }
    }
}

user { 'vagrant':
    ensure => 'present',
    system => true,
    notify => Service['php5-fpm'],
}

# Elasticsearch
$config_hash = {
   'ES_USER' => 'vagrant',
   'ES_GROUP' => 'vagrant',
 }

file { '/home/vagrant/www/elasticsearch/data':
  ensure => directory,
  owner => 'vagrant',
  group => 'vagrant',
} ->
file { '/home/vagrant/www/elasticsearch/logs':
  ensure => directory,
  owner => 'vagrant',
  group => 'vagrant',
} ->
class { 'elasticsearch':
	java_install => true,
  init_defaults => $config_hash,
	package_url => 'https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.1.0.deb',
  config                   => {
     'ES_USER' => 'vagrant',
     'ES_GROUP' => 'vagrant',
     'index'                => {
       'number_of_replicas' => '0',
       'number_of_shards'   => '2'
     },
     'path' => {
       'data'     => '/home/vagrant/www/elasticsearch/data',
       'logs'     => '/home/vagrant/www/elasticsearch/logs'
     },
     'discovery.zen.ping.multicast.enabled' => false,
     'discovery.zen.minimum_master_nodes' => 1,
     'gateway.expected_nodes' => 1
   }
}

elasticsearch::plugin{'mobz/elasticsearch-head':
   module_dir => 'head'
 }
elasticsearch::plugin{'polyfractal/elasticsearch-inquisitor':
	module_dir => 'inquisitor'
}
elasticsearch::plugin{'elasticsearch/elasticsearch-analysis-icu/2.1.0':
	module_dir => 'analysis-icu'
}
