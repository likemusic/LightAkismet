<?php

	require_once('AkismetServiceSingleton.php');

	/**
	* Class for interact with Akismet server with preset parameters values
	*/
	class AkismetService
	{
		/**
		* The API key being verified for use with the API
		* @link http://akismet.com/development/api/#verify-key
		* @var string
		*/
		public $Key;

		/**
		* Http-request header value of UserAgent field.
		* Must be in format: "{Application Name}/{Version} | {Plugin Name}/{Version}"
		* for excample "WordPress/3.1.1 | Akismet/2.5.3".
		* @link http://akismet.com/development/api/#getting-started
		* @var string
		*/
		public $HttpClientUserAgent;

		/**
		* The front page or home URL of the instance making the request.
		* For a blog or wiki this would be the front page.
		* Note: Must be a full URI, including http://.
		* 
		* @var string
		*/
		public $Blog;

		/**
		* Dafault comments propetries values
		* 
		* @var AkismetComment
		*/
		protected $DefaultCommentValues;

		/**
		* Internal used singleton
		* 
		* @var AkismetServiceSingleton
		*/
		protected $AkismetSingleton;

		/**
		* Constructor
		* 
		* @param string $Key The API key being verified for use with the API.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param array|AkismetComment $DefaultCommentValues Default comments propetries values
		* @param string $AutoVerifyKey - Enable/Disable api key verification in constructor
		* @return AkismetService
		*/
		public function __construct( $Key=null, $HttpUserAgent=null, $Blog=null, $DefaultCommentValues=null, $AutoVerifyKey = false ) {
			$this->setDefaults( $DefaultCommentValues, $Blog, $HttpUserAgent, $Key );
			$this->AkismetSingleton = AkismetServiceSingleton::getInstance();

			if( $AutoVerifyKey ) {
				if( !$this->verifyKey() ) throw new Exception(self::EXCEPTION_MESSAGE_VERIFY_KEY_FAIL);
			}
		}

		/**
		* Set dafult values for all Akismet variables
		* 
		* @param AkismetComment $CommentValues Comments propetries values.
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Key The API key being verified for use with the API
		*/
		public function setDefaults( AkismetComment $CommentValues=null,  $Blog=null, $HttpUserAgent=null, $Key=null ) {
			if( $CommentValues !== null ) $this->setDefaultCommentValues( $CommentValues );
			
			if( $Blog !== null ) $this->Blog = $Blog;
			if( $HttpUserAgent !== null ) $this->HttpClientUserAgent = $HttpUserAgent;
			if( $Key !== null ) $this->Key = $Key;
		}
		
		public function setDefaultCommentValues( AkismetComment $CommentValues ) {
			$this->DefaultCommentValues = $CommentValues;	
			if( $CommentValues->blog !== null ) {
				$this->Blog = $CommentValues->blog;
			}
		}
		
		public function getDefaultCommentValues() {
			return $this->DefaultCommentValues; 
		}

		/**
		* Verify Api key
		* @link http://akismet.com/development/api/#verify-key
		* 
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Key The API key being verified for use with the API.
		*/
		public function verifyKey( $Blog = null, $HttpUserAgent=null, $Key = null ) {
			$this->setParamsVars( $Blog, $HttpUserAgent, $Key );
			$Result = self::verifyKey( $Key, $HttpUserAgent, $Blog );
			return $Result;
		}

		/**
		* Check comment.
		* @link http://akismet.com/development/api/#comment-check
		* 
		* @param array|AkismetComment $CommentValues  Comments propetries values.
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Key The API key being verified for use with the API
		* @return bool
		*/
		public function checkComment ( $CommentValues, $Blog=null, $HttpUserAgent=null, $Key=null ) {
			$this->setParamsVars( $Blog, $HttpUserAgent, $Key );
			$Result = $this->AkismetSingleton->checkComment( $Key, $HttpUserAgent, $CommentValues, $Blog );
			return $Result;
		}


		/**
		* Submit spam.
		* @link http://akismet.com/development/api/#submit-spam
		* 
		* @param array|AkismetComment $CommentValues  Comments propetries values.
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Key The API key being verified for use with the API
		*/
		public function submitSpam( $CommentValues, $Blog=null, $HttpUserAgent=null, $Key=null ) {
			$this->setParamsVars( $Blog, $HttpUserAgent, $Key );
			return $this->AkismetSingleton->submitSpam($Key, $HttpUserAgent, $CommentValues, $Blog);
		}

		/**
		* Submit Ham
		* @link http://akismet.com/development/api/#submit-ham
		* 
		* @param array|AkismetComment $CommentValues  Comments propetries values.
		* @param string $Blog The front page or home URL of the instance making the request.
		* @param string $HttpUserAgent Http-request header value of UserAgent field.
		* @param string $Key The API key being verified for use with the API
		*/
		public function submitHam( $CommentValues, $Blog=null, $HttpUserAgent=null, $Key=null ) {
			$this->setParamsVars( $Blog, $HttpUserAgent, $Key );
			return $this->AkismetSingleton->submitHam( $CommentValues, $Blog, $HttpUserAgent, $Key );
		}


		/**
		* Set result values for variables that will be passed to singleton methods
		* 
		* @param string $Blog
		* @param string $HttpUserAgent
		* @param string $Key
		*/
		protected function setParamsVars( &$Blog, &$HttpUserAgent, &$Key ) {

			if( $Blog===null ) {
				$Blog = $this->Blog;
			}

			if( $HttpUserAgent===null ) {
				$HttpUserAgent = $this->HttpClientUserAgent;	
			}

			if( $Key===null ) {
				$Key = $this->Key;	
			}
		}
	}

?>