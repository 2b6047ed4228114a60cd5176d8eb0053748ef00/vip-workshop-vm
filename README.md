VIP Workshop VM
===============

Hey, welcome to the VIP Workshop Virtual Machine.

Purpose
-------

The goal of this VM is to simplify and standardize the setup across all
attendees and speakers.

No more last-minute permissions debugging or people, who didn't bother
to install web server, MySQL, memcached and a several more pieces of
software needed for the presentations.

Vagrant
-------

This VM is maintained using [Vagrant](http://www.vagrantup.com/).
vagrant is a tool, which helps create, configure, and replicate virtual
machines.

How to Install
--------------

0. Install [Vagrant](http://downloads.vagrantup.com/), preferably the
   latest 1.5 version. Less than 1.1 will not work.
1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads).
2. Check out `https://github.com/Automattic/vip-workshop-vm`
3. Run `vagrant up`.
4. Wait :-)
5. Map `10.86.73.81` to `vip-workshop.dev` in your hosts file or in your local
   DNS server.
6. You can now open `http://vip-workshop.dev/` or run `vagrant ssh`

How Does It Work
----------------

Every time the VM is provisioned (on boot or `vagrant reload`, not on
`vagrant resume`), we run Puppet. It installs or updates the
neededs software, copies files to their right places, restarts the
services with the proper configs.

Conventions
-------------------------------------------

* Use `http://vip-workshop.dev/talk-slug` as the URL of your demo sites
* Whenever you need to choose a username and a password, choose
  `wordpress`/`wordpress`.

Host Directory Structure
------------------------
.
├── puppet
│
├── talks – holds all the talk files, synced to `~/.talks` on the VM.
│   └── talk-slug
│
└── www – holds the web interface for `http://vip-workshop.dev/` – talks listing,
         source file highlighting service. Synced to `~/www` on the VM.

Distributing Updates to the Attendee VM
---------------------------------------

We have set up a private gist on Github that the attendees will be able to
access. Since only one person can push to a gist, Josh will need to "deploy"
changes that occur in the private Github repo.

Attendees can get the VM by running

```
git clone https://gist.github.com/??? && vagrant up
```

Attendees can update their VM by running

```
git pull && vagrant provision
```
