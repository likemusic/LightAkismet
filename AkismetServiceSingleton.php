<?php

	require_once('AkismetComment.php');

	/**
	* Class for interact with Akismet server
	*/
	class AkismetServiceSingleton {
		const SERVER_HOST = 'rest.akismet.com';
		const REST_PATH = '/1.1/';
		const SERVER_PORT = 80;

		const REST_METHOD_NAME_VERIFY_KEY = 'verify-key';
		const REST_METHOD_NAME_COMMENT_CHECK = 'comment-check';
		const REST_METHOD_NAME_SUBMIT_SPAM = 'submit-spam';
		const REST_METHOD_NAME_SUBMIT_HAM = 'submit-ham';

		const RESPONSE_VERIFY_KEY_VALID = 'valid';
		const RESPONSE_VERIFY_KEY_INVALID = 'invalid';
		const RESPONSE_METHOD_CALL_ERROR = 'invalid';
		const RESPONSE_COMMENT_CHECK_TRUE = 'true';
		const RESPONSE_COMMENT_CHECK_FALSE = 'false';
		
		const HTTP_CONTENT_SEPARATOR = "\r\n\r\n";
		const HTTP_FIELDS_SEPARATOR = "\r\n";
		const AKISMET_DEBUG_HELP_HTTP_HEADER_FIRELD = 'X-akismet-debug-help: ';

		const EXCEPTION_MESSAGE_INVALID_HTTP_RESPONSE_CONTENT = 'Invalid http-response content: ';
		const EXCEPTION_MESSAGE_INVALID_AKISMET_FUNCITON_CALL = 'Invalid function call. ResponseBody/HelpMessage: ';
		const EXCEPTION_MESSAGE_VERIFY_KEY_FAIL = 'Verification key in construcor fail.';
        const EXCEPTION_MESSAGE_INVALID_HTTP_RESPONSE_CODE = 'Invalid http-response code. ResponseCode/Response: ';
        
        const VALID_SERVER_RESPONSE_CODE = 200;

		private function __constructor() {}

		// The clone and wakeup methods prevents external instantiation of copies of the Singleton class
		private function __clone() { }  
		private function __wakeup() { } 

		protected static $instance;  //object instance
		/**
		* return an instance of the object
		* @return self
		*/
		public static function getInstance() { 
			if ( is_null(self::$instance) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		* Verifi key for blog
		* Return array with keys:
		* 'IsValid' - bool
		* 'Message' - if IsValid == false - contain 'X-akismet-debug-help' field from http server response
		* 
		* @param string $ApiKey
		* @param string $Blog
		* @param string $HttpUserAgent
		* @return array
		*/
		public function verifyKey( $HttpUserAgent, $ApiKey, $BlogUrl ) {
			$Params = array(
				'key' => $ApiKey,
				'blog' => $BlogUrl
			);

			$Content = http_build_query($Params);

			$RequestPath = self::REST_PATH.self::REST_METHOD_NAME_VERIFY_KEY;
			$Response = self::HttpPost( self::SERVER_HOST, $RequestPath, $HttpUserAgent, $Content );
			$ResponseContent = self::GetHttpResponseContent($Response);

			switch( $ResponseContent )
			{
				case self::RESPONSE_VERIFY_KEY_VALID: 
					$IsValid = true;
					break;
				case self::RESPONSE_VERIFY_KEY_INVALID: 
					$IsValid = false;
					break;
				default: 
					throw new Exception(EXCEPTION_MESSAGE_INVALID_HTTP_RESPONSE_CONTENT.$ResponseContent);
			}

			if( !$IsValid ) {
				$Message = self::GetHelpMessageFromAkismetServerHttpResponse( $Response );	
			} else {
				$Message = null;
			}

			$Result = array (
				'IsValid' => $IsValid,
				'Message' => $Message
			);

			return $Result;
		}

		/**
		* Check comment for spam
		* 
		* @param mixed $ApiKey
		* @param mixed $HttpUserAgent
		* @param mixed $CommentValues
		* @param mixed $BlogUrl
		* @param mixed $Comment
		* @return bool true (if this is spam) or false if it's not. 
		*/
		public function checkComment ( $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl ) {
			$Host = $ApiKey.'.'.self::SERVER_HOST;
			$RequestPath = self::REST_PATH.self::REST_METHOD_NAME_COMMENT_CHECK;

			if( $CommentValues instanceof AkismetComment ) $CommentValues = (array) $CommentValues; 			
			if( $BlogUrl !== null )	{
				$CommentValues['blog'] = $BlogUrl;
			}		

			$Content = http_build_query( $CommentValues );
			$Response = self::HttpPost( $Host, $RequestPath, $HttpUserAgent, $Content );
			$ResponseContent =  self::GetHttpResponseContent( $Response );
			
			switch( $ResponseContent ) {
				case self::RESPONSE_COMMENT_CHECK_TRUE :
					$IsSpam = true;
					break;
				case self::RESPONSE_COMMENT_CHECK_FALSE :
					$IsSpam = false;
					break;
				case self::RESPONSE_METHOD_CALL_ERROR :
					$ServerHelpMessage = self::GetHelpMessageFromAkismetServerHttpResponse( $Response );
					$HelpMessage = self::EXCEPTION_MESSAGE_INVALID_AKISMET_FUNCITON_CALL.$ResponseContent.'/'.$ServerHelpMessage;
					throw new Exception( $HelpMessage );
					break;
				default:
					$ServerHelpMessage = self::GetHelpMessageFromAkismetServerHttpResponse( $Response );
					throw new Exception( self::EXCEPTION_MESSAGE_INVALID_AKISMET_FUNCITON_CALL.$ResponseContent.'/'.$ServerHelpMessage );
					break;	
			}
			return $IsSpam;				
		}

		public function submitSpam ( $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl ) {
			return self::submitComment( self::REST_METHOD_NAME_SUBMIT_SPAM, $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl );
		}

		public function submitHam ( $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl ) {
			return self::submitComment( self::REST_METHOD_NAME_SUBMIT_HAM, $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl );
		}

		protected function submitComment( $RestMethodName, $ApiKey, $HttpUserAgent, $CommentValues, $BlogUrl = null ) {
			$Host = $ApiKey.'.'.self::SERVER_HOST;
			$RequestPath = self::REST_PATH.$RestMethodName;

			if( $BlogUrl !== null )	{
				$CommentValues['blog'] = $BlogUrl;
			}		

			$Content = http_build_query( $CommentValues );

			$Response = self::HttpPost( $Host, $RequestPath, $HttpUserAgent, $Content );
			
			$ResponceCode = self::GetHttpResponseCode( $Response );
			if( $ResponceCode !== self::VALID_SERVER_RESPONSE_CODE ) {
				$Message = self::EXCEPTION_MESSAGE_INVALID_HTTP_RESPONSE_CODE.$ResponceCode.'/'.$Response;
				throw new Exception( $Message );					
			}
			return; 
		}

		protected function HttpPost( $Host, $Path, $UserAgent, $Content ) {
			$ContentLength = strlen($Content);

			$HttpRequest  = "POST {$Path} HTTP/1.0\r\n";
			$HttpRequest .= "Host: $Host\r\n";
			$HttpRequest .= "User-Agent: {$UserAgent}\r\n";
			$HttpRequest .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$HttpRequest .= "Content-Length: {$ContentLength}\r\n";
			$HttpRequest .= "\r\n";
			$HttpRequest .= $Content;

			$Port = self::SERVER_PORT;
			$Response = $this->SendRequestAndGetResponse( $Host, $Port, $HttpRequest );
			return $Response;
		}

		protected function SendRequestAndGetResponse( $Host, $Port, $Request ) {
			$Response = '';
			$fs = fsockopen( $Host, $Port, $errno, $errstr, 10 );
			if( false != $fs ) {
				fwrite( $fs, $Request );
				while ( !feof( $fs ) )
					$Response .= fgets( $fs, 1160 ); // One TCP-IP packet
				fclose( $fs );
			}
			return $Response;
		}

		protected function GetHttpResponseContent( $HttpResponse ) {
			$Pos = strpos( $HttpResponse,self::HTTP_CONTENT_SEPARATOR );
			$Content = substr( $HttpResponse, $Pos + strlen( self::HTTP_CONTENT_SEPARATOR ) );
			return $Content;	
		}

		protected function GetHelpMessageFromAkismetServerHttpResponse( $AkismetServerHttpResponse ) {
			$HelpMessage = self::GetTextBetween( $AkismetServerHttpResponse, self::AKISMET_DEBUG_HELP_HTTP_HEADER_FIRELD, self::HTTP_FIELDS_SEPARATOR );
			return $HelpMessage;
		}

		protected function GetTextBetween( $Haystack, $StartMarker, $StopMarker, $Offset = 0 ) {
			$StartPos = strpos( $Haystack, $StartMarker, $Offset ) + strlen($StartMarker);
			$EndPos = strpos( $Haystack, $StopMarker, $StartPos );
			$Lengh = $EndPos - $StartPos;
			$Text = substr( $Haystack, $StartPos, $Lengh);
			return $Text;
		}
		
		protected function GetHttpResponseCode( $HttpResponse ) {
			//HTTP/1.1 200 OK\r\n
			$Pos = strpos( $HttpResponse, "\r\n" );
			$FirstRow = substr( $HttpResponse, 0, $Pos );
			$Code = self::GetTextBetween( $FirstRow, ' ', ' ');
			settype( $Code, 'integer' );
			return $Code;
		}
	}
?>
