<?php
# This file was automatically generated by the MediaWiki 1.32.0-alpha
# installer. If you make manual changes, please keep track in case you
# need to recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
	exit;
}


## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = "skilljar";
$wgMetaNamespace = "Skilljar";

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "";
$wgArticlePath = "/wiki/$1";

## The protocol and server name to use in fully-qualified URLs
$wgServer = "https://wiki.skilljar.tech";

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogo = "$wgResourceBasePath/resources/assets/wiki.png";

## UPO means: this is also a user preference option

$wgEnableEmail = true;
$wgEnableUserEmail = true; # UPO

$wgEmergencyContact = "aaron@skilljar.com";

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEnotifMinorEdits = true;
$wgUseEnotif = true;
$wgEmailAuthentication = false;

## Database settings
$url = parse_url("mysql://bf6f433bff2e8e:f08594cb@us-cdbr-iron-east-04.cleardb.net/heroku_7bbd9ac04837b96?reconnect=true"); // parse_url(getenv("CLEARDB_DATABASE_URL"));
$url = parse_url(getenv("DATABASE_URL"));

$wgDBtype = "mysql";
$wgDBserver = $url["host"];
$wgDBname = substr($url["path"], 1);
$wgDBuser = $url["user"];
$wgDBpassword = $url["pass"];

# MySQL specific settings
$wgDBprefix = "";

# MySQL table options to use during installation or update
$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = [];

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = false;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = true;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "C.UTF-8";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = "en";

$wgSecretKey = "c2ec51a5f490e741475c662c7b5261f4fbbc738d55dc81d13382b6590ac8a1c8"; // getenv("MEDIAWIKI_SECRET_KEY");

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = "84b3d286eb67decd"; // getenv("MEDIAWIKI_UPGRADE_KEY");

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

# The following permissions were set based on your choice in the installer
$wgGroupPermissions['*']['createaccount'] = false;
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['read'] = false;

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = "vector";

#
#
# End of automatically generated settings.
# Add more configuration options below.
#
#

wfLoadSkin( 'Vector' );
require_once 'vendor/autoload.php';
require_once "$IP/extensions/GoogleLogin/GoogleLogin.php";

$wgGLSecret = "rIAX6_6SudfIDI9mXufPMO7g"; // getenv('GOOGLE_OAUTH_SECRET');
$wgGLAppId = "537502373301-1uomav7cih58phmemdv1bahg86j046gm.apps.googleusercontent.com"; // getenv('GOOGLE_OAUTH_CLIENT_ID');
$wgGLAllowedDomains = array('skilljar.com', 'localhost');
$wgWhitelistRead = array('Special:GoogleLoginReturn');
$wgShowExceptionDetails = true;

# comment if you need to manually create a new account;
# otherwise, the line below disables email login and forces -only- Google OAuth logins
$wgAuthManagerAutoConfig['primaryauth'] = [];

#-----------------------------------------------------------------

wfLoadExtension( 'ImportUsers' );

#-----------------------------------------------------------------

wfLoadExtension( 'VisualEditor' );

// Enable by default for everybody
$wgDefaultUserOptions['visualeditor-enable'] = 1;

// Optional: Set VisualEditor as the default for anonymous users
// otherwise they will have to switch to VE
// $wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";

// Don't allow users to disable it
$wgHiddenPrefs[] = 'visualeditor-enable';

$wgVirtualRestConfig['modules']['parsoid'] = array(
    // URL to the Parsoid instance
    // Use port 8142 if you use the Debian package
    'url' => 'https://skilljar-wiki-parsoid.herokuapp.com',
    // Parsoid "domain", see below (optional)
    // 'domain' => 'localhost',
    // Parsoid "prefix", see below (optional)
    // 'prefix' => 'localhost'
	// If you run a private wiki then you have to set the following variable to true:
	'forwardCookies' => true
);

#-----------------------------------------------------------------

wfLoadExtension( 'SyntaxHighlight_GeSHi' );

#-----------------------------------------------------------------

$wgUploadDialog['fields'] = array(
	'description' => true,
	'date' => false,
	'categories' => true,
);

#-----------------------------------------------------------------

//// avoid the error message:
//// "Note: Due to technical limitations, thumbnails of high resolution GIF images such as this one will not be animated."

// $wgMaxAnimatedGifArea = 9000000000;

//// Wasn't working.
////
//// "Error creating thumbnail: /app/includes/shell/limit.sh: line 101: 321 Segmentation fault /usr/bin/timeout 
////  $MW_WALL_CLOCK_LIMIT /bin/bash -c "$1" 3>&- Error code: 139"
////

#-----------------------------------------------------------------

# Enable subpages in the main namespace
$wgNamespacesWithSubpages[NS_MAIN] = true;

# Enable subpages in the template namespace
$wgNamespacesWithSubpages[NS_TEMPLATE] = true;

#-----------------------------------------------------------------

# ensure external links open in a new tab, automatically
$wgExternalLinkTarget = '_blank';

#-----------------------------------------------------------------

# super long session expiration
$wgExtendedLoginCookieExpiration = 31536000;  # 1 year in seconds
$wgCookieExpiration = 31536000;

#-----------------------------------------------------------------

$wgNoReplyAddress = 'no-reply-wiki-bot@skilljar.com';
$wgPasswordSender = 'no-reply-wiki-bot@skilljar.com';

$wgSendGridAPIKey = getenv("SENDGRID_API_KEY");
wfLoadExtension( 'SendGrid' );

// $wgSMTP = array(
//    'host' => 'ssl://smtp.gmail.com',
//    'IDHost' => 'gmail.com',
//    'port' => 465,
//    'username' => 'wiki@skilljar.com',  # Now a defunct account, but this is what it'd look like
//    'password' => 'wenakfomiwrjqdwv',   # if you used an email account instead of a faceless one.
//    'auth' => true,
// );

#------------------------------------------------------------------

$wgUploadSizeWarning = 0;  # disable all warnings
$wgMaxUploadSize = 1024 * 1024 * 128;  # 128MB
$wgStrictFileExtensions = false;
$wgCheckFileExtensions = false;

