<?php

/**
 * Informacje na temat danych na 1 blankiecie
 * @author alex
 */

class BankTransferBlanketInfo {

	/**
	 * Dane odbiorcy
	 * @var string
	 */

	protected $toInfo;

	/**
	 * Numer konta
	 * @var string
	 */

	protected $bankAccountNumber;

	/**
	 * Kwota
	 * @var string
	 */

	protected $amount;

	/**
	 * Informacje nadawcy
	 * @var string
	 */

	protected $fromInfo;

	/**
	 * Tytuł płatności
	 * @var string
	 */

	protected $title;

	/**
	 * Kwota słownie
	 * @var string
	 */

	protected $amountInWords;

	/**
	 * @param string $toInfo - informacje o nadawcy
	 * @param string $bankAccountNumber - numer konta
	 * @param string $amount - kwota
	 * @param string $fromInfo - informacje odbiorcy
	 * @param string $title - tytuł płatności
	 * @param string $amountInWords - kwota słownie
	 */

	function __construct($toInfo, $bankAccountNumber, $amount, $fromInfo, $title, $amountInWords = '') {
		$this->toInfo = $toInfo;
		$this->bankAccountNumber = $bankAccountNumber;
		$this->amount = $amount;
		$this->fromInfo = $fromInfo;
		$this->title = $title;
		$this->amountInWords = $amountInWords;
	}

	/**
	 * Zwraca kwoty
	 * @return string
	 * @author alex
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * Zwraca kwotę słownie
	 * @return string
	 * @author alex
	 */
	public function getAmountInWords() {
		return $this->amountInWords;
	}

	/**
	 * Zwraca numer konta
	 * @return string
	 * @author alex
	 */
	public function getBankAccountNumber() {
		return $this->bankAccountNumber;
	}

	/**
	 * Zwraca informacje nadawcy
	 * @return string
	 * @author alex
	 */
	public function getFromInfo() {
		return $this->fromInfo;
	}

	/**
	 * Zwraca informacje o tytule
	 * @return string
	 * @author alex
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Zwracac informacje o nadawcy
	 * @return string
	 * @author alex
	 */
	public function getToInfo() {
		return $this->toInfo;
	}


}
