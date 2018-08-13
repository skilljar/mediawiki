<?php

require_once __DIR__ . '/Maintenance.php';
require_once __DIR__ .'/../vendor/autoload.php';
use GoogleLogin\GoogleUser;

/*
	This maintenace script queries the Google Admin API for a list of all
	@skilljar.com users.

	It then attempts to create a new Wiki account for each Gmail account in the list.
	Existing counts get skipped.

	If a new account, an entry is made in the user_google_user table (our GoogleLogin 
	MediaWiki extension) so that the Gmail user can log into our wiki via OAuth
	(the only login method we allow)

	Default wiki user permissions are just the 'User' role.
*/

class SyncWikiAccountsWithGmailAccounts extends Maintenance {
	private static $permitRoles = [];  // [ 'sysop', 'bureaucrat', 'bot' ];
	private static $service;

	public function __construct() {
		parent::__construct();

		$client = new Google_Client();
		$client->setApplicationName("MediaWiki");
		$client->setAccessType("offline");

		$data = file_get_contents("credentials.json");		
		$data = str_replace("<CLIENT_SECRET>", getenv('GOOGLE_API_CLIENT_SECRET'), $data);
		file_put_contents("credentials.json", $data);

		$client->setAuthConfig('credentials.json');
		$client->setScopes([Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY]);

		// Load previously authorized credentials from a file.
		$credentialsPath = 'token.json';
		if (file_exists($credentialsPath)) {
		    $accessToken = json_decode(file_get_contents($credentialsPath), true);
		} else {
			// DEBUGGERY:  comment out below block to regen the access token manually from the command line
			$this->output( "Jason's access token has expired.  You'll have to generate a new one manually from the command-line and then update the environment variable JASONS_GOOGLE_ADMIN_API_READONLY_ACCESS_TOKEN");

			exit(1);
			// /DEBUGGERY  

		    // Request authorization from the user.
		    $authUrl = $client->createAuthUrl();
		    printf("Open the following link in your browser:\n%s\n", $authUrl);
		    print 'Enter verification code: ';
		    $authCode = trim(fgets(STDIN));

		    // Exchange authorization code for an access token.
		    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

		    // Check to see if there was an error.
		    if (array_key_exists('error', $accessToken)) {
		        throw new Exception(join(', ', $accessToken));
		    }

		    // Store the credentials to disk.
		    if (!file_exists(dirname($credentialsPath))) {
		        mkdir(dirname($credentialsPath), 0700, true);
		    }
		    file_put_contents($credentialsPath, json_encode($accessToken));
		    printf("Credentials saved to %s\n", $credentialsPath);
		}

		$client->setAccessToken($accessToken);

		// Refresh the token if it's expired.  The file I checked in had the access token removed, so it will always be refreshed at least once.
		if ($client->isAccessTokenExpired()) {
		    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
		    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
		}

		self::$service = new Google_Service_Directory($client);
	}

	public function execute() {
		$newAccountsMade = 0;

		$optParams = array(
		  'customer' => 'my_customer',
		  'maxResults' => 500,
		  'projection' => 'basic',
		  'fields' => 'users(id,name/fullName,primaryEmail)',
		  'orderBy' => 'email',
		);

		foreach( self::$service->users->listUsers($optParams)->users as $user ) {
			$googleUserId = $user->id;
			$email = $user->primaryEmail;
			$username = $user->name->fullName;

			// // TODO: DEBUGGERY, remove eventually
			// if ($email != 'adriel@skilljar.com') {
			// 	continue;
			// }
			// // /TODO DEBUGGERY

			$user = User::newFromName( $username );
			if ( !is_object( $user ) ) {
				$this->output( "invalid username: " . $username . "\n" );
				continue;
			}

			$exists = ( 0 !== $user->idForName() );

			if ( $exists ) {
				$this->output( "Account already exists for: " . $username . "\n" );
				continue;
			}

			// Insert the account into the database
			$user->setEmail( $email );
			$user->setRealName( $username );
			$user->setEmailAuthenticationTimestamp( wfTimestampNow() );  // set their email address as verified, otherwise they can't 
																		 // receive update emails for change to wiki pages on the user's watchlist
			$user->addToDatabase();
			$user->saveSettings();

			GoogleUser::connectWithGoogle( $user, $googleUserId );

			// Increment site_stats.ss_users
			$ssu = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
			$ssu->doUpdate();

			$newAccountsMade++;

			$this->output( "done, created new account for: " . $username . " " . $email . "\n" );

			// not really a necessary step, but I suppose it can't hurt.  Hence why we do it here at the end.
			$password = substr(str_shuffle(MD5(microtime())), 0, 10);  // random password, we don't care, won't surface to the user ever.
			try {
				$status = $user->changeAuthenticationData( [
					'username' => $user->getName(),
					'password' => $password,
					'retype' => $password,
				] );	
			} catch ( PasswordError $pwe ) {
				$this->output( $pwe->getText() );
				continue;
			}			
		}
	}
}

$maintClass = SyncWikiAccountsWithGmailAccounts::class;
require_once RUN_MAINTENANCE_IF_MAIN;

