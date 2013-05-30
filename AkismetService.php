<?php
	
	require_once('AkismetServiceSingleton.php');

	/**
	* Class for interact with Akismet server with preset parameters values
	*/
	class AkismetService
	{
		public $ApiKey;
		public $BlogUrl;
		public $HttpUserAgent;
		public $DefaultValues = array();
		
		protected $AkismetSingleton;


		public function __construct( $ApiKey=null, $HttpUserAgent=null, $BlogUrl=null, $AutoVerifyKey = false ) {
			$this->ApiKey = $ApiKey;
			$this->BlogUrl = $BlogUrl;
			$this->HttpUserAgent = $HttpUserAgent;
			$this->AkismetSingleton = AkismetServiceSingleton::getInstance();
			
			if( $AutoVerifyKey ) {
				if( !$this->verifyKey() ) throw new Exception(self::EXCEPTION_MESSAGE_VERIFY_KEY_FAIL);
			}
		}
		
		public function __construct( AkismetComment $DefaultValues ) {
			$this->DefaultValues = $DefaultValues;
		}

		public function verifyKey( $BlogUrl = null, $HttpUserAgent=null, $ApiKey = null ) {
			if( $BlogUrl===null ) {
				$BlogUrl = $this->BlogUrl;
			}

			if( $HttpUserAgent===null ) {
				$HttpUserAgent = $this->HttpUserAgent;	
			}

			if( $ApiKey===null ) {
				$ApiKey = $this->ApiKey;	
			}

			$Result = self::verifyKey( $ApiKey, $HttpUserAgent, $BlogUrl );
			return $Result;
		}

		public function submitSpam( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
			if( $BlogUrl=== null ) {
				$BlogUrl = $this->BlogUrl;
			}

			if( $HttpUserAgent === null ) {
				$HttpUserAgent = $this->HttpUserAgent;
			}

			if( $ApiKey === null ) {
				$ApiKey = $this->ApiKey;
			}
			
			return AkismetServiceSingleton::getInstance()->submitSpam($ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl);
		}

		public function submitHam( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
			if( $BlogUrl=== null ) {
				$BlogUrl = $this->BlogUrl;
			}

			if( $HttpUserAgent === null ) {
				$HttpUserAgent = $this->HttpUserAgent;
			}

			if( $ApiKey === null ) {
				$ApiKey = $this->ApiKey;
			}
			return self::submitHam( $CommentValues, $BlogUrl, $HttpUserAgent, $ApiKey );
		}

		public function checkComment ( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
			if( $BlogUrl === null) {
				$BlogUrl = $this->BlogUrl;
			}

			if( $HttpUserAgent === null ) {
				$HttpUserAgent = $this->HttpUserAgent;	
			}		

			if( $ApiKey === null) {
				$ApiKey = $this->ApiKey;
			}

			$Result = AkismetServiceSingleton::getInstance()->checkComment( $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl );
			return $Result;
		}
	}

?>