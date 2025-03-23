#!/usr/bin/perl -wT
use utf8;
use File::Temp qw/tempfile/;
use MIME::Base64 qw/encode_base64 decode_base64/;
use Encode;

$ENV{PATH}="/bin:/usr/bin";

###############################
# Test Values
#
$ENV{"REMOTE_USER"} = "TestGroup3-black";
$ENV{'QUERY_STRING'} = "LL=52%C2%B013%2746%22.78N,002%C2%B012%2734%22.56W";
$ENV{'DOCUMENT_ROOT'}='/var/www/seascouts/html';

################################
# Get system paths from logins etc.
#

# Get path to audio and coffee per seascouts setup allowing for different system setups
my ($docroot) = $ENV{'DOCUMENT_ROOT'} =~ m|(/var/www/.*)|; # Assume docroot is safe
my $pathToAudio = $docroot; $pathToAudio =~ s|/[^/]*/?$|/audio|;
if ( ! -d $pathToAudio ) { die "Can't find Audio $pathToAudio , $docroot"; }
my $coffeedir = $docroot; $coffeedir =~ s|/[^/]*/?$|/coffee|;

my $audiodir = "$docroot/audio";
mkdir("$docroot/audio");

# Get groupname from username (the b64 encoded one)
my ($username,$team) = $ENV{'REMOTE_USER'} =~ /^([A-Za-z0-9]+)(?:-(leaders|black|yellow|silver|blue|pink|purple))$/; # Precise because of taint
my $groupname = eval { do { local $/; open my $fh, '<', "$coffeedir/users/$username" or die $!; <$fh> } } // "none";
($groupname) = $groupname =~ /([A-Za-z0-9+\/=]{4,64})/; 
if ($groupname eq "none") {die "No Group Found $coffeedir/users/$username";}

# Creates a safe (persistant) tmp file like POSIX plan is to move file as required.
sub mktemp {
  my ($fh, $tmpname) = tempfile(SUFFIX => ".wav", UNLINK => 0);
  close ($fh);
  return $tmpname;
}

##################################
# Sanitise HTTP input (we're running in taint mode)
#
my ($q) = $ENV{'QUERY_STRING'} =~ /(?:^|&)LL=([0-9NESW%A-Fa-f,."'° ]+)(?:&|$)/;
$q =~ s/\+/ /g;
$q =~ s/%([0-9A-Fa-f]{2})/pack("C", hex($1))/egu;
$q =~ s/[ ,]//g;
$q = decode('UTF-8', $q);
my ($a) = $q =~ /^([0-9NESW°'".]*)/u;
if ( $a eq "" ) { die "No data! $ENV{'QUERY_STRING'} -> $q"; }
##################################
# Start Audio Processing
#

# Generate Squelch chirrup and 4 mins of white noise if not already there. (Atomic file handling).
if ( ! -f "/tmp/pst.wav" ) {
  my $tmpname=mktemp();
  system("sox","-n","-r","44100","-c","1","$tmpname","synth","0.1","noise","vol","0.8");
  rename("$tmpname","/tmp/pst.wav"); # kernel to handle openfile handle management
}
if ( ! -f "/tmp/white_noise.wav" ) {
  my $tmpname=mktemp();
  system("sox","-n","-r","44100","-c","1","$tmpname","synth","240","noise","vol","0.02");
  rename("$tmpname","/tmp/white_noise.wav"); # kernel to handle openfile handle management
}

# Create Lookup table for valid chars in LatLng
my %lookup=('°' => 'degrees', "'" => 'minutes', '"' => 'seconds', '.' => 'decimal', 'N'=> 'north', 'E' => 'east', 'S' => 'south', 'W' => 'west' );
foreach $d (0..9) { $lookup{"$d"}="$d"; }

########################
# Build LL Audio function
sub LatLngAudio {
  my ($type,$ll)=@_;
  ($type) = $type =~ m/^(jack|mike)$/;
  my @cmds;
  print "$ll\n";
  foreach my $c (split("",$ll)) {
    if ( exists($lookup{"$c"} )) {
      print "$c -> $type-$lookup{$c}.wav\n";
      push(@cmds,"$pathToAudio/$type-$lookup{$c}.wav"); # Audio
      if ($type eq "mike" ) { # Mike Pauses
        if ($lookup{"$c"} =~ /[NESW]/) { push(@cmds,"|sox -n -r 44100 -p synth 0.5 sine 0") } 
      } 
      else { # Jack pauses
        push(@cmds,"|sox -n -r 44100 -p synth ".(($lookup{"$c"} =~ /[NESW]/)?"0.5":"0.2")." sine 0");
      }
    }
  }
  return @cmds;
}

# Build First Part of 
my @cmd=("sox","/tmp/pst.wav","$pathToAudio/pre.wav","|sox -n -r 44100 -p synth 0.2 sine 0"); # First part: pst + Mayday...
push(@cmd,LatLngAudio("jack",$a));  # += Postition
push(@cmd,"$pathToAudio/post.wav"); # += Rest of Jack
push(@cmd,"/tmp/pst.wav");          # += pst
my $JackAudio=mktemp();
push(@cmd,"$JackAudio");
system(@cmd);  #### Jack written to file in /tmp $JackAudio

#############################
# Create hissed version of autio subrouting
#
sub addHiss { # creates new temporary hissfile
  my $Audio=$_[0];
  my $len=`soxi -D "$Audio"`;
  my ($l) = $len =~ /([0-9]+(?:\.\d{1,2})?)/;
  my $s=$l%60;
  my $m=($l-$s)/60;
  my $ll=sprintf("%02d:%02.2f",$m,$s);
  my $hissfile=mktemp();
  system("sox","-m","$Audio","/tmp/white_noise.wav","$hissfile","trim","0",$ll);
  return $hissfile;
}


# Add Background hiss to Jack Audio
my $JackAudioHiss=addHiss($JackAudio);
unlink $JackAudio;
$JackAudio=undef();

######
# Gen Mike Audio
#
@cmd=("sox","/tmp/pst.wav","$pathToAudio/mike-pre.wav","|sox -n -r 44100 -p synth 0.2 sine 0");
push(@cmd,LatLngAudio("mike",$a));
push(@cmd,("$pathToAudio/mike-mid.wav","|sox -n -r 44100 -p synth 0.2 sine 0"));
push(@cmd,LatLngAudio("mike",$a));
push(@cmd,"$pathToAudio/mike-post.wav");
push(@cmd,"/tmp/pst.wav");
my $MikeAudio=mktemp();
push(@cmd,"$MikeAudio");
system(@cmd);

# Add Background hiss to Jack Audio
my $MikeAudioHiss=addHiss($MikeAudio);
unlink $MikeAudio;
$MikeAudio=undef();

system("echo","$JackAudioHiss");
system("echo","$MikeAudioHiss");
system("echo","$audiodir/");
system("echo","$groupname");


system("sox","$JackAudioHiss","|sox -n -r 44100 -p synth 3 sine 0","$MikeAudioHiss","$audiodir/$groupname.mp3");











