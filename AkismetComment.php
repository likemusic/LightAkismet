<?php

	class AkismetComment
	{
		public function __constructor( $blog=null, $user_ip=null, $user_agent=null, $refferer=null, $permalink=null, 
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
		}
		
		public $blog;
		public $user_ip;
		public $user_agent;
		public $refferer;
		public $permalink;
		public $comment_type;
		public $comment_author;
		public $comment_author_email;
		public $comment_author_url;
		public $comment_content;
	}
?>
