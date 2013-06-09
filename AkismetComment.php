<?php

	/**
	*  Class Represent All fields sended to akismet service
	*/
	class AkismetComment
	{
		/**
		* Constructor
		* @param array $ArrayValues Associative array with values;
		* @param string $blog The front page or home URL of the instance making the request.
		* @param string $user_ip IP address of the comment submitter.
		* @param string $user_agent User agent string of the web browser submitting the comment.
		* @param string $refferer The content of the HTTP_REFERER header should be sent here.
		* @param string $permalink The permanent location of the entry the comment was submitted to.
		* @param string $comment_type May be blank, comment, trackback, pingback, or a made up value like "registration".
		* @param string $comment_author Name submitted with the comment.
		* @param string $comment_author_email Email address submitted with the comment.
		* @param string $comment_author_url URL submitted with comment.
		* @param string $comment_content The content that was submitted.
		*/
		public function __constructor( $ArrayValues = null, $blog=null, $user_ip=null, $user_agent=null, $refferer=null, $permalink=null, 
			$comment_type=null, $comment_author=null, $comment_author_email=null, $comment_author_url=null,
			$comment_content=null) {

			$this->blog = $blog;
			$this->user_ip = $user_ip;
			$this->user_agent = $user_agent;
			$this->refferer = $refferer;
			$this->permalink = $permalink;
			$this->comment_type = $comment_type;
			$this->comment_author = $comment_author;
			$this->comment_author_email = $comment_author_email;
			$this->comment_author_url = $comment_author_url;
			$this->comment_content = $comment_content;

			if( $ArrayValues !== null ) {
				$this->SetFromArray( $ArrayValues );	
			}
		}

		protected function SetFromArray( $ArrayValues ) {
			foreach ( get_object_vars($this) as $attr_name => $attr_value ) {
				if( ( $this->{$attr_name} === null ) && isset( $ArrayValues[$attr_name] ) ) {
					$this->{$attr_name} = $ArrayValues[$attr_name];
				}
			}
		}

		/**
		* The front page or home URL of the instance making the request.
		* For a blog or wiki this would be the front page.
		* Note: Must be a full URI, including http://.
		* (required)
		* 	
		* @var string
		*/
		public $blog;

		/**
		* IP address of the comment submitter.
		* (required)
		* 
		* @var string
		*/
		public $user_ip;

		/**
		* User agent string of the web browser submitting the comment - 
		* typically the HTTP_USER_AGENT cgi variable.
		* Not to be confused with the user agent of your Akismet library.
		* (required)
		* 
		* @var string
		*/
		public $user_agent;

		/**
		* The content of the HTTP_REFERER header should be sent here.
		* (note spelling)
		* 
		* @var string
		*/
		public $refferer;

		/**
		* The permanent location of the entry the comment was submitted to.
		* 
		* @var string
		*/
		public $permalink;

		/**
		* May be blank, comment, trackback, pingback, or a made up value like "registration".
		* 
		* @var string
		*/
		public $comment_type;

		/**
		* Name submitted with the comment.
		* 
		* @var string
		*/
		public $comment_author;

		/**
		* Email address submitted with the comment.
		* 
		* @var string
		*/
		public $comment_author_email;

		/**
		* URL submitted with comment.
		* 
		* @var string
		*/
		public $comment_author_url;

		/**
		* The content that was submitted.
		* 
		* @var string
		*/
		public $comment_content;
	}
?>
