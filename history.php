<?php
// History 1 Script
$ip = 'http://www.ebm-radio.org';
$port = 7000;
function get_data($ip = false, $port = false){
    try {
        if($ip && $port) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "{$ip}:{$port}/played.html");
            curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            $data=(object)array(
                'response'  =>  curl_exec($ch),
                'info'      =>  (object)curl_getinfo($ch)
            );
            return $data;
        }
        throw new Exception('Az IP-cím és a portszám megadása kötelező');
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
$data = get_data($ip, $port);
if($data->info->http_code == 200){
    echo print_r($data->response,true);
}
// History 2 Script
$server_ip = "s2.free-shoutcast.com";
$portbase = "18216";
if (!is_numeric($portbase)) {
    echo "Érvénytelen port";
    exit;
}
// Aktuális zeneszám
$fp2 = @fsockopen($server_ip,$portbase,$errno,$errstr,1);
if (!$fp2) { 
    echo "Kapcsolódás elutasítva";
} else { 
    fputs($fp2, "GET /7.html HTTP/1.0\r\nUser-Agent: Mozilla\r\n\r\n");
    while (!feof($fp2)) {
        $info = fgets($fp2);
    }
    $info = str_replace('</body></html>', "", $info);
    $split = explode(',', $info);
    if (empty($split[6])){
        echo "<h3 style='color: red; padding: 6px; border: solid 1px red; border-radius: 4px; background-color: pink; opacity: .8; text-align: center;'>The current song is not available</h3>"; // Diaplays when sever is online but no song title
    } else {
        $title = str_replace('\'', '`', $split[6]);
        $title = str_replace(',', ' ', $title);
        echo "<h3 style='padding: 6px; border: solid 1px #333; border-radius: 4px; background-color: #888; opacity: .8; text-align: center;'>$title</h3>"; // Diaplays song
    }
}
$fp = @fsockopen($server_ip,$portbase,$errno,$errstr,1);
// History
if (!$fp) { 
    echo "<p>A kapcsolat megtagadva, a szerver offline állapotban van.</p>";
    exit;
} else { 
    fputs($fp, "GET /played.html HTTP/1.0\r\nUser-Agent: Mozilla\r\n\r\n");
    while (!feof($fp)) {
        $info = fgets($fp);
        $info = str_replace('HTTP/1.1', "", str_replace('200', "", str_replace('OK', "", str_replace('Content-Type:text/html;charset=utf-8', "", str_replace('Content-Length:', "Tartalom hossza: ", $info)))));
        echo $info;
    }
    $content = get_string_between($info, "Admin Login</a></font></td></tr></table></td></tr></table><br>", "<br><br><table");
    print_r ($content);
    fclose($fp);
}
function get_string_between($string, $start, $end) {
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
      return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
?>
