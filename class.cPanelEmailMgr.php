<?php
/**
 * class.cpanelEmailMgr.php
 * 
 * This class provides methods to create and delete email accounts by using cPanel's
 * built-in functionality.
 * 
 * Usage example:
 * 
 * [begin file]  example.php
 * 
 * <code>
 * 
 * <?php
 * 
 * require_once('class.cPanelEmail.php');
 * 
 * $args['cpUser'] = 		'username';  	// Your cPanel username
 * $args['cpPassword'] = 	'pass12345';  	// Your cPanel password
 * $args['cpDomain'] = 		'example.com';  // Your domain name (sans the www.)
 * $args['cpSkin'] = 		'x3';  			// Skin version of your cPanel install
 * $args['useHttps'] = 		true;  			// Whether or not to use https://
 *
 * $cPanelEmailMgr = new cPanelEmailMgr($args);
 * 
 * //  Create 'testuser@example.com' with a mailbox diskspace quota of 20MB
 * if(!$cPanelEmailMgr->createAccount('test','example.com',20)) {
 * 		die('could not create account');
 * }
 * 
 * //  Delete the 'testuser@example.com' email account
 * if(!$cPanelEmailMgr->deleteAccount('test','example.com')) {
 * 		die('could not delete account');
 * }
 * 
 * ?>
 * 
 * </code>
 * 
 * [end file]
 * 
 * 
 * REQUIREMENTS
 * 
 * - PHP5 or greater
 * - PHP must not be running in safe mode
 * 
 * RECOMMENDATIONS
 * 
 * - It is recommended that $args['useHttps'] be set to TRUE;
 * 
 * FAQ's
 * 
 * - Q: 'What skin should I choose?', 
 * 	 A:  check out this article: http://www.zubrag.com/articles/determine-cpanel-skin.php
 * - Q: 'Where can I get more help with this class?',
 *   A:  http://www.brycemickler.com/forums/
 * 
 * This class is based on the scripts authored by Md. Zakir Hossain (Raju) 
 * (http://www.rajuru.xenexbd.com) which, in-turn, were based on the scripts from
 * http://www.zubrag.com/scripts/cpanel-create-email-account.php.
 * 
 * @version 0.9 beta
 * @author Bryce Mickler <bryce@brycemickler.com>
 * @copyright 2009
 * @license http://www.freebsd.org/copyright/freebsd-license.html Free BSD License
 * @link http://www.brycemickler.com/
 * @link http://www.rajuru.xenexbd.com/
 * @link http://www.zubrag.com/
 * @todo verify that this works with cPanel skins other than x, x2, and x3
 */
/**
 * Class to create and delte email accounts through cPanel
 *
 */
class cPanelEmailMgr
{
	/**
	 * cPanel account username
	 *
	 * @var string
	 */
	private $cpUser;
	
	/**
	 * cPanel account password
	 *
	 * @var string
	 */
	private $cpPassword;
	
	/**
	 * cPanel account domain name
	 * just the domain and top level domain name.  For example, the site located
	 * at http://www.google.com would enter 'google.com' as the domain name.
	 *
	 * @var string
	 */
	private $cpDomain;
	
	/**
	 * cPanel skin version
	 * Some possible options include:
	 * - x
	 * - x2
	 * - x3
	 * - rvblue
	 * - and others...
	 *
	 * @var string
	 * @link http://www.zubrag.com/articles/determine-cpanel-skin.php
	 */
	private $cpSkin;
	
	/**
	 * Whether to use https or http
	 *
	 * @var string
	 */
	private $protocol;
	
	/**
	 * Port used to access the cPanel pages
	 * Usually 2083 for https:// and 2082 for http://
	 * @var int
	 */
	private $port;

	/**
	 * Constructor for the cPanelEmailMgr class
	 *
	 * @param array $args
	 */
	public function __construct($args)
	{
		$this->cpUser = 		$args['cpUser'];
		$this->cpPassword = 	$args['cpPassword'];
		$this->cpDomain = 		$args['cpDomain'];
		$this->cpSkin = 		$args['cpSkin'];
		$this->protocol = 		($args['useHttps'] == true) ? 'https' : 'http';
		$this->port = 			($args['useHttps'] == true) ? '2083' : '2082';
	}

	/**
	 * Creates an email account
	 * Creates an email account, assigns a password and mail quota
	 *
	 * @param string $accountName
	 * @param string $accountPassword
	 * @param integer $accountQuota
	 * @return bool
	 */
	function createAccount($accountName, $accountPassword, $accountQuota)
	{
		$url = $this->protocol . '://' 
			. $this->cpUser . ':' 
			. $this->cpPassword . '@'
			. $this->cpDomain . ':'
			. $this->port . '/frontend/'
			. $this->cpSkin . '/mail/doaddpop.html?'
			. 'quota='. $quota 
			. '&email=' . $accountName
			. '&domain=' . $this->cpDomain
			. '&password=' . $accountPassword;
		
		if($f != fopen($url,"r")) 
		{
			/**
			 * Cannot create email account. Possible reasons: "fopen" function 
			 * not allowed on your server, PHP is running in SAFE mode');
			 */
			return false;
		}

		while (!feof ($f))
		{
			$line = fgets ($f, 1024);
			
			//  Does this account already exist?
			if (ereg ("already exists!", $line, $out))  
			{
				//  this email account already exists
				return false;
			}
		}
		
		fclose($f);
		return true;
	}

	/**
	 * Deletes an email account
	 *
	 * @param string $accountName
	 * @param string $accountDomain
	 * @return bool
	 */
	public function deleteAccount($accountName, $accountDomain)
	{
		$url = $this->protocol . '://' 
			. $this->cpUser . ':' 
			. $this->cpPassword . '@'
			. $this->cpDomain . ':'
			. $this->port . '/frontend/'
			. $this->cpSkin . '/mail/realdelpop.html?'
			. '&email=' . $accountName
			. '&domain=' . $accountDomain;

		if($f != @fopen($url)) 
		{
			/**
			 * Could delete email account. Possible reasons: "fopen" function 
			 * not allowed on your server, PHP is running in SAFE mode');
			 */
			return false;
		}
		
		fclose($f);
		return true;
	}
}
?>