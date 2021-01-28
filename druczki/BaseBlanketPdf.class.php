<?php
require_once(dirname(__FILE__) . '/vendor/tcpdf/config/lang/eng.php');
require_once(dirname(__FILE__) . '/vendor/tcpdf/tcpdf.php');

/**
 * Podstawa dla blanketow
 * @author alex
 */

abstract class BaseBlanketPdf extends TCPDF {

	/**
	 * Wektor wymiarow
	 * @var float
	 */

	protected $sizeVector = 1.0;


	/**
	 * Wspolrzedna X poczatku rysowania blankietu
	 * @var int
	 */

	protected $startX = 5;

	/**
	 * Wspolrzedna Y poczatku rysownaia blankietu
	 * @var int
	 */

	protected $startY = 5;

	/**
	 * Inicjalizacja/rysowanie
	 * @var bool
	 */

	protected $initialized = false;

	/**
	 * Domyslny rozmiar czcionki
	 * @var int
	 */

	protected $fontDefaultSize = 15;

	/**
	 * Domyslny rozmiar czcionki
	 * @var int
	 */

	protected $fontSmallSize = 10;

	/**
	 * Nazwa czcionki
	 * @var string
	 */

	protected $fontName = 'cour';

	/**
	 * Szybkie generowanie kosztem wielkosci pliku
	 * @var bool
	 */

	protected $optimizeSpeed = true;


	/**
	 * Rysowanie strony
	 * @abstract
	 * @param mixed $data
	 * @return mixed
	 */

	abstract protected function drawPage($data);

	/**
	 * Zwraca dane
	 * @abstract
	 * @return mixed
	 */

	abstract public function getData();

	/**
	 * Rysowanie linii
	 * @abstract
	 * @param string $line
	 * @param int $index
	 * @return mixed
	 */

	abstract protected function drawLine($line, $index);


	/**
	 * Rysowanie danych
	 * @param BankTransferBlanketInfo[] $data
	 */

	protected function initialize($data) {
		if (!$this->initialized) {
			$this->configure();

			foreach ($data as $info) {
				$this->drawPage($info);
			}
			$this->initialized = true;
		}
	}

	/**
	 * Rysuje linię z odpowiednimi hookami
	 * @abstract
	 * @param string $line
	 * @param int $index
	 * @return mixed
	 */

	protected function handleLine($line, $index) {
		if (!$this->drawLinePre($line, $index)) {
			$this->drawLine($line, $index);
		}
		$this->drawLinePost($line, $index);
	}

	/**
	 * Hook przed rozrysowaniem linii
	 * @param string $line
	 * @param int $key
	 * @return bool - czy nie rysowac nastepnej linii
	 */

	protected function drawLinePre($line, $key) {
		return false;
	}

	/**
	 * Hook po rozrysowaniu linii
	 * @param string $line
	 * @param int $key
	 */

	protected function drawLinePost($line, $key) {

	}

	/**
	 * Rysowanie PDF
	 * @param string $name
	 * @param string $dest - cel, gdzie wysłac dokument: I - wysyła plik do przeglądarki (domyślnie), D - plik ściąga się na dysk. F - zapis pliku na lokalnym serwerze, S - zwraca dokumeny jako string, E - zwraca dokument jako emailowy załącznik w kodowaniu base64, Inne dostępne opcje: FI, FD.
	 * @return string
	 */

	public function Output($name = 'doc.pdf', $dest = 'I') {
		$this->initialize($this->getData()); //jak ktos zapomnial
		return parent::Output($name, $dest);
	}


	/** Ustawienie czcionki domyslnej
	 * @return float
	 */
	protected function getFontDefaultSize() {
		return $this->fontDefaultSize * $this->sizeVector;
	}

	/**
	 * Ustawienie czcionki dla kwoty slownie
	 * @return float
	 *
	 */

	protected function getFontSmallSize() {
		return $this->fontSmallSize * $this->sizeVector;
	}


	/**
	 * Ustawienie trybu generowania druczku: true - szybkie generowanie, ale duzy plik; false - maly plik, wolne generowanie
	 * @param $val
	 */

	public function setOptimizeSpeed($val) {
		$this->optimizeSpeed = $val;
	}


	/**
	 * Konfiguracja TCPDF
	 */

	protected function configure() {
		$this->addTTFfont(dirname(__FILE__) . '/fonts/' . $this->fontName . '.ttf', 'TrueTypeUnicode', '', 32);

		// set document information
		$this->SetCreator(PDF_CREATOR);

		// set default header data
		$this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 057', PDF_HEADER_STRING);

		// set header and footer fonts
		$this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// remove default header/footer
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);

		//set margins
		$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$this->SetHeaderMargin(PDF_MARGIN_HEADER);
		$this->SetFooterMargin(PDF_MARGIN_FOOTER);

		//łamanie linii
		$this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//sklowanie zdjęcias
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$this->SetFont($this->fontName, '', $this->getFontDefaultSize());
		$this->SetLineWidth(0);
		$this->SetDrawColor(0, 128, 255);

		//przyspieszenie generowania
		$this->setFontSubsetting(!$this->optimizeSpeed);

		$this->setCellHeightRatio(1);
	}

	/**
	 * Dzieli na 2 linie
	 * @param $lines
	 * @param $str
	 * @param $length
	 * @return array string
	 */

	protected function splitIntoLines($lines, $str, $length) {
		$lines[] = mb_substr($str, 0, $length, 'utf-8');
		$lines[] = mb_substr($str, $length, $length, 'utf-8');

		return $lines;
	}

	/**
	 * Wspolrzedna X od ktorej rysujemy blankiet
	 * @param int $x
	 */

	public function setStartX($x) {
		$this->startX = $x * $this->sizeVector;
	}

	/**
	 * Wspolrzedna Y od której rysujemy blankiet
	 * @param $y
	 */

	public function setStartY($y) {
		$this->startY = $y * $this->sizeVector;
	}


	/**
	 * Dzieli słowo na litery
	 * @param string $str
	 * @param int $l
	 * @return array
	 */

	protected function str_split_unicode($str, $l = 0) {
		if ($l > 0) {
			$ret = array();
			$len = mb_strlen($str, "UTF-8");
			for ($i = 0; $i < $len; $i += $l) {
				$ret[] = mb_substr($str, $i, $l, "UTF-8");
			}
			return $ret;
		}
		return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	}


}
