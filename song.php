<?php
/*
    Ez a lehívható információrészlet elemzésére szolgál
    egy shoutcast szerverről. Az Ön által használt URL felépítése:
    http://IP cím vagy Domain név:port/stats?sid=y 

    Cserélje ki a tartományt a figyelni kívántra. A port a fő
    a szerver portját, az „y” a ténylegesen figyelni kívánt szervert jelenti, 
    ami a legtöbb esetben sid=1. A legújabb inkarnációval
    a ShoutCAST DNAS több szervert is üzemeltethet ugyanazon porton.

    A rendelkezésre álló változók a következők:

        CURRENTLISTENERS - Hallgatók száma ezen az adatfolyamon
        PEAKLISTENERS    - A legtöbb hallgató
        MAXLISTENERS     - A legtöbb hallgató adatfolyamonként / szerverenként
        UNIQUELISTENERS  - Egyedi hallgatókat jelenít meg (egyesek több eszközt is hallgatnak ugyanarról az IP-ről)
        AVERAGETIME      - Átlagos hallgatási idő (másodpercben)
        SERVERGENRE      - Ha be van állítva, ez lesz a stream műfaja
        SERVERURL 	 - Az állomás honlapjának webcíme
        SERVERTITLE      - Ahogy az állomás/folyam el van nevezve
        SONGTITLE	 - Amit a streaming szoftver küld, általában az Előadó - Dal neve kombinációja
        STREAMHITS	 - Hányszor csatlakoztak a hallgatók az adatfolyamhoz annak kezdete óta (összesített)
        STREAMSTATUS	 - 1 ha bekapcsolt, 0 ha kikapcsolt
        BACKUPSTATUS	 - 1 ha bekapcsolt, 0 ha kikapcsolt (azt jelenti, hogy a szervernek van olyan fájlja, amelyet lejátszik, ha nincs csatlakoztatva forrás)
        STREAMPATH	 - Amit a hallgató belehelyez a hangszoftverébe, hogy meghallgassa. Általában ez a következő: http://server:port/path?sid=1
        STREAMUPTIME	 - Az idő (másodpercben), mennyi ideje tart a stream az újraindítás óta.
        BITRATE	         - adatsebesség kb. (azaz a 128 az 128 kb lenne)
        CONTENT		 - MIME típusa adatfolyamhoz (audio/video/stb)
        VERSION		 - Szerver verzió

    A szkriptben található funkciók:
        secs_to_str      - elemzi egy egész számot, és ember által olvasható karaktersorozattá alakítja (azaz óra perc másodperc)
	   					    
	   					    
   =====================  SZERZŐI JOGI KÖZLEMÉNY  ========================
   
   Az eredeti licensz Thomas Kroll ShoutCAST DNAS v2 adatelemző licence a Creative Commons 
   (Oszd meg! Nevezd meg!) 4.0 nemzetközi licenc alapján, melynek linkje már nem érhető el.
   Az újradolgozásért MComp Software felelős a https://www.mcomp.hu/song.php.txt 
   címen található munka alapján.
   
   =====================  CREATIVE COMMON LICENCE  ========================
   A Creative Common licensz magyar verziója a https://mcomp.hu/cc/cc.pdf címről tölthető le.
   Eredeti verzió a https://creativecommons.org/licenses/by-sa/4.0/ címen érhető el.
*/
$debug = true;
/* --BEGIN: functions */
function secs_to_str ($duration) {
    $periods = array(
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    );
    $parts = array();
    foreach ($periods as $name => $dur) {
        $div = floor($duration / $dur);
        if ($div == 0){
            continue;
        } else {
            if ($div == 1) {
                $parts[] = $div . " " . $name;
            } else {
                $parts[] = $div . " " . $name . "s";
            }
        }
        $duration %= $dur;
    }
    $last = array_pop($parts);
    if (empty($parts)) {
        return $last;
    } else {
        return join(', ', $parts) . " and " . $last;
    }
}
function remove_utf8_bom($text) {
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}
function iTunes_get($combine) {
    $json = file_get_contents('https://itunes.apple.com/search?term='.$combine.'&entity=musicTrack&limit=1');
    $obj = remove_utf8_bom($json);
    $obj=preg_replace('/.+?({.+}).+/','$1',$obj);
    $obj=json_decode($obj,TRUE);
    return $obj;
}
function clean($in) {
    $in = preg_replace("/\([^)]+\)/","",$in);  //removed parenthesis
    $in = preg_replace("/\[[^]]+\]/","",$in);  //removed brackets
    return(str_ireplace(' -','',$in));
}
/* --END: functions */
/*  Előadó - Dal - Szervernév lekérése */
$server = "http://www.ebm-radio.org:7000/stats?sid=1";
$srv_url = urlencode($server);
$sc_stats = simplexml_load_file($srv_url);
/* itt talál további információkat az aktuális számról */
$album = iTunes_get(urlencode(clean($sc_stats->SONGTITLE)));
extract($album);
/* a szerver URL által beolvasott xml fájl feldolgozása egy hozzáértőnek innen már gyerekjáték */
    echo "<div id='refresh'>";
    echo '<p>Állomás: <a class="title-hover" target="_blank" title="Weboldal megnyitása" href="'.$sc_stats->SERVERURL.'">'.$sc_stats->SERVERTITLE.'</a><br>'
            . 'Műfaj: <a class="title-hover" title="Google keresés" style="color: #000; text-decoration: none;" target="_blank" href="https://www.google.co.in/search?q=' . preg_replace("/\(([^()]*+|(?R))*\)/", "", $sc_stats->SERVERGENRE) . '">'.$sc_stats->SERVERGENRE.'</a></p>';
    $album_image = !empty($album['results'][0]['artworkUrl100']) ? "<img width=\"100\" src=\"".$album['results'][0]['artworkUrl100']."\" />" : "<b style=\"position: absolute; margin-top: 27px; margin-left: 9px; font-size: 14px; color: #000;\">Nincs borító</b><img width=\"100\" src=\"https://i.pinimg.com/564x/43/8b/e7/438be7b5200258e1653499123b47646e.jpg\" />";
    echo "<table border=0>";
    if (isset($album['results'][0])) {
        echo "<tr><td><table border=\"1\" padding=\"1\"><tr><td>".$album_image."</td><td class=\"data-pos\"><span style=\"left: 6px; margin-right: 10px; top: 10px;\"><a class=\"title-hover\" title=\"Keresés a YouTube - on\" style=\"color: #000; text-decoration: none;\" target=\"_blank\" href=\"https://www.youtube.com/results?search_query=" . preg_replace("/\(([^()]*+|(?R))*\)/", "", $sc_stats->SONGTITLE) . "\">".$sc_stats->SONGTITLE."</a></span><br />".$album['results'][0]['collectionName']."<br />".date("Y",strtotime($album['results'][0]['releaseDate']))  . " (" . $album['results'][0]['country'] .")<br>" . $album['results'][0]['primaryGenreName'] . "</table></td></tr>";
        echo "<tr><td>Előnézet: <a href=\"" . $album['results'][0]["previewUrl"] . "\" target=\"_blank\">" . $album['results'][0]["previewUrl"] . "</a></td></tr>";
        echo "<tr><td>Kiadási dátum: " . str_replace("T", " T", $album['results'][0]["releaseDate"]) . "</td></tr>";  
    } else {
        echo "<tr><td><table border=\"1\" padding=\"1\"><tr><td><img width=\"100\" src=\"https://i.pinimg.com/564x/43/8b/e7/438be7b5200258e1653499123b47646e.jpg\" /></td><td class=\"data-pos\"><span style=\"position: relative; left: 6px; margin-right: 10px; top: 10px;\"><a class=\"title-hover\" title=\"Keresés a YouTube - on\" target=\"_blank\" href=\"https://www.youtube.com/results?search_query=" . preg_replace("/\(([^()]*+|(?R))*\)/", "", $sc_stats->SONGTITLE) . "\">".$sc_stats->SONGTITLE."</a></span><br /><br /></table></td></tr>";
    }
    echo "</table><br />";
    echo "<title>$sc_stats->SONGTITLE</title>";
    if($debug) {
        echo "<hr><br />SHOUTcast Verzió: $sc_stats->VERSION<br/>";
        echo "Tisztított cím: ".clean($sc_stats->SONGTITLE)."<br />";
        echo "<br /><br /><b>Állomás adatok</b>";
        echo "<br>";
        echo "Hallgatók: ".$sc_stats->CURRENTLISTENERS." of ".$sc_stats->MAXLISTENERS." [Peak: ".$sc_stats->PEAKLISTENERS."]";
        echo "<br>";
        echo "Üzemidő (az utolsó indítás óta): ". secs_to_str($sc_stats->STREAMUPTIME);
        echo "<br>";
    }
    // print_r($album) . "<br>";
    $country = !empty($album['results'][0]['country']) ? $album['results'][0]['country'] : "Nem lokalizálható!";
    echo "Ország: ".$country;
    echo "<br><hr>";
    // Kérjük, ne törölje ki ezt a sort a fent leírt szerzői jogi okokból. Köszönöm!
    echo "<div style=\"width: 100%; text-align: center;\"><a target=\"_blank\" href=\"http://creativecommons.org/licenses/by-sa/4.0/\"><img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"https://i.creativecommons.org/l/by-sa/4.0/88x31.png\" /></a><br /><span xmlns:dct=\"http://purl.org/dc/terms/\" href=\"http://purl.org/dc/dcmitype/Text\" property=\"dct:title\" rel=\"dct:type\">Az <a target=\"_blank\" href=\"http://www.mcomp.hu/\">MComp Software</a> a ShoutCAST DNAS v2 adatelemzőjének licence a</span> <a target=\"_blank\" href=\"http://creativecommons.org/licenses/by-sa/4.0/\">Creative Commons Attribution-ShareAlike 4.0 International</a> licensz alá tartozik.<br />A <a target=\"_blank\" href=\"http://www.mcomp.hu/song.php.txt\">http://www.mcomp.hu/song.php.txt</a> címen található példa alapján</div>";
    echo "</div><br>";
?>

