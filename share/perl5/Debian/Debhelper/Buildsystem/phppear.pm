# A debhelper build system class for building PHP PEAR based
# projects.
#
# Copyright: © 2011 Mathieu Parent
# License: GPL-2+

package Debian::Debhelper::Buildsystem::phppear;

use strict;
use warnings;
use Cwd ();
use File::Spec;
use Debian::Debhelper::Dh_Lib qw(error complex_doit);
use base "Debian::Debhelper::Buildsystem::autoconf";

sub DESCRIPTION {
	"PHP PEAR (package.xml)"
}

sub new {
	my $class=shift;
	my $this=$class->SUPER::new(@_);
	$this->{pkgtools_cmd} = ['/usr/bin/pkgtools'];
	# Out of source tree building is prefered.
	$this->prefer_out_of_source_building(@_);
	return $this;
}

sub testmode {
	my $this=shift;
	my $sourcedir=shift;
	$this->{pkgtools_cmd} = ['/usr/bin/php',
		'-d', "include_path=$sourcedir/share/php",
		"$sourcedir/bin/pkgtools"];
}

sub check_auto_buildable {
	my $this=shift;
	return 1 if -e $this->get_sourcepath("package.xml");
	return 1 if -e $this->get_sourcepath("package2.xml");
	return 1 if -e $this->get_sourcepath("channel.xml");
	return 0;
}

# Local functions
sub _shell_exec {
	my $child_pid = open(my $output, "-|", @_) // error("@_ failed to fork: $!");
	if ($child_pid) {
		waitpid $child_pid, 0;
	} else {
		exit 0;
	}
	if ($? == -1) {
		error("@_ failed to execute: $!");
	}
	elsif ($? & 127) {
		error("@_ died with signal ".($? & 127));
	}
	elsif ($? != 0) {
		error("@_ returned exit code ".($? >> 8));
	}
	return $output;
}

sub _pkgtools {
	my $this=shift;
	unshift(@_, @{ $this->{pkgtools_cmd} });
	my $results = _shell_exec(@_);
	my $result = <$results>;
	close $results;
	return $result;
}


# Get peardir (relative to sourcedir)
sub _get_peardir {
	my $this=shift;
	return $this->get_sourcedir()."/".$this->{phppear_name}."-".$this->{phppear_version};
}

sub _get_mainpackage {
	my $packages = _shell_exec('dh_listpackages');
	my $package = <$packages>;
	close $packages;
	# Strip newline
	$package =~ s/\s*$//;
	return $package;
}

sub _install_new_files {
	my $this=shift;
	my $old_dir = shift;
	my $new_dir = shift;
	my $target = shift;
	my %old_files = {};
	if (-d $old_dir) {
		opendir(my $old_dh, $old_dir) || error("can't opendir $old_dir: $!");
		%old_files = map { $_ => 1 } grep( $_ ne "." && $_ ne "..", readdir($old_dh));
		closedir $old_dh;
	}
	opendir(my $new_dh, $new_dir) || error("can't opendir $new_dir: $!");
	my %new_files = map { $_ => 1 } grep( $_ ne "." && $_ ne "..", readdir($new_dh));
	closedir $new_dh;
	for (sort keys %new_files) {
		my $old = "$old_dir/$_";
		my $new = "$new_dir/$_";
		my $subtarget = "$target/$_";
		if (-d $new) {
			$this->_install_new_files( $old, $new, $subtarget );
		} elsif( !$old_files{$_} ) {
			if (! -d $new) {
				$this->doit_in_sourcedir("/usr/bin/install",
					 "-T", "-D", "--mode=0644",
					$new, $subtarget);
			}
		}
	}
}

sub _pear_channel_add {
	my $this=shift;
	my $destdir=shift;
	my $builddir=$this->get_builddir() || ".";
	# Create a new PEAR Registry, without ...
	$this->doit_in_sourcedir("/usr/bin/pear",
		"-d", "php_dir=$builddir/without",
		"list-channels");
	# ... and with the channel installed
	$this->doit_in_sourcedir("/usr/bin/pear",
		"-c", "$builddir/.pearrc",
		"-d", "php_dir=$builddir/with",
		"channel-add",
		$this->get_sourcedir()."/channel.xml");
	# Install channel specific files
	$this->_install_new_files("$builddir/without", "$builddir/with", "$destdir/usr/share/php")
}

