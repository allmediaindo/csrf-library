<?php
/**
 * LightPHP Framework
 * LitePHP is a framework that has been designed to be lite waight, extensible and fast.
 * 
 * @author Robert Pitt <robertpitt1988@gmail.com>
 * @category core
 * @copyright 2013 Robert Pitt
 * @license GPL v3 - GNU Public License v3
 * @version 1.0.0
 */
class CSRF_Library
{
	protected $session;

	/**
	 * Session key
	 */
	protected $session_identifier = '_csrf_library_token_';

	/**
	 * Get / Post key
	 */
	protected $gp_identifier = '_csrf_gp_token_';

	/**
	 * Session Token
	 */
	protected $_token = null;

	/**
	 * 
	 */
	public function __construct()
	{
		/**
		 * Fetch the session library
		 */
		$this->session = Registry::get("Libraryloader")->session;

		/**
		 * Fetch the session library
		 */
		$this->bcrypt = Registry::get("Libraryloader")->bcrypt;

		/**
		 * Fetch the session library
		 */
		$this->input = Registry::get("Input");

		/**
		 * Generate an access token if we do not have one.
		 */
		if($this->session->exists($this->session_identifier) === false)
		{
			/**
			 * Create a unique identifier using the session id.
			 */
			$this->_token = $this->bcrypt->encrypt($this->session->id(), 1);

			/**
			 * Assign the token to the session
			 */
			$this->session->set($this->session_identifier, $this->_token);
		}

		/**
		 * Return the token from the session and assign in localy
		 */
		$this->_token = $this->session->get($this->session_identifier);
	}

	/**
	 * Generate
	 * @throws Exception If the token is invalid
	 */
	public function validate()
	{
		/**
		 * If we do not have the CSRF token in the storage, throw exception.
		 */
		if($this->session->exists($this->session_identifier) === false)
		{
			throw new Exception("Missing CSRF Token in session");
		}

		/**
		 * Check the input parameters
		 */
		if(!$this->input->get($this->gp_identifier) && !$this->input->post($this->gp_identifier))
		{
			throw new Exception("Missing CSRF Token in request");
		}

		/**
		 * Run a validation
		 */
		if(!$this->bcrypt->validate($this->session->id(), $this->session->get($this->session_identifier)))
		{
			throw new Exception("CSRF Detected.");
		}

		/**
		 * Mark the request as accepted.
		 */
		return true;
	}

	/**
	 * Generate a html block with the key and the
	 */
	public function html()
	{
		return sprintf('<input type="hidden" name="%s" value="%s" />', $this->gp_identifier, $this->_token);
	}

	/**
	 * Return the token for the session
	 */
	public function token()
	{
		return $ths->_token;
	}
}