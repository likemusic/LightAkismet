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
		public $DefaultCommentValues = array();
		
		/**
		* Intarnal used singleton
		* 
		* @var AkismetServiceSingleton
		*/
		protected $AkismetSingleton;

		const PARAM_NAME_BLOG = 'blog';
		
		/**
		* Constructor
		* 
		* @param string $ApiKey
		* @param string $HttpUserAgent
		* @param string $BlogUrl
		* @param string $DefaultCommentValues
		* @param string $AutoVerifyKey
		* @return AkismetService
		*/
		public function __construct( $ApiKey=null, $HttpUserAgent=null, $BlogUrl=null, $DefaultCommentValues=null, $AutoVerifyKey = false ) {
			$this->setDafaults( $DefaultCommentValues, $BlogUrl, $HttpUserAgent, $ApiKey );
			$this->AkismetSingleton = AkismetServiceSingleton::getInstance();
			
			if( $AutoVerifyKey ) {
				if( !$this->verifyKey() ) throw new Exception(self::EXCEPTION_MESSAGE_VERIFY_KEY_FAIL);
			}
		}
		
		/**
		* Set dafult values for all Akismet variables
		* 
		* @param array|AkismetComment $CommentValues
		* @param string $BlogUrl
		* @param string $HttpUserAgent
		* @param string $ApiKey
		*/
		public function setDafaults( $CommentValues,  $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
			if( $CommentValues !== null ) $this->DefaultCommentValues = $CommentValues;
			if( $BlogUrl !== null ) $this->BlogUrl = $BlogUrl;
			if( $this->BlogUrl === null ) $this->BlogUrl = $DefaultCommentValues[self::PARAM_NAME_BLOG];
			
			if( $HttpUserAgent !== null ) $this->HttpUserAgent = $HttpUserAgent;
			if( $ApiKey !== null ) $this->ApiKey = $ApiKey;
		}
		
		public function verifyKey( $BlogUrl = null, $HttpUserAgent=null, $ApiKey = null ) {
            $this->setParamsVars( $BlogUrl, $HttpUserAgent, $ApiKey );
			$Result = self::verifyKey( $ApiKey, $HttpUserAgent, $BlogUrl );
			return $Result;
		}

		protected function setParamsVars( &$BlogUrl, &$HttpUserAgent, &$ApiKey ) {
			
			if( $BlogUrl===null ) {
				$BlogUrl = $this->BlogUrl;
			}

			if( $HttpUserAgent===null ) {
				$HttpUserAgent = $this->HttpUserAgent;	
			}

			if( $ApiKey===null ) {
				$ApiKey = $this->ApiKey;	
			}
		}
		
		public function submitSpam( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
            $this->setParamsVars( $BlogUrl, $HttpUserAgent, $ApiKey );
			return $this->AkismetSingleton->submitSpam($ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl);
		}

		public function submitHam( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
            $this->setParamsVars( $BlogUrl, $HttpUserAgent, $ApiKey );
			return $this->AkismetSingleton->submitHam( $CommentValues, $BlogUrl, $HttpUserAgent, $ApiKey );
		}

		public function checkComment ( $CommentValues, $BlogUrl=null, $HttpUserAgent=null, $ApiKey=null ) {
            $this->setParamsVars( $BlogUrl, $HttpUserAgent, $ApiKey );
			$Result = $this->AkismetSingleton->checkComment( $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl );
			return $Result;
		}
	}

?>