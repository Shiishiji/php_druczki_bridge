<?php
require_once 'BankTransferBlanketInfo.class.php';
require_once 'BankTransferBlanketPdf.class.php';

$info = array(

	//informacja na temat pierwszego druczku
	new BankTransferBlanketInfo(
		'Nazwa odbiorcy',
		'Nr rachunku odbiorcy',
		'Kwota',
		'Nazwa zleceniodawcy',
		'Tytułem'
	),

	//informacja na temat drugiego druczku
	new BankTransferBlanketInfo(
		'Adrian Kowalski ul.Narodów Zjednoczonych 342/343 mmm 342 00-033',
		'43121200003203878643549098',
		'8912.99',
		'Barbara Barózcz ul.Długa5556 2332 99-009 Kraków',
		'Zapłata za usługę nr 123123 zamówienia nr 3214óź/2012'
	),
);

//wysłanie do przeglądarki lub zapis na dysk
$blanket = new BankTransferBlanketPdf($info);

//wyswietlamy logo (lub false aby schowac)
$blanket->setDrawLogo(true);

//cel, gdzie wysłac dokument: I - wysyła plik do przeglądarki (domyślnie),
//D - plik ściąga się na dysk. F - zapis pliku na lokalnym serwerze,
//S - zwraca dokumeny jako string, E - zwraca dokument jako emailowy załącznik w kodowaniu base64, Inne dostępne opcje: FI, FD.
$blanket->Output();