sub _pear_install {
	my $this=shift;
	my $destdir=shift;
	my @params=@_;
	$this->doit_in_sourcedir("/usr/bin/pear",
		"-c", "debian/pearrc", # Allows local override
		"-d", "download_dir=/tmp",
		"-d", "include_path=/usr/share/php",
		"-d", "php_bin=/usr/bin/php",
		"-d", "bin_dir=/usr/bin",
		"-d", "php_dir=/usr/share/php",
		"-d", "data_dir=/usr/share/php/data",
		"-d", "doc_dir=/usr/share/doc/".$this->_get_mainpackage(),
		"-d", "test_dir=/usr/share/php/tests",
		"install",
		"--offline",
		"--nodeps",
		"-P", $destdir,
		@params,
		$this->_get_peardir()."/package.xml"
	);


}

sub _set_sourcedir {
	my $this=shift;
	my $sourcedir=shift;
	# Get relative sourcedir abs_path (without symlinks)
	my $abspath = Cwd::abs_path($sourcedir);
	if (! -d $abspath || $abspath !~ /^\Q$this->{cwd}\E/) {
		error("invalid or non-existing path to the source directory: ".$sourcedir);
	}
	$this->{sourcedir} = File::Spec->abs2rel($abspath, $this->{cwd});
}

sub pre_building_step {
	my $this=shift;
	my ($step)=@_;
	if (
		(-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml"))
		&& -e $this->get_sourcepath("channel.xml")
	) {
		error("Package can contain only one of package.xml or channel.xml, got both");
	}
	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if (!$this->{phppear_name}) {
			$this->{phppear_name} = $this->_pkgtools('-v', 'phppear', 'name');
		}
		if (!$this->{phppear_version}) {
			$this->{phppear_version} = $this->_pkgtools('-v', 'phppear', 'version');
		}
		if (!$this->{phppear_packagetype}) {
			$this->{phppear_packagetype} = $this->_pkgtools('-v', 'phppear', 'packagetype');;
		}
		if ($this->{phppear_packagetype} !~ /^(php|extsrc|zendextsrc)$/) {
			error('PEAR package type not supported: "'.$this->{phppear_packagetype}.'"');
		}
	}
}

sub configure {
	my $this=shift;
	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if (-e $this->get_sourcepath("package2.xml")) {
			$this->doit_in_sourcedir("cp", "package2.xml", $this->_get_peardir()."/package.xml");
		} else {
			$this->doit_in_sourcedir("cp", "package.xml", $this->_get_peardir()."/package.xml");
		}
		# Remove md5sums and sha1sums to allow patching
		$this->doit_in_sourcedir('sed', '-i',
			'-e', 's/md5sum="[^"]*"//',
			'-e', 's/sha1sum="[^"]*"//',
			$this->_get_peardir()."/package.xml");
		if (($this->{phppear_packagetype} eq 'extsrc') || ($this->{phppear_packagetype} eq 'zendextsrc')) { # PECL
			$this->_set_sourcedir($this->_get_peardir());
			$this->doit_in_sourcedir('phpize');
			$this->SUPER::configure(@_);
			$this->_set_sourcedir('.');
		}
	}
}

sub build {
	my $this=shift;
	$this->mkdir_builddir();
	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if (($this->{phppear_packagetype} eq 'extsrc') || ($this->{phppear_packagetype} eq 'zendextsrc')) { # PECL
			$this->_set_sourcedir($this->_get_peardir());
			$this->SUPER::build();
			$this->_set_sourcedir('.');
		}
	}
}

