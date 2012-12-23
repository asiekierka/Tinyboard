<?php
 /*	libpaski - biblioteka do rysowania ladnych paskow
	Copyright (c) 2012, Adrian Siekierka
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	1. Redistributions of source code must retain the above copyright
	   notice, this list of conditions and the following disclaimer.
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	3. Neither the name of the author nor the names of any contributors may
	   be used to endorse or promote products derived from this software without
	   specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY ADRIAN SIEKIERKA ''AS IS'' AND ANY
	EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
	DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
											*/
// Tu dodawacz nowe czcionki. Nazwa-wysokosc => array(wysokosc, format, polozenie, offset_y),
// Nazwa-any oznacza czcionke o dowolnej wysokosci.
$lp_czcionki = array(
	"GohuFont-11" => array(11, "ttf","fonts/Gohu11.ttf",-1),
	"GohuFontBold-11" => array(11, "ttf","fonts/Gohu11B.ttf",-1)
);

function lp_znajdzCzcionke($czcionka,$h) {
	global $lp_czcionki;
	$dane_czcionki = FALSE;
	$dopasowanie = 10000;
	$wys_docelowa = "zadna";
	if(count(explode("-",$czcionka))>1) $wys_docelowa = explode("-",$czcionka)[1];
	foreach($lp_czcionki as $nazwa => $dane) {
		if($nazwa == $czcionka) {
			$dane_czcionki = $dane;
			if($wys_docelowa == "any") $dane_czcionki[0] = $h;
			break;
		}
		$temp = explode("-",$nazwa);
		if(count($temp)<2) continue;
		if($temp[0] != $czcionka) continue; // To nie ta czcionka, kontynuuj.		
		$wys = intval($temp[1]);
		if($wys<3) continue; // Czcionki ponizej 3px nie istnieja i istniec nie beda. Poza tym sa nieczytelne.
		if(($h-$wys)<$dopasowanie && ($h-$wys)>0) { $dane_czcionki = $dane; $dopasowanie = ($h-$wys); }
	}
	return $dane_czcionki;
}

function lp_dlugoscTekstu($dane_czcionki, $tekst) {
	$px_czcionki = $dane_czcionki[0]; $pt_czcionki = ($px_czcionki*3)/4;
	if($dane_czcionki[1] == "ttf") {
		$bbox = imagettfbbox($pt_czcionki,0,$dane_czcionki[2],$tekst);
	}
	return abs($bbox[2] - $bbox[0]);
}

// Funkcja drawPasek daje obrazek GD w razie powodzenia, a FALSE w razie porazki.
function drawPasek($w, $h, $ulamek, $czcionka, $kolor = 0xFF0000, $kolor_tla = 0x777777, $kolor_tekstu = 0xFFFFFF) {
	// Przygotuj dane
	$dane_czcionki = lp_znajdzCzcionke($czcionka,$h); // Czcionka
	$px_czcionki = $dane_czcionki[0]; $pt_czcionki = ($px_czcionki*3)/4;
	$img = imagecreatetruecolor($w,$h); // Obrazek
	$zw = $ulamek*$w;
	$zproc = strval($ulamek*100) . "%";
	// Narysuj
	imagefilledrectangle($img,0,0,$w,$h,$kolor_tla);
	imagefilledrectangle($img,0,0,$zw,$h,$kolor);
	// Tekst
	$zpw = lp_dlugoscTekstu($dane_czcionki,$zproc);
	$zpx = ($zw-1)-$zpw;
	$zpy = $px_czcionki+$dane_czcionki[3];
	if($zpx<1) $zpx = 1; if($zpy<1) $zpy = 1;
	if($dane_czcionki[1] == "ttf") {
		imagettftext($img,$pt_czcionki,0,$zpx,$zpy,$kolor_tekstu,$dane_czcionki[2],$zproc);
	}
	return $img;
}

//imagepng(drawPasek(200,13,0.3,"GohuFont-11"),"t.png");

?>
