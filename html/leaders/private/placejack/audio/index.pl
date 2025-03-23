#!/usr/bin/perl -T
use utf8;
use File::Temp qw/tempfile/;
use MIME::Base64 qw/encode_base64 decode_base64/;
use Encode;
use POSIX qw/floor/;

$ENV{PATH}="/bin:/usr/bin";

# Get path to audio and coffee per seascouts setup allowing for different system setups
my ($docroot) = $ENV{'DOCUMENT_ROOT'} =~ m|(/var/www/.*)|; # Assume docroot is safe
my $audioTemplateDir = $docroot; $audioTemplateDir =~ s|/[^/]*/?$|/audio|;
if ( ! -d $audioTemplateDir ) { die "Can't find Audio $audioTemplateDir , $docroot"; }
my $coffeedir = $docroot; $coffeedir =~ s|/[^/]*/?$|/coffee|;

my $audiodir = "$docroot/escape/audio";

my ($tfh) = tempfile(".testXXXXXXXX", DIR=> "$audiodir") or die "Can't write to directory";
close ($tfh);

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

##################
# Get Jack by login name not QS!!
#
sub round {
  my $num = shift;
  my $pl = shift // 0;
  return floor($num * (10**$pl) + 0.5) / (10**$pl);
}

sub toDMS {
  my ($deg,$type)=@_;
  my $h = ( ($type eq "lat") ? (($deg >= 0) ? 'N' : 'S') : (($deg >= 0) ? 'E' : 'W') );
  my $d = floor(abs($deg));
  my $m = floor((abs($deg) - $d) * 60);
  my $sfloat=(abs($deg) - $d - $m/60 ) * 3600;
  my $s = round($sfloat, 2);
  my $sint= floor($sfloat);
  my $decimal = round((($sfloat-$sint)*100),0);
#  return sprintf("%0".(($type eq "lat")?"2":"3")."d°%02d'%02.2f\"%s", $d, $m, $s, $h);
  return sprintf("%0".(($type eq "lat")?"2":"3")."d°%02d'%02d\".%02d%s", $d, $m, $sint, $decimal, $h); # because of daft convention
}
sub toDDM {
  my ($deg,$type)=@_;
  my $h = ( ($type eq "lat") ? (($deg >= 0) ? 'N' : 'S') : (($deg >= 0) ? 'E' : 'W') );
  my $d = floor(abs($deg));
  my $mfloat = (abs($deg) - $d) * 60,2;
  my $m = round($mfloat,2);
  my $mint = floor($mfloat);
  my $decimal = round((($mfloat-$mint)*100),0);
#  return sprintf("%0".(($type eq "lat")?"2":"3")."d°%02.2f'%s", $d, $m, $h);
  return sprintf("%0".(($type eq "lat")?"2":"3")."d°%02d'.%02d%s", $d, $mint, $decimal, $h);
}

my $boundsjson = eval { do { local $/; open my $fh, '<', "$coffeedir/bounds/$groupname.json" or die $!; <$fh> } } // "none";
my $jack;

if ( $boundsjson eq "none" ) {
  $jack="Jack's location is currently not set";
}
else {
  my ($lat,$lng,$len) = $boundsjson =~ /^{"lat":(-?[0-9]+(?:\.[0-9]+)),"lng":(-?[0-9]+(?:\.[0-9]+)),"len":([0-9]+(?:\.[0-9]+))}$/;
  if ($len >20) {
    $jack=toDDM($lat,"lat").toDDM($lng,"lon");
  } else {
    $jack=toDMS($lat,"lat").toDMS($lng,"lon");
  }
}

my ($a) = $jack =~ /^([0-9NESW°'".]*)/u;
if ( $a eq "" ) { die "Badly formated Position $jack -> $a"; }
##################################
# Start Audio Processing
#

# Generate Squelch chirrup and 4 mins of white noise if not already there. (Atomic file handling).
if ( ! -f "/tmp/pst.wav" ) {
  my $tmpname=mktemp();
  my $ret=system("sox","-q","-n","-r","44100","-c","1","$tmpname","synth","0.1","noise","vol","0.8");
  if ($ret != 0 ) {die "Fail1";}
  rename("$tmpname","/tmp/pst.wav"); # kernel to handle openfile handle management
}
if ( ! -f "/tmp/white_noise.wav" ) {
  my $tmpname=mktemp();
  my $ret=system("sox","-q","-n","-r","44100","-c","1","$tmpname","synth","240","noise","vol","0.02");
  if ($ret != 0 ) {die "Fail2";}
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
  foreach my $c (split("",$ll)) {
    if ( exists($lookup{"$c"} )) {
      push(@cmds,"$audioTemplateDir/$type-$lookup{$c}.wav"); # Audio
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
my @cmd=("sox","-q","/tmp/pst.wav","$audioTemplateDir/pre.wav","|sox -n -r 44100 -p synth 0.2 sine 0"); # First part: pst + Mayday...
push(@cmd,LatLngAudio("jack",$a));  # += Postition
push(@cmd,"$audioTemplateDir/post.wav"); # += Rest of Jack
push(@cmd,"/tmp/pst.wav");          # += pst
my $JackAudio=mktemp();
push(@cmd,"$JackAudio");
$ret=system(@cmd);  #### Jack written to file in /tmp $JackAudio
if ($ret != 0 ) {die "Fail3";}

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
  my $ret=system("sox","-q","-m","$Audio","/tmp/white_noise.wav","$hissfile","trim","0",$ll);
  if ($ret != 0 ) {die "Fail4";}
  return $hissfile;
}


# Add Background hiss to Jack Audio
my $JackAudioHiss=addHiss($JackAudio);
unlink $JackAudio;
$JackAudio=undef();

######
# Gen Mike Audio
#
@cmd=("sox","-q","/tmp/pst.wav","$audioTemplateDir/mike-pre.wav","|sox -n -r 44100 -p synth 0.2 sine 0");
push(@cmd,LatLngAudio("mike",$a));
push(@cmd,("$audioTemplateDir/mike-mid.wav","|sox -n -r 44100 -p synth 0.2 sine 0"));
push(@cmd,LatLngAudio("mike",$a));
push(@cmd,"$audioTemplateDir/mike-post.wav");
push(@cmd,"/tmp/pst.wav");
my $MikeAudio=mktemp();
push(@cmd,"$MikeAudio");
$ret=system(@cmd);
if ($ret != 0 ) {die "Fail5";}

# Add Background hiss to Jack Audio
my $MikeAudioHiss=addHiss($MikeAudio);
unlink $MikeAudio;
$MikeAudio=undef();

$ret=system("sox","-q","$JackAudioHiss","|sox -n -r 44100 -p synth 3 sine 0","$MikeAudioHiss","$audiodir/$groupname.mp3");
if ($ret != 0 ) {die "Fail6";}
my ($host) = $ENV{'SERVER_NAME'} =~ /([a-z0-9_.-]*)/;
print "Location: https://$host/leaders/private/\r\n\r\n";