sub install {
	my $this=shift;
	my $destdir=shift;

	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if ($this->{phppear_packagetype} =~ /^(php|extsrc)$/) {
			if ($this->{phppear_packagetype} eq 'extsrc') { # PECL
				$ENV{'INSTALL_ROOT'} = $destdir;
				$this->_pear_install($destdir, '--nobuild');
				$this->_set_sourcedir($this->_get_peardir());
				$this->SUPER::install($destdir);
				$this->_set_sourcedir('.');
			} else {
				$this->_pear_install($destdir);
			}
			# remove unwanted files
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/tmp");
			$this->doit_in_sourcedir("rm", "-f", $destdir."/usr/share/php/.filemap");
			$this->doit_in_sourcedir("rm", "-f", $destdir."/usr/share/php/.lock");
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/php/.channels");
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/php/.depdblock");
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/php/.depdb");
			$this->doit_in_sourcedir("rmdir", "--ignore-fail-on-non-empty", $destdir."/usr/share/php/.registry/.channel.pecl.php.net");
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/php/.registry/.channel.doc.php.net");
			$this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/php/.registry/.channel.__uri");
			# workaround pear install which will copy docs file into a subdir
			if (-d $destdir."/usr/share/doc/".$this->_get_mainpackage()."/".$this->{phppear_name}) {
			    $this->doit_in_sourcedir("cp", "-r", $destdir."/usr/share/doc/".$this->_get_mainpackage()."/".$this->{phppear_name}."/.", $destdir."/usr/share/doc/".$this->_get_mainpackage()."/.");
			    $this->doit_in_sourcedir("rm", "-rf", $destdir."/usr/share/doc/".$this->_get_mainpackage()."/".$this->{phppear_name});
			}
			# delete COPYING and LICENSE files and prune empty directories
			if (-d $destdir."/usr/share/doc/") {
				$this->doit_in_sourcedir("find", $destdir."/usr/share/doc/", "-type", "f", "-name", "COPYING", "-delete");
				$this->doit_in_sourcedir("find", $destdir."/usr/share/doc/", "-type", "f", "-name", "LICENSE", "-delete");
				$this->doit_in_sourcedir("find", $destdir."/usr/share/doc/", "-type", "f", "-empty", "-delete");
				$this->doit_in_sourcedir("find", $destdir."/usr/share/doc/", "-type", "d", "-empty", "-delete");
			}
			# Remove tests files
			if (!$ENV{PHPPEAR_KEEP_TESTS}) {
				$this->doit_in_sourcedir('rm', '-rf', $destdir.'/usr/share/php/tests');
			}

			# add package.xml and changelog to doc dir
			$this->doit_in_sourcedir("mkdir", "-p", $destdir."/usr/share/doc/".$this->_get_mainpackage());
			$this->doit_in_sourcedir("cp", "package.xml", $destdir."/usr/share/doc/".$this->_get_mainpackage());
			if (-e $this->get_sourcepath("package2.xml")) {
				$this->doit_in_sourcedir("cp", "package2.xml", $destdir."/usr/share/doc/".$this->_get_mainpackage());
			}
			complex_doit(join(' ', @{$this->{pkgtools_cmd}})." --sourcedirectory ".$this->get_sourcedir()." phppear changelog > ".$destdir."/usr/share/doc/".$this->_get_mainpackage()."/changelog");
		}
	}
	if (-e $this->get_sourcepath("channel.xml")) {
		$this->_pear_channel_add($destdir);
	}
}

sub test {
	my $this=shift;
	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if (($this->{phppear_packagetype} eq 'extsrc') || ($this->{phppear_packagetype} eq 'zendextsrc')) { # PECL
			$ENV{'NO_INTERACTION'} = '1';
			$this->SUPER::test();
		}
	}
}

sub clean {
	my $this=shift;
	if (-e $this->get_sourcepath("package.xml") || -e $this->get_sourcepath("package2.xml")) {
		if (($this->{phppear_packagetype} eq 'extsrc') || ($this->{phppear_packagetype} eq 'zendextsrc')) { # PECL
			$this->_set_sourcedir($this->_get_peardir());
			$this->SUPER::clean();
			$this->doit_in_sourcedir('phpize', '--clean');
			$this->_set_sourcedir('.');
		}
		$this->doit_in_sourcedir("rm", "-f", $this->_get_peardir()."/package.xml");
	}
	$this->rmdir_builddir();
}

1
