<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 *
 * The TYPOlight webCMS is an accessible web content management system that 
 * specializes in accessibility and generates W3C-compliant HTML code. It 
 * provides a wide range of functionality to develop professional websites 
 * including a built-in search engine, form generator, file and user manager, 
 * CSS engine, multi-language support and many more. For more information and 
 * additional TYPOlight applications like the TYPOlight MVC Framework please 
 * visit the project website http://www.typolight.org.
 *
 * PHP version 5
 * @copyright  Janoš Guljaš 2008
 * @author     Janoš Guljaš <newrennaissance@gmail.com>
 * @package    
 * @license    GPL
 * @filesource
 */

$GLOBALS['TL_LANG']['ERR']['captcha'] = 'Iz bezbjednosnih razloga, odgovorite na pitanje!';
$GLOBALS['TL_LANG']['SEC']['question1'] = 'Saberite %d i %d.';
$GLOBALS['TL_LANG']['SEC']['question2'] = 'Koji je zbir brojeva %d i %d?';
$GLOBALS['TL_LANG']['SEC']['question3'] = 'Izračunajte %d plus %s.';
$GLOBALS['TL_LANG']['CTE']['texts'] = 'Elementi teksta';
$GLOBALS['TL_LANG']['CTE']['headline'] = array('Naslov', 'Ovaj element sadrži naslov, koji je formatiran sa <H1> tagovima.');
$GLOBALS['TL_LANG']['CTE']['text'] = array('Tekst', 'Ovaj element sadrži formatiran tekst sa linkovima. Mogu se dodati i slike.');
$GLOBALS['TL_LANG']['CTE']['html'] = array('HTML', 'Ovaj element sadrži HTML kod. Određeni HTML tagovi su zabranjeni.');
$GLOBALS['TL_LANG']['CTE']['list'] = array('Lista', 'Ovaj element sadrži sortiranu ili nesortiranu listu.');
$GLOBALS['TL_LANG']['CTE']['table'] = array('Tabela', 'Ovaj element sadrži tabelu.');
$GLOBALS['TL_LANG']['CTE']['accordion'] = array('Accordion', 'Ovaj element omogućava moofx accordion. Mootools JavaScript šablon mora biti uključen u modulu za izgled strane.');
$GLOBALS['TL_LANG']['CTE']['code'] = array('Kod', 'Ovaj element sadrži kod koji će biti prikazan na strani. Kod će samo biti prikazan, a ne izvršen.');
$GLOBALS['TL_LANG']['CTE']['links'] = 'Elementi za linkove';
$GLOBALS['TL_LANG']['CTE']['hyperlink'] = array('Link ka sajtu', 'Ovaj element sadrži link ka nekom drugom sajtu.');
$GLOBALS['TL_LANG']['CTE']['toplink'] = array('Vrh strane', 'Ovaj element sadrži link ka vrhu trenutne strane.');
$GLOBALS['TL_LANG']['CTE']['images'] = 'Element i za slike';
$GLOBALS['TL_LANG']['CTE']['image'] = array('Slika', 'Ovaj element sadrži jednu sliku.');
$GLOBALS['TL_LANG']['CTE']['gallery'] = array('Galerija slika', 'Ovaj element sadrži umanjene slike iz vaše galerije.');
$GLOBALS['TL_LANG']['CTE']['files'] = 'Elementi za fajlove';
$GLOBALS['TL_LANG']['CTE']['download'] = array('Preuzimanje fajla', 'Ovaj element sadrži link ka fajlu koji može biti preuzet od strane posjetilaca sajta.');
$GLOBALS['TL_LANG']['CTE']['downloads'] = array('Fajlovi za preuzimanje', 'Ovaj element sadrži fajlove koji mogu biti preuzeti sa sajta.');
$GLOBALS['TL_LANG']['CTE']['includes'] = 'Dodaj elemente';
$GLOBALS['TL_LANG']['CTE']['alias'] = array('Alias', 'Ovaj element Vam omogućava da prikažete određen postojeći sadržaj u više članaka.');
$GLOBALS['TL_LANG']['CTE']['teaser'] = array('Uvodni tekst', 'Ovaj element omogućava prikaz uvodnog teksta članka.');
$GLOBALS['TL_LANG']['CTE']['form'] = array('Formular', 'Koristite ovu opciju da biste dodali formular u članak.');
$GLOBALS['TL_LANG']['CTE']['module'] = array('Modul', 'Koristite ovu opciju da biste dodali modul (na primjer, navigacioni meni ili Flash) u članak.');
$GLOBALS['TL_LANG']['MSC']['go'] = 'Idi';
$GLOBALS['TL_LANG']['MSC']['quicknav'] = 'Navigacija';
$GLOBALS['TL_LANG']['MSC']['quicklink'] = 'Link';
$GLOBALS['TL_LANG']['MSC']['username'] = 'Korisničko ime';
$GLOBALS['TL_LANG']['MSC']['login'] = 'Prijava';
$GLOBALS['TL_LANG']['MSC']['logout'] = 'Odjava';
$GLOBALS['TL_LANG']['MSC']['loggedInAs'] = 'Prijavljeni ste kao %s.';
$GLOBALS['TL_LANG']['MSC']['emptyField'] = 'Unesite korisničko ime i šifru!';
$GLOBALS['TL_LANG']['MSC']['confirmation'] = 'Potvrda';
$GLOBALS['TL_LANG']['MSC']['sMatches'] = '%s pojavljivanja za %s';
$GLOBALS['TL_LANG']['MSC']['sEmpty'] = 'Nije nađeno <strong>%s</strong>';
$GLOBALS['TL_LANG']['MSC']['sResults'] = 'Rezultati %s - %s od %s za <strong>%s</strong>';
$GLOBALS['TL_LANG']['MSC']['sNoResult'] = 'Pretraga <strong>%s</strong> nema rezultata.';
$GLOBALS['TL_LANG']['MSC']['seconds'] = 'sekunde';
$GLOBALS['TL_LANG']['MSC']['first'] = '« Prva';
$GLOBALS['TL_LANG']['MSC']['previous'] = 'Prethodna';
$GLOBALS['TL_LANG']['MSC']['next'] = 'Sljedeća';
$GLOBALS['TL_LANG']['MSC']['last'] = 'Poslednja »';
$GLOBALS['TL_LANG']['MSC']['goToPage'] = 'Idite na stanu %s';
$GLOBALS['TL_LANG']['MSC']['totalPages'] = 'Strana %s od %s';
$GLOBALS['TL_LANG']['MSC']['fileUploaded'] = 'Fajl %s je uspešno postavljen';
$GLOBALS['TL_LANG']['MSC']['searchLabel'] = 'Pretraga';
$GLOBALS['TL_LANG']['MSC']['matchAll'] = 'sve riječi';
$GLOBALS['TL_LANG']['MSC']['matchAny'] = 'bilo koja riječ';
$GLOBALS['TL_LANG']['MSC']['saveData'] = 'Sačuvaj';
$GLOBALS['TL_LANG']['MSC']['printAsPdf'] = 'Print u PDF';
$GLOBALS['TL_LANG']['MSC']['pleaseWait'] = 'Sačekajte trenutak';
$GLOBALS['TL_LANG']['MSC']['loading'] = 'Učitavanje...';
$GLOBALS['TL_LANG']['MSC']['more'] = 'Detaljnije...';
$GLOBALS['TL_LANG']['MSC']['com_name'] = 'Ime';
$GLOBALS['TL_LANG']['MSC']['com_email'] = 'E-mail (nije objavljen)';
$GLOBALS['TL_LANG']['MSC']['com_website'] = 'Sajt';
$GLOBALS['TL_LANG']['MSC']['com_submit'] = 'Dodaj';
$GLOBALS['TL_LANG']['MSC']['comment_by'] = 'Komentar od';
$GLOBALS['TL_LANG']['MSC']['com_quote'] = '%s je napisao:';
$GLOBALS['TL_LANG']['MSC']['com_code'] = 'Kod:';
$GLOBALS['TL_LANG']['MSC']['com_subject'] = 'TYPOlight :: Novi komentar u %s';
$GLOBALS['TL_LANG']['MSC']['com_message'] = 'Na vašem sajtu je dodat komentar.%s Ako moderirate komentare, morate se logovati da biste ga aktivirali.';

?>
