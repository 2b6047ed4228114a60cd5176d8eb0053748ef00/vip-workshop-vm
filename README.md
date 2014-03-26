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
   latest 1.[1-9] version. It might work on 1.0.x, but I haven't tested.
1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads).
2. Check out `https://wpcom.svn.automattic.com/the-pit-of-despair/vip-workshop/2013`
3. Go into the `2013` directory and run: `vagrant up`.
4. Wait :-)
5. Map `192.168.84.92` to `vip.dev` in your hosts file or in your local
   DNS server.
6. You can now open `http://vip.dev/` or run `vagrant ssh`

How Does It Work
----------------

Every time the VM is provisioned (on boot or `vagrant reload`, not on
`vagrant resume`), we run `bin/provision.sh`. It installs or updates the
neededs software, copies files to their right places, restarts the
services with the proper configs.

We let each talk to provision itself by automatically loading
`talks/*/provision.sh` in the end of the provision phase.

Conventions
-------------------------------------------

* Use `http://vip.dev/talk-slug` as the URL of your demo sites
* Whenever you need to choose a username and a password, choose
  `vip`/`vip`.

Host Directory Structure
------------------------
.
├── bin
│
├── config – here we keep configuration files, synced to `~/.config` on
│            the VM. Most of them are symlinked to their proper places.
│
├── talks – holds all the talk files, synced to `~/.talks` on the VM.
│   └── talk-slug
│       └── provision.sh – sets up the talk: copies slides, installs/
│                          imports WordPress, copies plugins/themesm
│                          copies sample files. All the needed files
│                          should live in this directory.
│
├── updates – the source for hotxifes after we distributed the VM. See
│             below for more information.
│
└── ui – holds the web interface for `http://vip.dev/` – talks listing,
         source file highlighting service. Synced to `/.ui/` on the VM.

VM Directory Structure
----------------------
~
├── .dist – base directory for software installation and common files
│
└── www – the webroot, its index lists the talks, each talk has its own
          directory

Attendee VM
-----------

Unfortunately in this form the VM isn't a good fit for distribution:

* It needs a lot of fragile provisioning: debian modules are installed
  and updated, WordPress is `svn up`ped, talk files are copied around.
* A lot of the talk files aren't directly synced to the VM and we can't
  make live changes. We can't ask attendees to change a file and then
  wait for all the provisioning.
* There are a lot of extra files they attendees don't care about and
  don't need them in the host directory – nginx configs, SQL imports.

To fix those problems, we build a derivative VM from the development
one, which is more attendee-friendly:

* It has all the files in the base box.
* Provisioning is minimal – starts the services.
* It has the web root synced to `www/` on the host. Doing exercises live
  is not very easy.
* Since the talk files and scripts are provisioned in the base box, we
  can't easily make changes after we've distributed the VM. For example,
  if we'd like to change the SQL import file for the demo WP install of
  a talk we can't just give them the new SQL file and a changed provision.sh
  file. That's why we need a bit more elaborate updates system, which is
  explained in the section "Distributing Updates".

Building & Distributing the Attendee VM
---------------------------------------

Should be as easy as `bin/build-attendee-vm.sh`. It takes around 10 minutes.

The result is the `attendee-vm` directory. It should contain a `www/`
directry and a `vip-workshop-2013.box` file.

You can test it by running `vagrant up`. If you use the same directory
fr testing, don't forget to remove the `.vagrant` direcotory before
archiving and distributing it.

It's common to put an `install/` folder with the latest installation
packages of VirtualBox and Vagrant for several operating systems.

Distributing Updates to the Attendee VM
---------------------------------------

At its provisioning stage the attendee VM looks for files named
`update-\d.zip` in the `updates/` directory on the host (synced to
`~/.updates/` on the VM).

If an update hasn't been already provisioned (listed in
`~/.installed-updates` on the VM), it’s deflated and the file
`provision.sh` is run.

This way we can modify the VM we firts distributed in any way we want –
we can copy more files, we can import SQL, we can add new software,
anything. Attendees just need to put the zip file in the `updates/`
direcotry and run `vagrant reload`.
