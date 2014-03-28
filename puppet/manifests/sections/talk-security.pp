class { 'nodejs':
  version => 'stable',
  make_install => false,
}

package { 'phantomjs':
  provider => npm
}
