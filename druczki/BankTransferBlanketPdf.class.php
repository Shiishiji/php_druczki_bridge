<?php
require_once(dirname(__FILE__) . '/vendor/createIT/PolishNumberWriter.class.php');
require_once('BaseBlanketPdf.class.php');

/**
 * Klasa pozwalająca na łatwe i szybkie tworzenie blankietów z przelewami
 * @author alex
 */

class BankTransferBlanketPdf extends BaseBlanketPdf {

	/**
	 * Szerokość komórki
	 * @var float
	 */

	protected $cellWidth = 5.01;

	/**
	 * Wysokość komórki
	 * @var float
	 */

	protected $cellHeight = 7.00;

	/**
	 * Ile mamy okienek w standardowej linii
	 * @var int
	 */

	protected $lineCells = 27;


	/**
	 * Ile maksymalnie upchamy literek w linii
	 * powyzej tej liczby zmniejszamy czcionke
	 * @var int
	 */

	protected $maxLineCells = 55;


	/**
	 * Ile mamy liter w dlugiej linii
	 * @var int
	 */

	protected $lineLongCells = 60;

	/**
	 * Odleglosc miedzy blankietami
	 * @var int
	 */

	protected $blanketMargin = 10;

	/**
	 * Dane
	 * @var BankTransferBlanketInfo[]
	 */

	protected $data;

	/**
	 * Szerokosc blankietu w px
	 * @var int
	 */

	protected $blanketSize = 164;

	/**
	 * Szerokosc blankietu w px
	 * @var int
	 */

	protected $blanketHeight = 110;

	/**
	 * Ilosc kopii blankietow
	 * @var int
	 */
	protected $numberOfCopies = 2;


	/**
	 * Czy rysujemy logo w stopce?
	 * @var bool
	 */
	protected $drawLogo = true;


	/**
	 * Tworzy obiekt
	 * @param BankTransferBlanketInfo[] $data
	 */

	public function __construct($data) {
		parent::__construct();
		$this->data = $data;

	}

	/**
	 * Czy rysujemy logo?
	 * @see images/logo.png
	 * @param bool $val
	 */

	public function setDrawLogo($val) {
		$this->drawLogo = $val;
	}

	/**
	 * Umozliwia ustawienie odleglosci miedzy blankietami
	 * @param $margin
	 * @internal param $int $
	 */
	public function setBlanketMargin($margin) {
		$this->blanketMargin = $margin;
	}

	/**
	 * Zwraca wysokosc blankietu z marginesem dolnym
	 * @return int
	 */

	public function getBlanketHeightWithMargin() {
		// return $this->getImageRBY() + $this->blanketMargin;
		return $this->blanketHeight + $this->blanketMargin;
	}


	/**
	 * Ustawianie wysokosci komorki
	 * @return float
	 */
	protected function getCellHeight() {
		return $this->cellHeight * $this->sizeVector;
	}


	/**
	 * Wczytanie rozmiaru blankietu
	 * @return float
	 */
	protected function getBlanketSize() {
		return $this->blanketSize * $this->sizeVector;
	}

	/**
	 * Obliczanie szerokosci komorki
	 * @param string
	 * @return float
	 */

	protected function getCellWidth($line) {
		$lineLength = mb_strlen($line, 'utf-8');
		// var_dump($lineLength);

		if ($lineLength > $this->lineCells) {
			/* obliczanie rozmiaru komorki */
			return (($this->lineCells * $this->cellWidth * $this->sizeVector) / ($lineLength));
		}
		return $this->cellWidth * $this->sizeVector;
	}

	/**
	 * Rysuje logo w stopce strony
	 */
	protected function drawLogo() {
		if ($this->drawLogo) {
			$this->SetXY(160, 260);
			$this->Image(dirname(__FILE__) . '/images/logo.png', '', '', 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
		}
	}

	/**
	 * @param string
	 * @return int
	 * Oblicza dlugosc linii w blankiecie, dla standardowej
	 * linii to 27, a dla dluzszej to dlugosc zmiennej / 2
	 */

	protected function getNumberOfCells($txt) {
		$txtLength = mb_strlen($txt, 'utf-8');
		if ($txtLength > 2 * $this->lineCells) {
			return $txtLength / 2 + 1;
		}

		return $this->lineCells;

	}

	/**
	 * Rysowanie strony
	 * @param BankTransferBlanketInfo $info
	 * @return mixed|void
	 */

	protected function drawPage($info) {
		$this->AddPage();
		$definition = $this->getLinePositions();

		$lines = array();
		$lines = $this->splitIntoLines($lines, $info->getToInfo(), $this->getNumberOfCells($info->getToInfo()));
		$lines[] = $info->getBankAccountNumber();
		$lines[] = str_replace('.', ',', $info->getAmount());

		$lines = $this->splitIntoLines($lines, PolishNumberWriter::build($info->getAmount()), $this->lineLongCells);

		$lines = $this->splitIntoLines($lines, $info->getFromInfo(), $this->getNumberOfCells($info->getFromInfo()));
		$lines = $this->splitIntoLines($lines, $info->getTitle(), $this->getNumberOfCells($info->getTitle()));

		$margin = 0;
		for ($x = 0; $x < $this->numberOfCopies; $x++) {

			$this->SetXY($this->startX, $this->startY + $margin);
			$this->Image($this->getBlanketImage(), '', '', $this->getBlanketSize(), '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);

			foreach ($definition as $key => $coordinates) {
				$this->SetXY($coordinates[0] + $this->startX, $coordinates[1] + $this->startY + $margin);

				$line = $lines[$key];

				//@see drawLine
				$this->handleLine($line, $key);
			}
			$margin = $margin + $this->getBlanketHeightWithMargin(); //dla kolejnego blankietu przenosimy nizej
		}

		$this->drawLogo();

	}

	/**
	 * Zwraca zdjęcie blanketu
	 * @return string
	 */

	protected function getBlanketImage() {
		return dirname(__FILE__) . '/images/transfer.jpg';
	}

	/**
	 * Zwraca numer linii (x,y)
	 * @return array
	 */

	protected function getLinePositions() {

		return array(
			array(21.1 * $this->sizeVector, 4.9 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 13.1 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 21.5 * $this->sizeVector),
			array(96.5 * $this->sizeVector, 29.9 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 37.8 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 40.8 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 47.1 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 56.0 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 64.1 * $this->sizeVector),
			array(21.1 * $this->sizeVector, 72.6 * $this->sizeVector),
		);
	}

	/**
	 * Hook przed rozrysowaniem linii
	 * @param string $line
	 * @param int $key
	 * @return bool - czy nie rysowac nastepnej linii
	 */

	protected function drawLinePre($line, $key) {
		if (($key == 4) || ($key == 5)) { //slownie - w tym przypadku zmieniamy wyglad
			$this->SetFont($this->fontName, '', $this->getFontSmallSize());
			$this->MultiCell(145 * $this->sizeVector, $this->cellHeight, $line, 0, 'L');
			$this->setFont($this->fontName, '', $this->getFontDefaultSize());
			return true;
		}
		if (mb_strlen($line, 'utf-8') > $this->maxLineCells) {
			$this->SetFont($this->fontName, '', $this->getFontSmallSize());
		} else {
			$this->setFont($this->fontName, '', $this->getFontDefaultSize());
		}

		return false;
	}

	/**
	 * Zwraca dane
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Rysowanie linii
	 * @param string $line
	 * @param int $index
	 * @return mixed
	 */
	protected function drawLine($line, $index) {
		$cellWidth = $this->getCellWidth($line);
		foreach ($this->str_split_unicode($line) as $char) {
			$this->Cell($cellWidth, $this->getCellHeight(), $char, 0, $ln = 0, 'C', 0, '', 0, false, 'T', 'T');
		}
	}
}
